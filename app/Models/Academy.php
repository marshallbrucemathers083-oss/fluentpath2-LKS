<?php
// app/Models/Academy.php - Independent Practice Sessions, Review Zone, and Quiz Evaluator

require_once dirname(__DIR__) . '/Database.php';
require_once dirname(__DIR__) . '/Services/XpService.php';
require_once __DIR__ . '/Achievement.php';

class Academy {
    /**
     * Fetch practice questions based on filters or Review Zone
     */
    public static function getQuestions(array $filters, int $limit = 10, ?int $userId = null): array {
        $pdo = Database::getConnection();

        $params = [];
        $whereClauses = [];

        // Check if Review Zone is requested
        if (!empty($filters['mode']) && $filters['mode'] === 'review_zone') {
            if (!$userId) {
                return [];
            }
            // Fetch questions where user's latest attempt was incorrect
            $sql = "
                SELECT c.*
                FROM challenges c
                JOIN (
                    SELECT challenge_id, MAX(id) AS max_attempt_id
                    FROM challenge_attempts
                    WHERE user_id = :uid
                    GROUP BY challenge_id
                ) latest ON c.id = latest.challenge_id
                JOIN challenge_attempts ca ON latest.max_attempt_id = ca.id
                WHERE ca.is_correct = 0
                ORDER BY ca.attempted_at DESC
                LIMIT " . (int)$limit;

            $stmt = $pdo->prepare($sql);
            $stmt->execute(['uid' => $userId]);
            $questions = $stmt->fetchAll();
        } else {
            // Standard Academy filter
            if (!empty($filters['skill']) && $filters['skill'] !== 'Mixed') {
                $whereClauses[] = 'c.skill = :skill';
                $params['skill'] = $filters['skill'];
            }

            if (!empty($filters['level'])) {
                $whereClauses[] = 'c.level = :level';
                $params['level'] = $filters['level'];
            }

            if (!empty($filters['difficulty'])) {
                $whereClauses[] = 'c.difficulty = :difficulty';
                $params['difficulty'] = $filters['difficulty'];
            }

            if (!empty($filters['search'])) {
                $whereClauses[] = '(c.topic LIKE :topic_search OR c.prompt LIKE :prompt_search)';
                $term = '%' . trim((string)$filters['search']) . '%';
                $params['topic_search'] = $term;
                $params['prompt_search'] = $term;
            }

            $whereSql = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

            $sql = "
                SELECT c.*
                FROM challenges c
                {$whereSql}
                ORDER BY RAND()
                LIMIT " . (int)$limit;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $questions = $stmt->fetchAll();
        }

        // Attach options for multiple choice / reading / listening
        foreach ($questions as &$q) {
            $oStmt = $pdo->prepare('SELECT id, option_key, option_text FROM challenge_options WHERE challenge_id = :cid ORDER BY option_key ASC');
            $oStmt->execute(['cid' => $q['id']]);
            $q['options'] = $oStmt->fetchAll();
        }
        unset($q);

        return $questions;
    }

    /**
     * Grade an Academy practice session submitted by user
     */
    public static function submitSession(int $userId, array $answers, array $allowedIds = []): array {
        $pdo = Database::getConnection();

        $allowed = array_fill_keys(array_map('intval', $allowedIds), true);
        $answers = array_intersect_key($answers, $allowed);
        $totalQuestions = count($answers);
        if ($totalQuestions === 0) {
            return ['success' => false, 'error' => 'Tidak ada jawaban yang dikirimkan.'];
        }

        $correctCount = 0;
        $results = [];
        $weakTopics = [];

        foreach ($answers as $challengeId => $submittedAnswer) {
            $challengeId = (int)$challengeId;
            $submittedClean = trim((string)$submittedAnswer);

            $stmt = $pdo->prepare('SELECT * FROM challenges WHERE id = :id LIMIT 1');
            $stmt->execute(['id' => $challengeId]);
            $ch = $stmt->fetch();

            if (!$ch) {
                continue;
            }

            $oStmt = $pdo->prepare('SELECT option_key, option_text FROM challenge_options WHERE challenge_id = :cid AND is_correct = 1');
            $oStmt->execute(['cid' => $challengeId]);
            $options = $oStmt->fetchAll();

            $isCorrect = false;
            foreach ($options as $opt) {
                    if (strcasecmp($submittedClean, $opt['option_key']) === 0 ||
                        strcasecmp($submittedClean, $opt['option_text']) === 0) {
                        $isCorrect = true;
                        break;
                    }
            }
            if (!$isCorrect && strcasecmp($submittedClean, trim($ch['correct_answer'])) === 0) {
                $isCorrect = true;
            }

            if ($isCorrect) {
                $correctCount++;
            } else {
                $topicName = $ch['topic'] ?: $ch['skill'];
                $weakTopics[$topicName] = ($weakTopics[$topicName] ?? 0) + 1;
            }

            // Record each attempt
            $att = $pdo->prepare("
                INSERT INTO challenge_attempts (user_id, challenge_id, submitted_answer, is_correct, score, xp_awarded, attempted_at)
                VALUES (:uid, :cid, :ans, :correct, :score, 0, NOW())
            ");
            $att->execute([
                'uid'     => $userId,
                'cid'     => $challengeId,
                'ans'     => $submittedClean,
                'correct' => $isCorrect ? 1 : 0,
                'score'   => $isCorrect ? 100 : 0,
            ]);

            $results[] = [
                'challenge_id'     => $challengeId,
                'prompt'           => $ch['prompt'],
                'skill'            => $ch['skill'],
                'topic'            => $ch['topic'],
                'submitted_answer' => $submittedClean,
                'correct_answer'   => $ch['correct_answer'],
                'is_correct'       => $isCorrect,
                'explanation'      => $ch['explanation'],
            ];
        }

        $accuracy = ($totalQuestions > 0) ? (int)round(($correctCount / $totalQuestions) * 100) : 0;

        // Award practice XP (proportional to accuracy, e.g. up to 30 XP)
        $xpEarned = (int)round(($accuracy / 100) * 30);
        if ($xpEarned > 0) {
            $eventKey = 'acad_session_' . bin2hex(random_bytes(16)) . "_u{$userId}";
            XpService::awardXp($userId, $xpEarned, 'academy_practice', null, $eventKey, "Sesi Latihan Academy ({$accuracy}% Akurasi)");
        }

        // Check 100% accuracy achievement
        if ($accuracy === 100 && $totalQuestions >= 3) {
            Achievement::unlock($userId, 'accuracy_master');
        }

        Achievement::evaluateAll($userId);

        arsort($weakTopics);

        return [
            'success'         => true,
            'total_questions' => $totalQuestions,
            'correct_count'   => $correctCount,
            'accuracy'        => $accuracy,
            'xp_earned'       => $xpEarned,
            'weak_topics'     => array_keys($weakTopics),
            'results'         => $results,
        ];
    }
}

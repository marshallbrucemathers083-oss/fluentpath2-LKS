<?php
// app/Models/Exam.php - TOEFL-Style Unofficial Objective Practice Test Engine

require_once dirname(__DIR__) . '/Database.php';
require_once dirname(__DIR__) . '/Services/XpService.php';
require_once __DIR__ . '/Achievement.php';

class Exam {
    /**
     * Get TOEFL-style exam questions structured into sections:
     * - Structure & Written Expression (Grammar)
     * - Reading Comprehension
     * - Listening Comprehension
     */
    public static function getExamQuestions(): array {
        $pdo = Database::getConnection();

        // Get dedicated exam questions (IDs >= 200) or high-level challenges
        $sql = "
            SELECT c.* 
            FROM challenges c
            WHERE c.id >= 200 OR c.topic LIKE '%TOEFL%' OR c.level IN ('B2', 'C1', 'C2')
            ORDER BY 
                CASE c.skill 
                    WHEN 'Grammar' THEN 1 
                    WHEN 'Listening' THEN 2 
                    WHEN 'Reading' THEN 3 
                    ELSE 4 
                END,
                c.id ASC
            LIMIT 12
        ";
        $questions = $pdo->query($sql)->fetchAll();

        foreach ($questions as &$q) {
            $oStmt = $pdo->prepare('SELECT id, option_key, option_text FROM challenge_options WHERE challenge_id = :cid ORDER BY option_key ASC');
            $oStmt->execute(['cid' => $q['id']]);
            $q['options'] = $oStmt->fetchAll();
        }
        unset($q);

        return $questions;
    }

    /**
     * Submit and grade exam attempt
     */
    public static function submitExam(int $userId, array $submittedAnswers, int $durationSeconds, array $allowedIds = []): array {
        $pdo = Database::getConnection();
        $allowedIds = array_values(array_unique(array_filter(array_map('intval', $allowedIds), static fn($id) => $id > 0)));
        if (!$allowedIds) return ['success'=>false, 'error'=>'Sesi ujian tidak ditemukan. Mulai sesi baru sebelum mengirim jawaban.'];
        $submittedAnswers = array_intersect_key($submittedAnswers, array_fill_keys($allowedIds, true));
        $durationSeconds = max(0, min($durationSeconds, 7200));

        $examTitle = 'Simulasi Tes Objektif TOEFL (Unofficial Practice)';
        $readingCorrect = 0;
        $readingTotal = 0;
        $listeningCorrect = 0;
        $listeningTotal = 0;
        $structureCorrect = 0;
        $structureTotal = 0;

        $detailedReview = [];

        // Grade every question shown for this session. Omitted inputs count as
        // unanswered and cannot inflate the result.
        foreach ($allowedIds as $challengeId) {
            $cid = (int)$challengeId;
            $submittedClean = trim((string)($submittedAnswers[$cid] ?? ''));

            $stmt = $pdo->prepare('SELECT * FROM challenges WHERE id = :id LIMIT 1');
            $stmt->execute(['id' => $cid]);
            $ch = $stmt->fetch();
            if (!$ch) {
                continue;
            }

            $oStmt = $pdo->prepare('SELECT option_key, option_text FROM challenge_options WHERE challenge_id = :cid AND is_correct = 1');
            $oStmt->execute(['cid' => $cid]);
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

            $skill = $ch['skill'];
            if ($skill === 'Reading') {
                $readingTotal++;
                if ($isCorrect) $readingCorrect++;
            } elseif ($skill === 'Listening') {
                $listeningTotal++;
                if ($isCorrect) $listeningCorrect++;
            } else { // Grammar / Structure
                $structureTotal++;
                if ($isCorrect) $structureCorrect++;
            }

            $detailedReview[] = [
                'challenge_id'     => $cid,
                'skill'            => $skill,
                'prompt'           => $ch['prompt'],
                'submitted_answer' => $submittedClean,
                'correct_answer'   => $ch['correct_answer'],
                'is_correct'       => $isCorrect,
                'explanation'      => $ch['explanation'],
            ];
        }

        $totalQuestions = $readingTotal + $listeningTotal + $structureTotal;
        $totalCorrect = $readingCorrect + $listeningCorrect + $structureCorrect;
        if ($totalQuestions === 0) return ['success'=>false, 'error'=>'Soal pada sesi ini tidak lagi tersedia. Mulai sesi baru.'];

        $readingScore = $readingTotal > 0 ? (int)round(($readingCorrect / $readingTotal) * 30) : 0;
        $listeningScore = $listeningTotal > 0 ? (int)round(($listeningCorrect / $listeningTotal) * 35) : 0;
        $structureScore = $structureTotal > 0 ? (int)round(($structureCorrect / $structureTotal) * 35) : 0;
        $totalScore = $readingScore + $listeningScore + $structureScore;

        // Record attempt in exam_attempts
        $stmt = $pdo->prepare("
            INSERT INTO exam_attempts 
                (user_id, exam_title, reading_score, listening_score, structure_score, total_score, max_score, duration_seconds, answers_json, completed_at)
            VALUES 
                (:uid, :title, :r_score, :l_score, :s_score, :tot_score, 100, :dur, :json, NOW())
        ");
        $stmt->execute([
            'uid'       => $userId,
            'title'     => $examTitle,
            'r_score'   => $readingScore,
            'l_score'   => $listeningScore,
            's_score'   => $structureScore,
            'tot_score' => $totalScore,
            'dur'       => $durationSeconds,
            'json'      => json_encode($detailedReview),
        ]);
        $attemptId = (int)$pdo->lastInsertId();

        // Award exam XP (e.g. 60 XP for completing exam)
        $eventKey = "exam_attempt_{$attemptId}_u{$userId}";
        $xpAwarded = 0;
        if (XpService::awardXp($userId, 60, 'exam_completion', $attemptId, $eventKey, "Menyelesaikan Simulasi Ujian TOEFL ({$totalScore}/100)")) {
            $xpAwarded = 60;
        }

        // Check first_exam achievement
        Achievement::unlock($userId, 'first_exam');
        Achievement::evaluateAll($userId);

        return [
            'attempt_id'        => $attemptId,
            'exam_title'        => $examTitle,
            'reading_score'     => $readingScore,
            'reading_correct'   => $readingCorrect,
            'reading_total'     => $readingTotal,
            'listening_score'   => $listeningScore,
            'listening_correct' => $listeningCorrect,
            'listening_total'   => $listeningTotal,
            'structure_score'   => $structureScore,
            'structure_correct' => $structureCorrect,
            'structure_total'   => $structureTotal,
            'total_score'       => $totalScore,
            'max_score'         => 100,
            'duration_seconds'  => $durationSeconds,
            'xp_awarded'        => $xpAwarded,
            'detailed_review'   => $detailedReview,
        ];
    }

    public static function getUserExamHistory(int $userId): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM exam_attempts WHERE user_id = :uid ORDER BY completed_at DESC');
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll();
    }

    public static function getAttemptById(int $attemptId, int $userId): ?array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM exam_attempts WHERE id = :id AND user_id = :uid LIMIT 1');
        $stmt->execute(['id' => $attemptId, 'uid' => $userId]);
        $attempt = $stmt->fetch();
        return $attempt ?: null;
    }
}

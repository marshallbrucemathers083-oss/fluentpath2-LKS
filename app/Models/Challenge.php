<?php
// app/Models/Challenge.php - Challenge Validation, Multi-type Evaluator, and Attempt Recorder

require_once dirname(__DIR__) . '/Database.php';
require_once dirname(__DIR__) . '/Services/XpService.php';
require_once __DIR__ . '/Achievement.php';

class Challenge {
    public static function getById(int $challengeId): ?array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM challenges WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $challengeId]);
        $ch = $stmt->fetch();
        if (!$ch) {
            return null;
        }

        $oStmt = $pdo->prepare('SELECT id, option_key, option_text FROM challenge_options WHERE challenge_id = :cid ORDER BY option_key ASC');
        $oStmt->execute(['cid' => $challengeId]);
        $ch['options'] = $oStmt->fetchAll();

        return $ch;
    }

    /**
     * Validate and grade user's challenge submission server-side
     */
    public static function submit(int $userId, int $challengeId, string $submittedAnswer): array {
        $ch = self::getById($challengeId);
        if (!$ch) {
            return ['success' => false, 'error' => 'Tantangan tidak ditemukan.'];
        }

        $type = $ch['type'];
        $correctAnswer = $ch['correct_answer'];
        $isCorrect = false;

        $submittedClean = trim($submittedAnswer);
        $correctClean = trim($correctAnswer);

        switch ($type) {
            case 'multiple_choice':
            case 'reading_comprehension':
            case 'listening_comprehension':
                // Check if submitted value matches option_key or option_text
                $answerStmt = Database::getConnection()->prepare('SELECT option_key, option_text FROM challenge_options WHERE challenge_id = :cid AND is_correct = 1');
                $answerStmt->execute(['cid' => $challengeId]);
                foreach ($answerStmt->fetchAll() as $opt) {
                    if (strcasecmp($submittedClean, $opt['option_key']) === 0 || strcasecmp($submittedClean, $opt['option_text']) === 0) $isCorrect = true;
                }
                if (!$isCorrect && empty($ch['options'])) $isCorrect = (strcasecmp($submittedClean, $correctClean) === 0);
                break;

            case 'fill_blank':
            case 'grammar_correction':
                // Normalize spaces and punctuation for fair evaluation
                $sNorm = strtolower(preg_replace('/[[:punct:]\s]+/', ' ', $submittedClean));
                $cNorm = strtolower(preg_replace('/[[:punct:]\s]+/', ' ', $correctClean));
                $isCorrect = ($sNorm === $cNorm);
                break;

            case 'sentence_order':
                // Compare ordered tokens
                $submittedTokens = json_decode($submittedClean, true);
                $correctTokens = json_decode($correctClean, true);

                if (is_array($submittedTokens) && is_array($correctTokens)) {
                    $isCorrect = ($submittedTokens === $correctTokens);
                } else {
                    // Fallback to normalized space string comparison
                    $sNorm = strtolower(preg_replace('/[[:punct:]\s]+/', ' ', $submittedClean));
                    $cNorm = strtolower(preg_replace('/[[:punct:]\s]+/', ' ', $correctClean));
                    $isCorrect = ($sNorm === $cNorm);
                }
                break;

            case 'vocab_match':
                $submittedPairs = json_decode($submittedClean, true);
                $correctPairs = json_decode($correctClean, true);

                if (is_array($submittedPairs) && is_array($correctPairs)) {
                    $matchAll = true;
                    foreach ($correctPairs as $k => $v) {
                        if (!isset($submittedPairs[$k]) || strcasecmp(trim($submittedPairs[$k]), trim($v)) !== 0) {
                            $matchAll = false;
                            break;
                        }
                    }
                    $isCorrect = $matchAll;
                }
                break;

            case 'short_answer':
                // Check if answer matches expected canonical keywords
                $sNorm = strtolower($submittedClean);
                $cNorm = strtolower($correctClean);
                if (str_contains($sNorm, $cNorm) || str_contains($cNorm, $sNorm)) {
                    $isCorrect = true;
                }
                break;

            default:
                $isCorrect = (strcasecmp($submittedClean, $correctClean) === 0);
                break;
        }

        $pdo = Database::getConnection();
        $xpAwarded = 0;
        $score = $isCorrect ? 100 : 0;

        // If correct, attempt to award XP (idempotent, only awarded once per challenge)
        if ($isCorrect) {
            $reward = (int)$ch['xp_reward'];
            $eventKey = "challenge_{$challengeId}_u{$userId}";
            if (XpService::awardXp($userId, $reward, 'challenge_completion', $challengeId, $eventKey, "Menyelesaikan Tantangan #{$challengeId}")) {
                $xpAwarded = $reward;
            }
        }

        // Record attempt in database
        $stmt = $pdo->prepare("
            INSERT INTO challenge_attempts (user_id, challenge_id, submitted_answer, is_correct, score, xp_awarded, attempted_at)
            VALUES (:uid, :cid, :ans, :correct, :score, :xp, NOW())
        ");
        $stmt->execute([
            'uid'     => $userId,
            'cid'     => $challengeId,
            'ans'     => $submittedClean,
            'correct' => $isCorrect ? 1 : 0,
            'score'   => $score,
            'xp'      => $xpAwarded,
        ]);

        // Evaluate achievements
        $newAchievements = Achievement::evaluateAll($userId);

        return [
            'success'          => true,
            'is_correct'       => $isCorrect,
            'correct_answer'   => $correctAnswer,
            'explanation'      => $ch['explanation'],
            'xp_awarded'       => $xpAwarded,
            'new_achievements' => $newAchievements,
        ];
    }
}

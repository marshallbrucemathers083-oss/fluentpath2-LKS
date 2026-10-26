<?php
// app/Models/Lesson.php - Curriculum, Lessons, and Progress Management

require_once dirname(__DIR__) . '/Database.php';
require_once dirname(__DIR__) . '/Services/XpService.php';
require_once __DIR__ . '/Achievement.php';

class Lesson {
    public static function getAllGroupedByLevel(?int $userId = null): array {
        $pdo = Database::getConnection();

        $sql = "
            SELECT l.*, 
                   (CASE WHEN ulp.is_completed = 1 THEN 1 ELSE 0 END) AS is_completed,
                   ulp.completed_at
            FROM lessons l
            LEFT JOIN user_lesson_progress ulp ON l.id = ulp.lesson_id AND ulp.user_id = :uid
            WHERE l.is_published = 1 AND l.archived_at IS NULL
            ORDER BY 
                FIELD(l.level, 'A1', 'A2', 'B1', 'B2', 'C1', 'C2'),
                l.order_num ASC,
                l.id ASC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['uid' => $userId ?? 0]);
        $rows = $stmt->fetchAll();

        $grouped = [
            'A1' => [], 'A2' => [], 'B1' => [], 'B2' => [], 'C1' => [], 'C2' => []
        ];

        foreach ($rows as $row) {
            $grouped[$row['level']][] = $row;
        }

        return $grouped;
    }

    public static function getById(int $lessonId, ?int $userId = null): ?array {
        $pdo = Database::getConnection();

        $sql = "
            SELECT l.*, 
                   (CASE WHEN ulp.is_completed = 1 THEN 1 ELSE 0 END) AS is_completed,
                   ulp.completed_at
            FROM lessons l
            LEFT JOIN user_lesson_progress ulp ON l.id = ulp.lesson_id AND ulp.user_id = :uid
            WHERE l.id = :id AND l.is_published = 1 AND l.archived_at IS NULL
            LIMIT 1
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $lessonId, 'uid' => $userId ?? 0]);
        $lesson = $stmt->fetch();

        if (!$lesson) {
            return null;
        }

        // Fetch associated challenges
        $cSql = "
            SELECT c.*,
                   (SELECT COUNT(*) FROM challenge_attempts ca WHERE ca.challenge_id = c.id AND ca.user_id = :uid AND ca.is_correct = 1) AS user_solved
            FROM challenges c
            WHERE c.lesson_id = :lid
            ORDER BY c.id ASC
        ";
        $cStmt = $pdo->prepare($cSql);
        $cStmt->execute(['lid' => $lessonId, 'uid' => $userId ?? 0]);
        $challenges = $cStmt->fetchAll();

        // Attach options for each challenge
        foreach ($challenges as &$ch) {
            $oStmt = $pdo->prepare('SELECT id, option_key, option_text FROM challenge_options WHERE challenge_id = :cid ORDER BY option_key ASC');
            $oStmt->execute(['cid' => $ch['id']]);
            $ch['options'] = $oStmt->fetchAll();
        }
        unset($ch);

        $lesson['challenges'] = $challenges;
        return $lesson;
    }

    public static function markCompleted(int $userId, int $lessonId): array {
        $pdo = Database::getConnection();

        $lesson = self::getById($lessonId, $userId);
        if (!$lesson) {
            return ['success' => false, 'error' => 'Pelajaran tidak ditemukan.'];
        }

        // Upsert completion record
        $stmt = $pdo->prepare("
            INSERT INTO user_lesson_progress (user_id, lesson_id, is_completed, completed_at)
            VALUES (:uid, :lid, 1, NOW())
            ON DUPLICATE KEY UPDATE is_completed = 1, completed_at = IF(completed_at IS NULL, NOW(), completed_at)
        ");
        $stmt->execute(['uid' => $userId, 'lid' => $lessonId]);

        // Award XP (idempotent, only awarded once per lesson)
        $xpAwarded = 0;
        $reward = (int)$lesson['xp_reward'];
        $eventKey = "lesson_{$lessonId}_u{$userId}";
        if (XpService::awardXp($userId, $reward, 'lesson_completion', $lessonId, $eventKey, "Menyelesaikan Pelajaran: {$lesson['title']}")) {
            $xpAwarded = $reward;
        }

        // Check and award achievements
        $newAchievements = Achievement::evaluateAll($userId);

        return [
            'success'          => true,
            'xp_awarded'       => $xpAwarded,
            'new_achievements' => $newAchievements,
        ];
    }

    public static function getRecommendedNext(int $userId): ?array {
        $pdo = Database::getConnection();

        // Find the first lesson that the user has not completed
        $sql = "
            SELECT l.*
            FROM lessons l
            LEFT JOIN user_lesson_progress ulp ON l.id = ulp.lesson_id AND ulp.user_id = :uid
            WHERE (ulp.is_completed IS NULL OR ulp.is_completed = 0) AND l.is_published = 1 AND l.archived_at IS NULL
            ORDER BY 
                FIELD(l.level, 'A1', 'A2', 'B1', 'B2', 'C1', 'C2'),
                l.order_num ASC,
                l.id ASC
            LIMIT 1
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['uid' => $userId]);
        $next = $stmt->fetch();

        if ($next) {
            return $next;
        }

        // If all completed, return the first lesson as review
        $first = $pdo->query("SELECT * FROM lessons ORDER BY id ASC LIMIT 1")->fetch();
        return $first ?: null;
    }
}

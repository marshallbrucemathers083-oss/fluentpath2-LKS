<?php
// app/Models/Achievement.php - Achievement badges and unlocks

require_once dirname(__DIR__) . '/Database.php';
require_once dirname(__DIR__) . '/Services/XpService.php';
require_once dirname(__DIR__) . '/Services/StreakService.php';

class Achievement {
    public static function getAll(): array {
        $pdo = Database::getConnection();
        return $pdo->query('SELECT * FROM achievements ORDER BY id ASC')->fetchAll();
    }

    public static function getUserAchievements(int $userId): array {
        $pdo = Database::getConnection();
        $sql = "
            SELECT a.*, ua.unlocked_at, 
                   (CASE WHEN ua.id IS NOT NULL THEN 1 ELSE 0 END) AS is_unlocked
            FROM achievements a
            LEFT JOIN user_achievements ua ON a.id = ua.achievement_id AND ua.user_id = :user_id
            ORDER BY a.id ASC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public static function unlock(int $userId, string $code): bool {
        $pdo = Database::getConnection();
        
        $stmt = $pdo->prepare('SELECT id, xp_reward, title FROM achievements WHERE code = :code LIMIT 1');
        $stmt->execute(['code' => $code]);
        $ach = $stmt->fetch();
        if (!$ach) {
            return false;
        }

        try {
            $insert = $pdo->prepare('INSERT INTO user_achievements (user_id, achievement_id, unlocked_at) VALUES (:uid, :aid, NOW())');
            $insert->execute(['uid' => $userId, 'aid' => $ach['id']]);

            // Award achievement XP
            XpService::awardXp(
                $userId,
                (int)$ach['xp_reward'],
                'achievement',
                (int)$ach['id'],
                "ach_{$ach['id']}_u{$userId}",
                "Meraih Pencapaian: " . $ach['title']
            );

            return true;
        } catch (PDOException $e) {
            // Already unlocked (duplicate key)
            return false;
        }
    }

    /**
     * Check conditions and unlock eligible badges for a user
     */
    public static function evaluateAll(int $userId): array {
        $pdo = Database::getConnection();
        $newlyUnlocked = [];

        // 1. Check completed lessons count
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM user_lesson_progress WHERE user_id = :uid AND is_completed = 1');
        $stmt->execute(['uid' => $userId]);
        $lessonsCount = (int)$stmt->fetchColumn();

        if ($lessonsCount >= 1) {
            if (self::unlock($userId, 'first_lesson')) {
                $newlyUnlocked[] = 'first_lesson';
            }
        }
        if ($lessonsCount >= 5) {
            if (self::unlock($userId, 'five_lessons')) {
                $newlyUnlocked[] = 'five_lessons';
            }
        }

        // 2. Check completed challenges count
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM challenge_attempts WHERE user_id = :uid AND is_correct = 1');
        $stmt->execute(['uid' => $userId]);
        $challengesCount = (int)$stmt->fetchColumn();

        if ($challengesCount >= 1) {
            if (self::unlock($userId, 'first_challenge')) {
                $newlyUnlocked[] = 'first_challenge';
            }
        }

        // 3. Check streak
        $streak = StreakService::calculateStreak($userId);
        if ($streak >= 7) {
            if (self::unlock($userId, 'seven_day_streak')) {
                $newlyUnlocked[] = 'seven_day_streak';
            }
        }

        // 4. Check exam completion
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM exam_attempts WHERE user_id = :uid');
        $stmt->execute(['uid' => $userId]);
        if ((int)$stmt->fetchColumn() >= 1) {
            if (self::unlock($userId, 'first_exam')) {
                $newlyUnlocked[] = 'first_exam';
            }
        }

        return $newlyUnlocked;
    }
}

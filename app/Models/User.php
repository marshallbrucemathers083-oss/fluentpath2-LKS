<?php
// app/Models/User.php - User Profile, Learning Statistics, and Progress Calculations

require_once dirname(__DIR__) . '/Database.php';
require_once dirname(__DIR__) . '/Services/XpService.php';
require_once dirname(__DIR__) . '/Services/StreakService.php';

class User {
    public static function findById(int $userId): ?array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT id, username, created_at FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function getStats(int $userId): array {
        $pdo = Database::getConnection();

        // 1. Total XP and Weekly XP
        $totalXp = XpService::getUserTotalXp($userId);
        $weeklyXp = XpService::getUserWeeklyXp($userId);

        // 2. Learning Streak
        $streak = StreakService::calculateStreak($userId);

        // 3. Completed Lessons Count
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM user_lesson_progress WHERE user_id = :uid AND is_completed = 1');
        $stmt->execute(['uid' => $userId]);
        $completedLessonsCount = (int)$stmt->fetchColumn();

        // 4. Completed Challenges Count
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM challenge_attempts WHERE user_id = :uid AND is_correct = 1');
        $stmt->execute(['uid' => $userId]);
        $completedChallengesCount = (int)$stmt->fetchColumn();

        // 5. Determine Current CEFR Level
        $cefrLevels = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'];
        $currentLevel = 'A1';

        $stmt = $pdo->prepare("
            SELECT l.level, COUNT(DISTINCT l.id) AS done_count
            FROM user_lesson_progress ulp
            JOIN lessons l ON ulp.lesson_id = l.id
            WHERE ulp.user_id = :uid AND ulp.is_completed = 1
            GROUP BY l.level
        ");
        $stmt->execute(['uid' => $userId]);
        $levelProgress = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // level => done_count

        foreach ($cefrLevels as $lvl) {
            $count = $levelProgress[$lvl] ?? 0;
            if ($count > 0) {
                $currentLevel = $lvl;
            }
        }

        // 6. Recent challenge attempts (last 5)
        $stmt = $pdo->prepare("
            SELECT ca.*, c.prompt, c.type, c.skill, c.level, c.topic
            FROM challenge_attempts ca
            JOIN challenges c ON ca.challenge_id = c.id
            WHERE ca.user_id = :uid
            ORDER BY ca.attempted_at DESC
            LIMIT 5
        ");
        $stmt->execute(['uid' => $userId]);
        $recentAttempts = $stmt->fetchAll();

        // 7. Recent unlocked achievements (last 3)
        $stmt = $pdo->prepare("
            SELECT a.title, a.icon, a.description, ua.unlocked_at
            FROM user_achievements ua
            JOIN achievements a ON ua.achievement_id = a.id
            WHERE ua.user_id = :uid
            ORDER BY ua.unlocked_at DESC
            LIMIT 3
        ");
        $stmt->execute(['uid' => $userId]);
        $recentAchievements = $stmt->fetchAll();

        // 8. Progress by Skill
        $skills = ['Grammar', 'Vocabulary', 'Reading', 'Listening', 'Writing', 'Speaking'];
        $skillProgress = [];
        foreach ($skills as $skill) {
            $stmt = $pdo->prepare("
                SELECT 
                    COUNT(DISTINCT l.id) AS total_lessons,
                    COUNT(DISTINCT CASE WHEN ulp.is_completed = 1 THEN l.id END) AS completed_lessons
                FROM lessons l
                LEFT JOIN user_lesson_progress ulp ON l.id = ulp.lesson_id AND ulp.user_id = :uid
                WHERE l.skill = :skill
            ");
            $stmt->execute(['uid' => $userId, 'skill' => $skill]);
            $row = $stmt->fetch();
            $tot = (int)($row['total_lessons'] ?? 0);
            $comp = (int)($row['completed_lessons'] ?? 0);
            $pct = $tot > 0 ? (int)round(($comp / $tot) * 100) : 0;
            $skillProgress[$skill] = [
                'total'     => $tot,
                'completed' => $comp,
                'percent'   => $pct,
            ];
        }

        return [
            'total_xp'             => $totalXp,
            'weekly_xp'            => $weeklyXp,
            'streak'               => $streak,
            'completed_lessons'    => $completedLessonsCount,
            'completed_challenges' => $completedChallengesCount,
            'current_level'        => $currentLevel,
            'recent_attempts'      => $recentAttempts,
            'recent_achievements'  => $recentAchievements,
            'skill_progress'       => $skillProgress,
        ];
    }
}

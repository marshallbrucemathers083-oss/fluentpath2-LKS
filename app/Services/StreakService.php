<?php
// app/Services/StreakService.php - Accurate learning streak calculation

require_once dirname(__DIR__) . '/Database.php';

class StreakService {
    /**
     * Compute current active day streak for a user
     */
    public static function calculateStreak(int $userId): int {
        $pdo = Database::getConnection();

        // Get distinct active dates from XP transactions and challenge attempts
        $sql = "
            SELECT DISTINCT activity_date FROM (
                SELECT DATE(created_at) AS activity_date FROM xp_transactions WHERE user_id = :uid1
                UNION
                SELECT DATE(attempted_at) AS activity_date FROM challenge_attempts WHERE user_id = :uid2
                UNION
                SELECT DATE(completed_at) AS activity_date FROM user_lesson_progress WHERE user_id = :uid3 AND is_completed = 1
            ) AS combined_activity
            ORDER BY activity_date DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['uid1' => $userId, 'uid2' => $userId, 'uid3' => $userId]);
        $dates = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($dates)) {
            return 0;
        }

        $today = new DateTime('today');
        $yesterday = (new DateTime('today'))->modify('-1 day');

        $latestDate = new DateTime($dates[0]);

        // If latest activity was before yesterday, the streak is broken
        if ($latestDate < $yesterday) {
            return 0;
        }

        $streak = 0;
        $expected = ($latestDate->format('Y-m-d') === $today->format('Y-m-d')) ? $today : $yesterday;

        foreach ($dates as $dateStr) {
            $actDate = new DateTime($dateStr);
            if ($actDate->format('Y-m-d') === $expected->format('Y-m-d')) {
                $streak++;
                $expected->modify('-1 day');
            } elseif ($actDate < $expected) {
                break;
            }
        }

        return $streak;
    }
}

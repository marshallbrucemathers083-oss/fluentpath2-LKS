<?php
// app/Models/Leaderboard.php - Weekly & All-Time Leaderboards with Stable Tie-Breaking

require_once dirname(__DIR__) . '/Database.php';

class Leaderboard {
    /**
     * Get Weekly Leaderboard for the current calendar week
     */
    public static function getWeekly(int $limit = 20, ?int $currentUserId = null): array {
        $pdo = Database::getConnection();

        $sql = "
            SELECT 
                u.id AS user_id,
                u.username,
                SUM(x.amount) AS weekly_xp,
                MIN(x.created_at) AS first_xp_this_week
            FROM users u
            JOIN xp_transactions x ON u.id = x.user_id
            WHERE YEARWEEK(x.created_at, 1) = YEARWEEK(NOW(), 1)
            GROUP BY u.id, u.username
            HAVING weekly_xp > 0
            ORDER BY weekly_xp DESC, first_xp_this_week ASC, u.username ASC
            LIMIT " . (int)$limit;

        $rankings = $pdo->query($sql)->fetchAll();

        // Assign ranks and check user rank
        $currentUserRank = null;
        foreach ($rankings as $idx => &$row) {
            $row['rank'] = $idx + 1;
            if ($currentUserId && (int)$row['user_id'] === $currentUserId) {
                $currentUserRank = $row['rank'];
            }
        }
        unset($row);

        // If current user is not in top limit, calculate their standalone weekly rank
        if ($currentUserId && $currentUserRank === null) {
            $userRankSql = "
                SELECT rank_pos FROM (
                    SELECT 
                        u.id AS user_id,
                        ROW_NUMBER() OVER (ORDER BY SUM(x.amount) DESC, MIN(x.created_at) ASC, u.username ASC) AS rank_pos
                    FROM users u
                    JOIN xp_transactions x ON u.id = x.user_id
                    WHERE YEARWEEK(x.created_at, 1) = YEARWEEK(NOW(), 1)
                    GROUP BY u.id, u.username
                ) ranked
                WHERE user_id = :uid
            ";
            $stmt = $pdo->prepare($userRankSql);
            $stmt->execute(['uid' => $currentUserId]);
            $currentUserRank = $stmt->fetchColumn() ?: null;
        }

        // Get start and end date of current week (Monday - Sunday)
        $monday = new DateTime();
        $monday->setISODate((int)$monday->format('o'), (int)$monday->format('W'), 1);
        $sunday = clone $monday;
        $sunday->modify('+6 days');

        return [
            'rankings'          => $rankings,
            'current_user_rank' => $currentUserRank,
            'week_period'       => $monday->format('d M') . ' - ' . $sunday->format('d M Y'),
        ];
    }

    /**
     * Get All-Time Leaderboard
     */
    public static function getAllTime(int $limit = 20, ?int $currentUserId = null): array {
        $pdo = Database::getConnection();

        $sql = "
            SELECT 
                u.id AS user_id,
                u.username,
                SUM(x.amount) AS total_xp,
                MIN(x.created_at) AS first_xp_date
            FROM users u
            JOIN xp_transactions x ON u.id = x.user_id
            GROUP BY u.id, u.username
            HAVING total_xp > 0
            ORDER BY total_xp DESC, first_xp_date ASC, u.username ASC
            LIMIT " . (int)$limit;

        $rankings = $pdo->query($sql)->fetchAll();

        $currentUserRank = null;
        foreach ($rankings as $idx => &$row) {
            $row['rank'] = $idx + 1;
            if ($currentUserId && (int)$row['user_id'] === $currentUserId) {
                $currentUserRank = $row['rank'];
            }
        }
        unset($row);

        // If current user is not in top limit, calculate their standalone rank
        if ($currentUserId && $currentUserRank === null) {
            $userRankSql = "
                SELECT rank_pos FROM (
                    SELECT 
                        u.id AS user_id,
                        ROW_NUMBER() OVER (ORDER BY SUM(x.amount) DESC, MIN(x.created_at) ASC, u.username ASC) AS rank_pos
                    FROM users u
                    JOIN xp_transactions x ON u.id = x.user_id
                    GROUP BY u.id, u.username
                ) ranked
                WHERE user_id = :uid
            ";
            $stmt = $pdo->prepare($userRankSql);
            $stmt->execute(['uid' => $currentUserId]);
            $currentUserRank = $stmt->fetchColumn() ?: null;
        }

        return [
            'rankings'          => $rankings,
            'current_user_rank' => $currentUserRank,
        ];
    }
}

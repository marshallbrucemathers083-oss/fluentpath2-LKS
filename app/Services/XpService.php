<?php
// app/Services/XpService.php - Server-side XP Management & Anti-Abuse Rules

require_once dirname(__DIR__) . '/Database.php';

class XpService {
    /**
     * Award XP with idempotency and duplicate prevention.
     * Guaranteed safe across concurrent submissions via unique event_key.
     */
    public static function awardXp(
        int $userId,
        int $amount,
        string $sourceType,
        ?int $sourceId,
        string $eventKey,
        string $description
    ): bool {
        if ($amount <= 0) {
            return false;
        }

        $pdo = Database::getConnection();

        try {
            // Check if this event has already rewarded XP
            $checkStmt = $pdo->prepare('SELECT id FROM xp_transactions WHERE event_key = :event_key LIMIT 1');
            $checkStmt->execute(['event_key' => $eventKey]);
            if ($checkStmt->fetch()) {
                // Already awarded; do not grant duplicate XP
                return false;
            }

            $stmt = $pdo->prepare(
                'INSERT INTO xp_transactions (user_id, amount, source_type, source_id, event_key, description, created_at)
                 VALUES (:user_id, :amount, :source_type, :source_id, :event_key, :description, NOW())'
            );

            $stmt->execute([
                'user_id'     => $userId,
                'amount'      => $amount,
                'source_type' => $sourceType,
                'source_id'   => $sourceId,
                'event_key'   => $eventKey,
                'description' => $description,
            ]);

            return true;
        } catch (PDOException $e) {
            // Duplicate key constraint violation is safely handled
            if ($e->getCode() === '23000') {
                return false;
            }
            throw $e;
        }
    }

    /**
     * Get total XP earned by a user all-time
     */
    public static function getUserTotalXp(int $userId): int {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT COALESCE(SUM(amount), 0) FROM xp_transactions WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Get weekly XP earned by a user in the current calendar week (Monday to Sunday)
     */
    public static function getUserWeeklyXp(int $userId): int {
        $pdo = Database::getConnection();
        // MySQL YEARWEEK with mode 1: week starts Monday
        $stmt = $pdo->prepare(
            'SELECT COALESCE(SUM(amount), 0) FROM xp_transactions 
             WHERE user_id = :user_id AND YEARWEEK(created_at, 1) = YEARWEEK(NOW(), 1)'
        );
        $stmt->execute(['user_id' => $userId]);
        return (int)$stmt->fetchColumn();
    }
}

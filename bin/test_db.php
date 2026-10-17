<?php
// bin/test_db.php - CLI Database Connection Verification Tool

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/app/Database.php';

echo "=== FluentPath Database Connection Tester ===\n";

$config = require dirname(__DIR__) . '/config/database.php';
echo "Host: " . $config['host'] . ":" . $config['port'] . "\n";
echo "Database: " . $config['dbname'] . "\n";
echo "User: " . $config['user'] . "\n";
echo "Connecting...\n";

try {
    $pdo = Database::getConnection();
    echo "[OK] PDO connection established successfully!\n";

    // Test tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Found " . count($tables) . " tables in database:\n";
    foreach ($tables as $t) {
        $count = $pdo->query("SELECT COUNT(*) FROM `{$t}`")->fetchColumn();
        echo " - {$t}: {$count} records\n";
    }

    echo "\nDatabase verification passed!\n";
    exit(0);
} catch (Throwable $e) {
    echo "[FAILED] Connection error: " . $e->getMessage() . "\n";
    echo "\nPlease ensure the database and user are created:\n";
    echo "  sudo mariadb < database/setup.sql\n";
    echo "  sudo mariadb fluentpath < database/schema.sql\n";
    echo "  sudo mariadb fluentpath < database/seed.sql\n";
    exit(1);
}

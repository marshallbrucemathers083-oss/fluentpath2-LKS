<?php
// bin/migrate.php - Safe database migration and seeder runner via PDO

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/app/Database.php';

echo "=== FluentPath Schema & Seed Migrator ===\n";

try {
    $pdo = Database::getConnection();
    echo "[1/2] Importing database/schema.sql...\n";
    $schemaSql = file_get_contents(dirname(__DIR__) . '/database/schema.sql');
    $pdo->exec($schemaSql);
    echo " -> Schema imported successfully.\n";

    echo "[2/2] Importing database/seed.sql...\n";
    $seedSql = file_get_contents(dirname(__DIR__) . '/database/seed.sql');
    $pdo->exec($seedSql);
    echo " -> Seed data imported successfully.\n";

    echo "\nAll migrations completed successfully!\n";
    exit(0);
} catch (Throwable $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}

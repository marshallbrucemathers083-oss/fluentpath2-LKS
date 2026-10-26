<?php
// bin/migrate.php - Legacy schema and starter-content importer.
// Use only for a new database. It is not an upgrade tool for a populated database.

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/app/Database.php';

echo "=== FluentPath Schema & Seed Migrator ===\n";

try {
    $pdo = Database::getConnection();
    $existingTables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    if ($existingTables) {
        throw new RuntimeException('Database is not empty. This importer is only for a fresh installation; no files were imported.');
    }
    echo "[1/2] Importing database/schema.sql...\n";
    $schemaSql = file_get_contents(dirname(__DIR__) . '/database/schema.sql');
    if ($schemaSql === false) throw new RuntimeException('Unable to read database/schema.sql.');
    $pdo->exec($schemaSql);
    echo " -> Schema imported successfully.\n";

    echo "[2/2] Importing database/seed.sql...\n";
    $seedSql = file_get_contents(dirname(__DIR__) . '/database/seed.sql');
    if ($seedSql === false) throw new RuntimeException('Unable to read database/seed.sql.');
    $pdo->exec($seedSql);
    echo " -> Seed data imported successfully.\n";

    echo "\nAll migrations completed successfully!\n";
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, "Migration failed: " . $e->getMessage() . "\n");
    exit(1);
}

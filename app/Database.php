<?php
// app/Database.php - PDO Connection Manager

class Database {
    private static ?PDO $instance = null;
    private static ?string $lastError = null;

    public static function getConnection(): PDO {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $config = require dirname(__DIR__) . '/config/database.php';

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['dbname'],
            $config['charset']
        );

        try {
            self::$instance = new PDO(
                $dsn,
                $config['user'],
                $config['password'],
                $config['options']
            );
            return self::$instance;
        } catch (PDOException $e) {
            self::$lastError = $e->getMessage();
            
            // Log connection error to storage
            $logDir = dirname(__DIR__) . '/storage/logs';
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0755, true);
            }
            @file_put_contents(
                $logDir . '/database.log',
                sprintf("[%s] DB Connection Error: %s\n", date('Y-m-d H:i:s'), $e->getMessage()),
                FILE_APPEND
            );

            // If running in CLI, throw exception
            if (php_sapi_name() === 'cli') {
                throw $e;
            }

            // Web context: show user-friendly error page without exposing raw credentials or stack trace
            self::renderConnectionError();
            exit;
        }
    }

    public static function getLastError(): ?string {
        return self::$lastError;
    }

    public static function testConnection(): bool {
        try {
            $pdo = self::getConnection();
            $stmt = $pdo->query('SELECT 1');
            return (bool)$stmt->fetchColumn();
        } catch (Throwable $e) {
            self::$lastError = $e->getMessage();
            return false;
        }
    }

    private static function renderConnectionError(): void {
        http_response_code(503);
        $errorView = dirname(__DIR__) . '/views/pages/db_error.php';
        if (file_exists($errorView)) {
            require $errorView;
        } else {
            echo '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><title>Koneksi Database Terputus - FluentPath</title>';
            echo '<style>body{font-family:sans-serif;background:#f8fafc;color:#1e293b;padding:40px;line-height:1.6;} .card{background:#fff;max-width:600px;margin:40px auto;padding:32px;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);}</style></head><body>';
            echo '<div class="card"><h2>Koneksi Database Belum Terhubung</h2><p>Layanan database FluentPath sedang tidak dapat dijangkau. Silakan pastikan server MariaDB/MySQL berjalan dan kredensial di <code>.env</code> sudah sesuai.</p>';
            echo '<p>Jalankan perintah inisialisasi: <code>sudo mariadb &lt; database/setup.sql</code></p></div></body></html>';
        }
    }
}

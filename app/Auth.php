<?php
// app/Auth.php - Authentication and Session Management

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Helpers.php';

class Auth {
    public static function register(string $username, string $password, string $passwordConfirm): array {
        $username = trim($username);
        $errors = [];

        if (empty($username)) {
            $errors['username'] = 'Nama pengguna wajib diisi.';
        } elseif (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
            $errors['username'] = 'Nama pengguna hanya boleh memuat huruf, angka, dan garis bawah (3-30 karakter).';
        }

        if (strlen($password) < 6) {
            $errors['password'] = 'Kata sandi minimal harus 6 karakter.';
        }

        if ($password !== $passwordConfirm) {
            $errors['password_confirm'] = 'Konfirmasi kata sandi tidak cocok.';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $pdo = Database::getConnection();

        // Check duplicate username
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username LIMIT 1');
        $stmt->execute(['username' => $username]);
        if ($stmt->fetch()) {
            return ['success' => false, 'errors' => ['username' => 'Nama pengguna sudah digunakan oleh akun lain.']];
        }

        // Hash password securely
        $hash = password_hash($password, PASSWORD_BCRYPT);

        // Insert new user
        // Public registration always creates the default role; request data cannot set it.
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, created_at) VALUES (:username, :hash, NOW())");
        $stmt->execute([
            'username' => $username,
            'hash'     => $hash,
        ]);

        $userId = (int)$pdo->lastInsertId();

        return ['success' => true, 'user_id' => $userId];
    }

    public static function login(string $username, string $password): array {
        $username = trim($username);

        if (empty($username) || empty($password)) {
            return ['success' => false, 'error' => 'Nama pengguna dan kata sandi wajib diisi.'];
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT id, username, password_hash, created_at, role, account_status, status_reason FROM users WHERE username = :username LIMIT 1');
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'error' => 'Nama pengguna atau kata sandi yang Anda masukkan salah.'];
        }

        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user'] = [
            'id'         => (int)$user['id'],
            'username'   => $user['username'],
            'created_at' => $user['created_at'],
            'role' => $user['role'] ?? 'user',
        ];

        return ['success' => true, 'account_status' => $user['account_status'] ?? 'active'];
    }

    public static function logout(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
    }

    public static function requireAuth(): void {
        if (!is_authenticated()) {
            flash('error', 'Silakan masuk terlebih dahulu untuk mengakses fitur pembelajaran.');
            redirect('/login');
        }
        if (!self::accountIsActive()) {
            $path=parse_url($_SERVER['REQUEST_URI']??'',PHP_URL_PATH);
            if($path==='/appeal')return;
            http_response_code(403);
            echo '<!doctype html><html lang="id"><meta charset="utf-8"><title>Akun dibatasi · FluentPath</title><link rel="stylesheet" href="/assets/css/style.css"><main class="system-message"><h1>Akun Anda sedang dibatasi</h1><p>Anda dapat melihat alasan keputusan dan mengajukan banding.</p><a class="button button-primary" href="/appeal">Lihat status dan ajukan banding</a></main></html>'; exit;
        }
    }

    public static function accountIsActive(): bool {
        if(!is_authenticated())return false;
        $stmt=Database::getConnection()->prepare('SELECT account_status FROM users WHERE id=:id LIMIT 1');
        $stmt->execute(['id'=>(int)$_SESSION['user_id']]);
        return $stmt->fetchColumn()==='active';
    }

    public static function isAdmin(): bool {
        if (!is_authenticated()) return false;
        $stmt = Database::getConnection()->prepare("SELECT role, account_status FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => (int)$_SESSION['user_id']]);
        $row = $stmt->fetch();
        if (!$row || $row['account_status'] !== 'active') {
            return false;
        }
        $_SESSION['user']['role'] = $row['role'];
        return $row['role'] === 'admin';
    }

    public static function requireAdmin(): void {
        self::requireAuth();
        if (!self::isAdmin()) {
            http_response_code(403);
            echo '<!doctype html><html lang="id"><meta charset="utf-8"><meta name="viewport" content="width=device-width"><title>Akses ditolak · FluentPath</title><link rel="stylesheet" href="/assets/css/style.css"><main class="system-message"><h1>Halaman ini khusus administrator</h1><p>Akun Anda tidak memiliki izin untuk membuka bagian tersebut.</p><a class="button button-primary" href="/dashboard">Kembali ke FluentPath</a></main></html>'; exit;
        }
    }

    public static function requireGuest(): void {
        if (is_authenticated()) {
            redirect('/dashboard');
        }
    }
}

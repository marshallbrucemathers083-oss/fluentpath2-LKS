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
        $stmt = $pdo->prepare('INSERT INTO users (username, password_hash, created_at) VALUES (:username, :hash, NOW())');
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
        $stmt = $pdo->prepare('SELECT id, username, password_hash, created_at FROM users WHERE username = :username LIMIT 1');
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
        ];

        return ['success' => true];
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
    }

    public static function requireGuest(): void {
        if (is_authenticated()) {
            redirect('/dashboard');
        }
    }
}

<?php
// app/Helpers.php - Utility functions for escaping, CSRF, flash messages, and views

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Escape HTML output to prevent XSS
 */
function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Generate or retrieve CSRF token
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Output hidden CSRF input field
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * Validate CSRF token from POST request
 */
function validate_csrf(): bool {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return true;
    }
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    return !empty($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

/**
 * Flash message helper
 */
function flash(string $key, ?string $message = null): ?string {
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    if (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

/**
 * Check if flash message exists
 */
function has_flash(string $key): bool {
    return isset($_SESSION['flash'][$key]);
}

/**
 * Check if user is logged in
 */
function is_authenticated(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get logged-in user data
 */
function current_user(): ?array {
    if (!is_authenticated()) {
        return null;
    }
    return $_SESSION['user'] ?? null;
}

/**
 * Redirect to path
 */
function redirect(string $url): void {
    header("Location: {$url}");
    exit;
}

/**
 * Send JSON response
 */
function json_response(array $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

/**
 * Render view template with data
 */
function view(string $viewName, array $data = []): void {
    extract($data);
    $viewFile = dirname(__DIR__) . '/views/' . str_replace('.', '/', $viewName) . '.php';
    if (!file_exists($viewFile)) {
        http_response_code(404);
        echo "View not found: " . e($viewName);
        exit;
    }
    require $viewFile;
}

/**
 * Color badge mapping for CEFR levels
 */
function get_cefr_color(string $level): string {
    return match (strtoupper($level)) {
        'A1' => '#10b981', // emerald
        'A2' => '#059669', // green
        'B1' => '#0284c7', // primary sky/cyan
        'B2' => '#2563eb', // blue
        'C1' => '#7c3aed', // purple
        'C2' => '#db2777', // rose/pink
        default => '#64748b',
    };
}

<?php
// public/index.php - Front Controller and Application Router

require_once dirname(__DIR__) . '/app/Helpers.php';
require_once dirname(__DIR__) . '/app/Auth.php';
require_once dirname(__DIR__) . '/app/Models/User.php';
require_once dirname(__DIR__) . '/app/Models/Lesson.php';
require_once dirname(__DIR__) . '/app/Models/Challenge.php';
require_once dirname(__DIR__) . '/app/Models/Academy.php';
require_once dirname(__DIR__) . '/app/Models/Exam.php';
require_once dirname(__DIR__) . '/app/Models/Leaderboard.php';
require_once dirname(__DIR__) . '/app/Models/Achievement.php';

// Parse current URL path
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestUri = rtrim($requestUri, '/');
if ($requestUri === '') {
    $requestUri = '/';
}

$method = $_SERVER['REQUEST_METHOD'];

// Route dispatching
switch (true) {
    // 1. Landing Page
    case $requestUri === '/':
        if (is_authenticated()) {
            redirect('/dashboard');
        }
        $weeklyBoard = Leaderboard::getWeekly(5);
        $weeklyRankings = $weeklyBoard['rankings'];
        view('pages.home', compact('weeklyRankings'));
        break;

    // 2. Authentication: Register
    case $requestUri === '/register':
        Auth::requireGuest();
        if ($method === 'POST') {
            if (!validate_csrf()) {
                flash('error', 'Token keamanan CSRF tidak valid.');
                redirect('/register');
            }
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';

            $result = Auth::register($username, $password, $passwordConfirm);
            if ($result['success']) {
                // Auto login user
                Auth::login($username, $password);
                flash('success', 'Selamat datang di FluentPath! Akun Anda berhasil dibuat.');
                redirect('/dashboard');
            } else {
                $errors = $result['errors'];
                $oldUsername = $username;
                view('pages.register', compact('errors', 'oldUsername'));
            }
        } else {
            view('pages.register');
        }
        break;

    // 3. Authentication: Login
    case $requestUri === '/login':
        Auth::requireGuest();
        if ($method === 'POST') {
            if (!validate_csrf()) {
                flash('error', 'Token keamanan CSRF tidak valid.');
                redirect('/login');
            }
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $result = Auth::login($username, $password);
            if ($result['success']) {
                flash('success', 'Berhasil masuk ke FluentPath.');
                redirect('/dashboard');
            } else {
                $error = $result['error'];
                $oldUsername = $username;
                view('pages.login', compact('error', 'oldUsername'));
            }
        } else {
            view('pages.login');
        }
        break;

    // 4. Authentication: Logout
    case $requestUri === '/logout':
        Auth::logout();
        flash('info', 'Anda telah berhasil keluar dari akun.');
        redirect('/login');
        break;

    // 5. Dashboard
    case $requestUri === '/dashboard':
        Auth::requireAuth();
        $user = current_user();
        $userId = (int)$user['id'];
        $stats = User::getStats($userId);
        $recommendedLesson = Lesson::getRecommendedNext($userId);
        $weeklyData = Leaderboard::getWeekly(5, $userId);
        view('pages.dashboard', compact('user', 'stats', 'recommendedLesson', 'weeklyData'));
        break;

    // 6. Learning Path Catalog
    case $requestUri === '/lessons':
        Auth::requireAuth();
        $user = current_user();
        $userId = (int)$user['id'];
        $groupedLessons = Lesson::getAllGroupedByLevel($userId);
        view('pages.lessons.index', compact('groupedLessons', 'user'));
        break;

    // 7. Individual Lesson View (/lessons/{id})
    case preg_match('#^/lessons/([0-9]+)$#', $requestUri, $matches):
        Auth::requireAuth();
        $user = current_user();
        $userId = (int)$user['id'];
        $lessonId = (int)$matches[1];
        $lesson = Lesson::getById($lessonId, $userId);
        if (!$lesson) {
            http_response_code(404);
            echo "Pelajaran tidak ditemukan.";
            exit;
        }
        view('pages.lessons.view', compact('lesson', 'user'));
        break;

    // 8. Academy Hub
    case $requestUri === '/academy':
        Auth::requireAuth();
        $user = current_user();
        view('pages.academy.index', compact('user'));
        break;

    // 9. Academy Practice Session
    case $requestUri === '/academy/practice':
        Auth::requireAuth();
        $user = current_user();
        $userId = (int)$user['id'];
        $filters = [
            'skill'      => $_GET['skill'] ?? null,
            'level'      => $_GET['level'] ?? null,
            'difficulty' => $_GET['difficulty'] ?? null,
            'mode'       => $_GET['mode'] ?? null,
        ];
        $questions = Academy::getQuestions($filters, 10, $userId);
        view('pages.academy.practice', compact('questions', 'filters', 'user'));
        break;

    // 10. Exam Lab Hub
    case $requestUri === '/exam':
        Auth::requireAuth();
        $user = current_user();
        $userId = (int)$user['id'];
        $history = Exam::getUserExamHistory($userId);
        view('pages.exam.index', compact('history', 'user'));
        break;

    // 11. Timed Exam Session
    case $requestUri === '/exam/take':
        Auth::requireAuth();
        $user = current_user();
        $questions = Exam::getExamQuestions();
        view('pages.exam.take', compact('questions', 'user'));
        break;

    // 12. Exam Result
    case $requestUri === '/exam/result':
        Auth::requireAuth();
        $user = current_user();
        $userId = (int)$user['id'];
        $attemptId = (int)($_GET['id'] ?? 0);
        $attempt = Exam::getAttemptById($attemptId, $userId);
        if (!$attempt) {
            flash('error', 'Catatan ujian tidak ditemukan.');
            redirect('/exam');
        }
        view('pages.exam.result', compact('attempt', 'user'));
        break;

    // 13. Leaderboard
    case $requestUri === '/leaderboard':
        Auth::requireAuth();
        $user = current_user();
        $userId = (int)$user['id'];
        $weeklyData = Leaderboard::getWeekly(20, $userId);
        $allTimeData = Leaderboard::getAllTime(20, $userId);
        view('pages.leaderboard', compact('weeklyData', 'allTimeData', 'user'));
        break;

    // 14. User Profile
    case $requestUri === '/profile':
        Auth::requireAuth();
        $user = current_user();
        $userId = (int)$user['id'];
        $stats = User::getStats($userId);
        $achievements = Achievement::getUserAchievements($userId);
        view('pages.profile', compact('user', 'stats', 'achievements'));
        break;

    default:
        http_response_code(404);
        echo "<!DOCTYPE html><html lang='id'><head><meta charset='utf-8'><title>404 Halaman Tidak Ditemukan</title><link rel='stylesheet' href='/assets/css/style.css'></head><body style='text-align:center; padding:80px 20px;'><div class='card' style='max-width:500px; margin:0 auto; padding:40px;'><h1>404</h1><p>Halaman yang Anda tuju tidak ditemukan.</p><a href='/' class='btn btn-primary'>Kembali ke Beranda</a></div></body></html>";
        break;
}

<?php
// public/api/lesson_complete.php - API endpoint to mark lesson as completed

require_once dirname(__DIR__, 2) . '/app/Auth.php';
require_once dirname(__DIR__, 2) . '/app/Models/Lesson.php';

header('Content-Type: application/json; charset=utf-8');

if (!is_authenticated()) {
    json_response(['success' => false, 'error' => 'Silakan masuk terlebih dahulu.'], 401);
}
if (!Auth::accountIsActive()) json_response(['success'=>false,'error'=>'Akun Anda sedang dibatasi. Ajukan banding melalui halaman status akun.'],403);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validate_csrf()) {
    json_response(['success' => false, 'error' => 'Permintaan tidak valid atau token CSRF tidak cocok.'], 403);
}

$userId = (int)$_SESSION['user_id'];
$lessonId = (int)($_POST['lesson_id'] ?? 0);

if ($lessonId <= 0) {
    json_response(['success' => false, 'error' => 'ID pelajaran tidak valid.'], 400);
}

$result = Lesson::markCompleted($userId, $lessonId);
json_response($result);

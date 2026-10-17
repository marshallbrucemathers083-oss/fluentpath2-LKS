<?php
// public/api/exam_submit.php - API endpoint to grade TOEFL-style practice test

require_once dirname(__DIR__, 2) . '/app/Auth.php';
require_once dirname(__DIR__, 2) . '/app/Models/Exam.php';

header('Content-Type: application/json; charset=utf-8');

if (!is_authenticated()) {
    json_response(['success' => false, 'error' => 'Silakan masuk terlebih dahulu.'], 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validate_csrf()) {
    json_response(['success' => false, 'error' => 'Permintaan tidak valid atau token CSRF tidak cocok.'], 403);
}

$userId = (int)$_SESSION['user_id'];
$answers = $_POST['answers'] ?? [];
$duration = (int)($_POST['duration_seconds'] ?? 0);

$result = Exam::submitExam($userId, $answers, $duration);
$result['success'] = true;
$result['redirect_url'] = '/exam/result?id=' . $result['attempt_id'];

json_response($result);

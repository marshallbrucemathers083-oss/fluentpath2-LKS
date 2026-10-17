<?php
// public/api/academy_submit.php - API endpoint to evaluate Academy practice session

require_once dirname(__DIR__, 2) . '/app/Auth.php';
require_once dirname(__DIR__, 2) . '/app/Models/Academy.php';

header('Content-Type: application/json; charset=utf-8');

if (!is_authenticated()) {
    json_response(['success' => false, 'error' => 'Silakan masuk terlebih dahulu.'], 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validate_csrf()) {
    json_response(['success' => false, 'error' => 'Permintaan tidak valid atau token CSRF tidak cocok.'], 403);
}

$userId = (int)$_SESSION['user_id'];
$answers = $_POST['answers'] ?? [];

if (!is_array($answers) || empty($answers)) {
    json_response(['success' => false, 'error' => 'Tidak ada lembar jawaban yang dikirimkan.'], 400);
}

$result = Academy::submitSession($userId, $answers);
json_response($result);

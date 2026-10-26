<?php
// public/api/challenge_submit.php - API endpoint for validating single challenge attempt

require_once dirname(__DIR__, 2) . '/app/Auth.php';
require_once dirname(__DIR__, 2) . '/app/Models/Challenge.php';

header('Content-Type: application/json; charset=utf-8');

if (!is_authenticated()) {
    json_response(['success' => false, 'error' => 'Sesi Anda telah kedaluwarsa. Silakan masuk kembali.'], 401);
}
if (!Auth::accountIsActive()) json_response(['success'=>false,'error'=>'Akun Anda sedang dibatasi. Ajukan banding melalui halaman status akun.'],403);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validate_csrf()) {
    json_response(['success' => false, 'error' => 'Permintaan tidak valid atau token CSRF tidak cocok.'], 403);
}

$userId = (int)$_SESSION['user_id'];
$challengeId = (int)($_POST['challenge_id'] ?? 0);
$answer = $_POST['answer'] ?? '';

if ($challengeId <= 0) {
    json_response(['success' => false, 'error' => 'ID tantangan tidak valid.'], 400);
}

$result = Challenge::submit($userId, $challengeId, (string)$answer);
json_response($result);

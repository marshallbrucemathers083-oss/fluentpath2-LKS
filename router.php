<?php
// router.php - Robust Router Script for PHP Built-in Development Server
// Can be run as: php -S localhost:8000 router.php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$publicFile = __DIR__ . '/public' . $uri;

// Check if requested path corresponds to a static file or PHP script in public/
if ($uri !== '/' && file_exists($publicFile) && !is_dir($publicFile)) {
    $ext = strtolower(pathinfo($publicFile, PATHINFO_EXTENSION));
    if ($ext === 'php') {
        require $publicFile;
        exit;
    }
    $mimes = [
        'css'  => 'text/css; charset=utf-8',
        'js'   => 'application/javascript; charset=utf-8',
        'json' => 'application/json; charset=utf-8',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'svg'  => 'image/svg+xml',
        'ico'  => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2'=> 'font/woff2',
        'ttf'  => 'font/ttf',
    ];

    if (isset($mimes[$ext])) {
        header("Content-Type: {$mimes[$ext]}");
    }
    header('Content-Length: ' . filesize($publicFile));
    readfile($publicFile);
    exit;
}

// Route all dynamic page requests to public/index.php
require __DIR__ . '/public/index.php';

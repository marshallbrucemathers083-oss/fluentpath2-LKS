<?php
// config/app.php - General application configuration

return [
    'name'     => $_ENV['APP_NAME'] ?? getenv('APP_NAME') ?: 'FluentPath',
    'env'      => $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?: 'development',
    'url'      => $_ENV['APP_URL'] ?? getenv('APP_URL') ?: 'http://localhost:8000',
    'cefr_levels' => ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'],
    'skills'   => ['Grammar', 'Vocabulary', 'Reading', 'Listening', 'Writing', 'Speaking'],
];

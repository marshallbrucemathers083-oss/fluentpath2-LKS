<?php
// views/layouts/header.php - Global Header Layout
$isAuth = is_authenticated();
$user = current_user();
$pageTitle = $title ?? 'Platform Pembelajaran Bahasa Inggris Terstruktur';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> — FluentPath</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <script>
        (function() {
            var saved = localStorage.getItem('fluentpath_theme');
            var systemDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            var theme = (saved === 'dark' || saved === 'light') ? saved : (systemDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
</head>
<body>
<?php if ($isAuth): ?>
<div class="app-container">
    <?php require dirname(__DIR__) . '/layouts/sidebar.php'; ?>
    <div class="main-content">
        <header class="topbar">
            <div style="font-weight: 600; font-size: 0.95rem; color: var(--text-muted);">
                Selamat datang kembali, <span style="color: var(--text-main); font-weight: 700;"><?= e($user['username'] ?? 'Learner') ?></span>
            </div>
            <div style="display: flex; align-items: center; gap: 14px;">
                <button type="button" class="theme-toggle-btn" aria-label="Ganti Tema" title="Ganti Mode Terang / Gelap">
                    <span class="icon-sun">☀️</span>
                    <span class="icon-moon">🌙</span>
                </button>
                <a href="/lessons" class="btn btn-outline btn-sm">Mulai Belajar</a>
                <a href="/logout" class="btn btn-secondary btn-sm" style="color: var(--danger);">Keluar</a>
            </div>
        </header>
        <main class="page-body">
            <?php require dirname(__DIR__) . '/partials/alerts.php'; ?>
<?php else: ?>
    <header class="public-header">
        <div class="public-nav-container">
            <a href="/" class="brand-logo">
                <div class="brand-icon">F</div>
                <span>FluentPath</span>
            </a>
            <ul class="public-nav-links">
                <li><a href="/#levels">Tingkatan CEFR</a></li>
                <li><a href="/#features">Fitur Belajar</a></li>
                <li><a href="/#leaderboard">Klasemen</a></li>
            </ul>
            <div style="display: flex; align-items: center; gap: 12px;">
                <button type="button" class="theme-toggle-btn" aria-label="Ganti Tema" title="Ganti Mode Terang / Gelap">
                    <span class="icon-sun">☀️</span>
                    <span class="icon-moon">🌙</span>
                </button>
                <a href="/login" class="btn btn-outline btn-sm">Masuk</a>
                <a href="/register" class="btn btn-primary btn-sm">Daftar Akun</a>
            </div>
        </div>
    </header>
    <main style="flex-grow: 1;">
        <div style="max-width: 1200px; margin: 20px auto 0; padding: 0 24px;">
            <?php require dirname(__DIR__) . '/partials/alerts.php'; ?>
        </div>
<?php endif; ?>

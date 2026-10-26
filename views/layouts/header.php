<?php
$isAuth = is_authenticated();
$user = current_user();
$isActiveAccount = $isAuth && Auth::accountIsActive();
$isAdmin = $isActiveAccount && Auth::isAdmin();
$moderationNotice = null;
if ($isAuth) {
    $noticeQuery = Database::getConnection()->prepare("SELECT action_type,reason,created_at FROM moderation_history WHERE target_user_id=:id AND action_type IN ('warning','post_suspend','account_suspend','ban') ORDER BY created_at DESC LIMIT 1");
    $noticeQuery->execute(['id'=>(int)$user['id']]);
    $latestNotice = $noticeQuery->fetch();
    if ($latestNotice) {
        $userCheck = Database::getConnection()->prepare("SELECT account_status, post_status, status_reason FROM users WHERE id=:id LIMIT 1");
        $userCheck->execute(['id'=>(int)$user['id']]);
        $uState = $userCheck->fetch();
        if ($uState && ($uState['account_status'] !== 'active' || $uState['post_status'] !== 'active' || !empty($uState['status_reason']))) {
            $moderationNotice = $latestNotice;
        }
    }
}
$pageTitle = $title ?? 'Belajar bahasa Inggris dengan terarah';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$navItems = $isActiveAccount
    ? [['/dashboard', 'Beranda'], ['/lessons', 'Jalur belajar'], ['/academy', 'Latihan'], ['/exam', 'Ujian'], ['/forum', 'Forum'], ['/leaderboard', 'Peringkat']]
    : ($isAuth ? [['/appeal','Status akun dan banding']] : [['/#curriculum', 'Kurikulum'], ['/#how-it-works', 'Cara belajar']]);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f6f4ec">
    <title><?= e($pageTitle) ?> · FluentPath</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="header-inner">
        <a class="brand" href="<?= $isActiveAccount ? '/dashboard' : ($isAuth?'/appeal':'/') ?>" aria-label="FluentPath, beranda">
            <span class="brand-mark" aria-hidden="true">F</span>
            <span>FluentPath</span>
        </a>
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation">
            <span class="nav-toggle-label">Menu</span>
            <span class="nav-toggle-icon" aria-hidden="true"></span>
        </button>
        <nav class="primary-nav" id="primary-navigation" aria-label="Navigasi utama">
            <?php foreach ($navItems as [$href, $label]): ?>
                <?php $target = parse_url($href, PHP_URL_PATH) ?: '/'; ?>
                <a href="<?= e($href) ?>"<?= $path === $target ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
            <?php endforeach; ?>
        </nav>
        <div class="header-actions">
            <?php if ($isAuth): ?>
                <a class="profile-link" href="<?= $isActiveAccount?'/profile':'/appeal' ?>"><?= e($user['username'] ?? 'Profil') ?></a>
                <?php if ($isAdmin): ?><a class="profile-link" href="/admin">Admin</a><?php endif; ?>
                <form action="/logout" method="post" class="logout-form"><?= csrf_field() ?><button class="button button-quiet" type="submit">Keluar</button></form>
            <?php else: ?>
                <a class="button button-quiet" href="/login">Masuk</a>
                <a class="button button-primary" href="/register">Buat akun</a>
            <?php endif; ?>
        </div>
    </div>
</header>
<main class="site-main <?= $isAuth ? 'is-authenticated' : 'is-public' ?>">
<?php require dirname(__DIR__) . '/partials/alerts.php'; ?>
<?php if ($moderationNotice): ?><aside class="moderation-notice moderation-site-notice" aria-label="Pemberitahuan moderator"><strong><?= e(str_replace('_',' ',ucfirst($moderationNotice['action_type']))) ?></strong> · <?= e($moderationNotice['created_at']) ?>: <?= e($moderationNotice['reason']) ?> <a href="/appeal">Lihat status atau ajukan banding</a></aside><?php endif; ?>

<?php
$title = 'Beranda belajar';
require dirname(__DIR__) . '/layouts/header.php';
$next = $recommendedLesson;
?>
<header class="dashboard-welcome">
    <div><p class="eyebrow">Beranda belajar · <?= e(date('l, j F Y')) ?></p><h1>Selamat datang, <?= e($user['username']) ?></h1><p class="muted">Lanjutkan dari materi berikutnya atau pilih latihan yang ingin Anda kerjakan.</p></div>
    <span class="current-level">Tingkat <?= e($stats['current_level']) ?></span>
</header>

<?php if ($next): ?>
    <section class="next-lesson" aria-labelledby="next-lesson-title">
        <div>
            <p class="eyebrow">Materi berikutnya · <?= e($next['level']) ?> · <?= e($next['skill']) ?></p>
            <h2 id="next-lesson-title"><?= e($next['title']) ?></h2>
            <p><?= e($next['learning_objectives']) ?></p>
            <a class="button button-primary" href="/lessons/<?= (int)$next['id'] ?>">Buka pelajaran</a>
        </div>
        <span class="lesson-reward">+<?= (int)$next['xp_reward'] ?> XP setelah selesai</span>
    </section>
<?php else: ?>
    <section class="surface content-section"><h2>Belum ada pelajaran</h2><p class="muted">Katalog pelajaran belum tersedia.</p></section>
<?php endif; ?>

<section class="content-section" aria-labelledby="progress-title">
    <div class="section-header"><div><p class="eyebrow">Progres kurikulum</p><h2 id="progress-title">Selesai per keterampilan</h2></div><a class="text-link" href="/lessons">Lihat semua pelajaran →</a></div>
    <div class="progress-list">
        <?php foreach ($stats['skill_progress'] as $skill => $progress): ?>
            <div class="progress-row"><span><?= e($skill) ?></span><div class="progress-track" role="progressbar" aria-label="Progres <?= e($skill) ?>" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= (int)$progress['percent'] ?>"><span style="width: <?= (int)$progress['percent'] ?>%"></span></div><span class="progress-value"><?= (int)$progress['completed'] ?>/<?= (int)$progress['total'] ?></span></div>
        <?php endforeach; ?>
    </div>
</section>

<section class="content-section" aria-labelledby="recent-activity-title">
    <div class="section-header"><div><p class="eyebrow">Riwayat latihan</p><h2 id="recent-activity-title">Aktivitas terbaru</h2></div><a class="text-link" href="/academy">Buka Academy →</a></div>
    <?php if (!empty($stats['recent_attempts'])): ?>
        <ul class="activity-list">
            <?php foreach (array_slice($stats['recent_attempts'], 0, 5) as $attempt): ?>
                <li><span><strong><?= e($attempt['skill']) ?></strong> · <?= e($attempt['level']) ?><?= $attempt['is_correct'] ? ' · Benar' : ' · Perlu ditinjau' ?></span><time datetime="<?= e(date(DATE_ATOM, strtotime($attempt['attempted_at']))) ?>"><?= e(date('j M Y, H:i', strtotime($attempt['attempted_at']))) ?></time></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="surface muted">Belum ada latihan tercatat. Kerjakan tantangan pada pelajaran untuk memulai riwayat.</p>
    <?php endif; ?>
</section>
<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>

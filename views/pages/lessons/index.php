<?php
$title = 'Jalur belajar';
require dirname(__DIR__, 2) . '/layouts/header.php';
$levelNames = ['A1'=>'Pemula', 'A2'=>'Dasar', 'B1'=>'Menengah', 'B2'=>'Menengah atas', 'C1'=>'Mahir', 'C2'=>'Lanjut'];
$activeLevel = $_GET['level'] ?? 'all';
?>
<header class="page-header">
    <p class="eyebrow">Kurikulum CEFR · A1–C2</p>
    <h1>Jalur belajar</h1>
    <p>Pelajaran dikelompokkan berdasarkan tingkat dan keterampilan. Pilih tingkat, lalu buka materi yang ingin Anda pelajari.</p>
</header>

<nav class="level-filter" aria-label="Filter tingkat">
    <a href="/lessons"<?= $activeLevel === 'all' ? ' aria-current="page"' : '' ?>>Semua</a>
    <?php foreach (array_keys($groupedLessons) as $level): ?>
        <a href="/lessons?level=<?= e($level) ?>"<?= $activeLevel === $level ? ' aria-current="page"' : '' ?>><?= e($level) ?></a>
    <?php endforeach; ?>
</nav>

<?php $shownLevels = 0; foreach ($groupedLessons as $level => $items): ?>
    <?php if ($activeLevel !== 'all' && $activeLevel !== $level) continue; $shownLevels++; $completed = count(array_filter($items, static fn($lesson) => (bool)$lesson['is_completed'])); ?>
    <section class="level-section" aria-labelledby="level-<?= e($level) ?>">
        <header class="level-heading"><div><p class="eyebrow"><?= e($level) ?> · <?= e($levelNames[$level] ?? '') ?></p><h2 id="level-<?= e($level) ?>"><?= e($levelNames[$level] ?? $level) ?></h2><p><?= $completed ?> dari <?= count($items) ?> pelajaran selesai</p></div><span class="progress-value"><?= count($items) ? (int)round($completed / count($items) * 100) : 0 ?>%</span></header>
        <?php if ($items): ?>
            <ol class="lesson-list">
                <?php foreach ($items as $index => $lesson): ?>
                    <li class="lesson-row">
                        <span class="lesson-number"><?= str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                        <div class="lesson-main"><div class="lesson-meta"><span><?= e($lesson['skill']) ?></span><span><?= $lesson['is_completed'] ? 'Selesai' : '+' . (int)$lesson['xp_reward'] . ' XP' ?></span></div><h3><a href="/lessons/<?= (int)$lesson['id'] ?>"><?= e($lesson['title']) ?></a></h3><p><?= e($lesson['learning_objectives']) ?></p></div>
                        <a class="lesson-open" href="/lessons/<?= (int)$lesson['id'] ?>"><?= $lesson['is_completed'] ? 'Ulangi' : 'Mulai' ?> →</a>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php else: ?>
            <p class="empty-inline">Belum ada pelajaran pada tingkat ini.</p>
        <?php endif; ?>
    </section>
<?php endforeach; ?>
<?php if (!$shownLevels): ?><p class="surface">Tingkat yang dipilih tidak ditemukan. <a href="/lessons">Tampilkan semua tingkat</a>.</p><?php endif; ?>
<?php require dirname(__DIR__, 2) . '/layouts/footer.php'; ?>

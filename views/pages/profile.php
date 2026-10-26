<?php $title = 'Profil dan pencapaian'; require dirname(__DIR__) . '/layouts/header.php'; ?>
<header class="page-header"><p class="eyebrow">Akun dan progres</p><h1>Profil</h1><p>Ringkasan hasil belajar dan pencapaian yang tersimpan pada akun ini.</p></header>

<section class="profile-heading">
    <span class="avatar" aria-hidden="true"><?= e(strtoupper(substr($user['username'], 0, 1))) ?></span>
    <div><h2><?= e($user['username']) ?></h2><p class="muted">Bergabung <?= e(date('j M Y', strtotime($user['created_at']))) ?></p></div>
</section>

<dl class="profile-facts">
    <div><dt>Tingkat saat ini</dt><dd><?= e($stats['current_level']) ?></dd></div>
    <div><dt>Total XP</dt><dd><?= number_format((int)$stats['total_xp']) ?></dd></div>
    <div><dt>Rentetan belajar</dt><dd><?= (int)$stats['streak'] ?> hari</dd></div>
    <div><dt>Pelajaran selesai</dt><dd><?= (int)$stats['completed_lessons'] ?></dd></div>
</dl>

<section class="content-section"><div class="section-header"><div><p class="eyebrow">Kurikulum</p><h2>Progres keterampilan</h2></div></div>
    <div class="progress-list"><?php foreach ($stats['skill_progress'] as $skill => $progress): ?><div class="progress-row"><span><?= e($skill) ?></span><div class="progress-track" role="progressbar" aria-label="Progres <?= e($skill) ?>" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= (int)$progress['percent'] ?>"><span style="width: <?= (int)$progress['percent'] ?>%"></span></div><span class="progress-value"><?= (int)$progress['completed'] ?>/<?= (int)$progress['total'] ?></span></div><?php endforeach; ?></div>
</section>

<section class="content-section"><div class="section-header"><div><p class="eyebrow">Pencapaian</p><h2>Lencana</h2></div></div>
    <?php if (!$achievements): ?><p class="surface muted">Belum ada lencana yang tersedia.</p><?php else: ?>
        <ul class="achievement-list"><?php foreach ($achievements as $achievement): $unlocked = (bool)$achievement['is_unlocked']; ?><li><div><strong><?= e($achievement['title']) ?></strong><p><?= e($achievement['description']) ?></p><span class="form-hint">Hadiah <?= (int)$achievement['xp_reward'] ?> XP</span></div><span class="achievement-state <?= $unlocked ? 'is-unlocked' : '' ?>"><?php if ($unlocked): ?>Terbuka<?= !empty($achievement['unlocked_at']) ? ' · ' . e(date('j M Y', strtotime($achievement['unlocked_at']))) : '' ?><?php else: ?>Belum terbuka<?php endif; ?></span></li><?php endforeach; ?></ul>
    <?php endif; ?>
</section>
<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>

<?php
// views/pages/profile.php - User Profile, Achievement Badges, and Comprehensive Progress
$title = "Profil & Pencapaian Belajar";
require dirname(__DIR__) . '/layouts/header.php';
?>

<div class="reveal-on-scroll" style="margin-bottom: 24px;">
    <h1 class="page-title">Profil & Portofolio Pembelajaran</h1>
    <p class="page-subtitle">
        Lacak rekam jejak penguasaan kompetensi bahasa Inggris dan koleksi lencana pencapaian Anda.
    </p>
</div>

<!-- Profile Card Header -->
<div class="card reveal-on-scroll" style="margin-bottom: 28px; background: var(--card-gradient);">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
        <div style="display: flex; align-items: center; gap: 20px;">
            <div class="avatar-circle" style="width: 68px; height: 68px; font-size: 1.8rem;">
                <?= strtoupper(substr($user['username'], 0, 1)) ?>
            </div>
            <div>
                <h2 style="font-size: 1.5rem; margin-bottom: 4px;"><?= e($user['username']) ?></h2>
                <div style="font-size: 0.85rem; color: var(--text-muted);">
                    Anggota sejak: <?= date('d M Y', strtotime($user['created_at'])) ?> &bull; Status Akun: Aktif
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <div class="profile-pill">
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Jenjang CEFR</div>
                <div style="font-weight: 800; font-size: 1.25rem; color: <?= get_cefr_color($stats['current_level']) ?>;">
                    Level <?= e($stats['current_level']) ?>
                </div>
            </div>
            <div class="profile-pill">
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Total Pengalaman</div>
                <div style="font-weight: 800; font-size: 1.25rem; color: var(--primary);">
                    <?= number_format($stats['total_xp']) ?> XP
                </div>
            </div>
            <div class="profile-pill">
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Streak Belajar</div>
                <div style="font-weight: 800; font-size: 1.25rem; color: var(--warning);">
                    🔥 <?= $stats['streak'] ?> Hari
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Skill Mastery Progress -->
<div class="card reveal-on-scroll" style="margin-bottom: 32px;">
    <h3 style="font-size: 1.25rem; margin-bottom: 20px;">Penguasaan Berdasarkan Keterampilan Bahasa</h3>
    <div class="grid-2">
        <?php foreach ($stats['skill_progress'] as $skillName => $p): ?>
            <div style="background: var(--bg-alt); padding: 14px 18px; border-radius: var(--radius-md); border: 1px solid var(--border);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <span style="font-weight: 700; font-size: 0.95rem; color: var(--text-main);"><?= e($skillName) ?></span>
                    <span style="font-weight: 700; font-size: 0.9rem; color: var(--primary);"><?= $p['percent'] ?>%</span>
                </div>
                <div class="progress-container" style="height: 10px;">
                    <div class="progress-fill" style="width: <?= $p['percent'] ?>%;"></div>
                </div>
                <div style="font-size: 0.775rem; color: var(--text-muted); margin-top: 6px;">
                    <?= $p['completed'] ?> dari <?= $p['total'] ?> modul kurikulum terselesaikan
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Achievement Badges -->
<div style="margin-bottom: 32px;">
    <div class="reveal-on-scroll" style="margin-bottom: 16px;">
        <h3 style="font-size: 1.3rem; margin-bottom: 4px;">Koleksi Lencana Pencapaian (Badges)</h3>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">
            Lencana diperoleh secara otomatis saat Anda mencapai target belajar nyata.
        </p>
    </div>

    <div class="grid-3">
        <?php foreach ($achievements as $ach): ?>
            <?php $isUnlocked = (bool)$ach['is_unlocked']; ?>
            <div class="card badge-card reveal-on-scroll <?= $isUnlocked ? 'unlocked' : 'locked' ?>">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                    <div class="stat-icon <?= $isUnlocked ? 'stat-icon-amber' : '' ?>" style="width: 50px; height: 50px; font-size: 2rem; border-radius: var(--radius-full); <?= !$isUnlocked ? 'background: var(--bg-alt);' : '' ?>">
                        <?= $isUnlocked ? '🏆' : '🔒' ?>
                    </div>
                    <?php if ($isUnlocked): ?>
                        <span class="badge badge-success">Terbuka</span>
                    <?php else: ?>
                        <span class="badge" style="background: var(--bg-alt); color: var(--text-muted);">Terkunci</span>
                    <?php endif; ?>
                </div>

                <h4 style="font-size: 1.1rem; margin-bottom: 6px; color: <?= $isUnlocked ? 'var(--warning-text)' : 'inherit' ?>;">
                    <?= e($ach['title']) ?>
                </h4>

                <p style="font-size: 0.85rem; color: <?= $isUnlocked ? 'var(--text-main)' : 'var(--text-muted)' ?>; line-height: 1.5; margin-bottom: 14px;">
                    <?= e($ach['description']) ?>
                </p>

                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border); padding-top: 10px; font-size: 0.8rem;">
                    <span style="font-weight: 700; color: var(--warning);">+<?= (int)$ach['xp_reward'] ?> XP</span>
                    <?php if ($isUnlocked && !empty($ach['unlocked_at'])): ?>
                        <span style="color: var(--text-muted);"><?= date('d M Y', strtotime($ach['unlocked_at'])) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>

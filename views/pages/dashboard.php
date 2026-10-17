<?php
// views/pages/dashboard.php - Authenticated User Learning Dashboard
$title = "Dashboard Pembelajaran";
require dirname(__DIR__) . '/layouts/header.php';
?>

<div class="reveal-on-scroll" style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
    <div>
        <h1 class="page-title">Halo, <?= e($user['username']) ?>! 👋</h1>
        <p class="page-subtitle" style="margin-bottom: 0;">
            Pantau capaian belajar, pertahankan streak harian, dan lanjutkan pelajaran berikutnya.
        </p>
    </div>
    <div>
        <span class="badge badge-cefr" style="background: <?= get_cefr_color($stats['current_level']) ?>; font-size: 0.95rem; padding: 6px 14px;">
            Jenjang Aktif: Level <?= e($stats['current_level']) ?>
        </span>
    </div>
</div>

<!-- Top Stats Grid -->
<div class="grid-4" style="margin-bottom: 28px;">
    <!-- Total XP -->
    <div class="card card-interactive reveal-on-scroll" style="display: flex; align-items: center; gap: 16px;">
        <div class="stat-icon stat-icon-blue">
            ⚡
        </div>
        <div>
            <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600; letter-spacing: 0.04em;">
                Total Pengalaman
            </div>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--primary);">
                <?= number_format($stats['total_xp']) ?> <span style="font-size: 0.95rem; font-weight: 600;">XP</span>
            </div>
            <div style="font-size: 0.775rem; color: var(--text-muted);">
                <?= number_format($stats['weekly_xp']) ?> XP minggu ini
            </div>
        </div>
    </div>

    <!-- Learning Streak -->
    <div class="card card-interactive reveal-on-scroll" style="display: flex; align-items: center; gap: 16px;">
        <div class="stat-icon stat-icon-amber">
            🔥
        </div>
        <div>
            <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600; letter-spacing: 0.04em;">
                Streak Belajar
            </div>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--warning);">
                <?= $stats['streak'] ?> <span style="font-size: 0.95rem; font-weight: 600;">Hari</span>
            </div>
            <div style="font-size: 0.775rem; color: var(--text-muted);">
                <?= $stats['streak'] > 0 ? 'Pertahankan terus!' : 'Belajar hari ini untuk memulai' ?>
            </div>
        </div>
    </div>

    <!-- Completed Lessons -->
    <div class="card card-interactive reveal-on-scroll" style="display: flex; align-items: center; gap: 16px;">
        <div class="stat-icon stat-icon-green">
            📖
        </div>
        <div>
            <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600; letter-spacing: 0.04em;">
                Modul Selesai
            </div>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--success);">
                <?= $stats['completed_lessons'] ?> <span style="font-size: 0.95rem; font-weight: 600;">Pelajaran</span>
            </div>
            <div style="font-size: 0.775rem; color: var(--text-muted);">
                Dari kurikulum A1 - C2
            </div>
        </div>
    </div>

    <!-- Completed Challenges -->
    <div class="card card-interactive reveal-on-scroll" style="display: flex; align-items: center; gap: 16px;">
        <div class="stat-icon stat-icon-purple">
            🎯
        </div>
        <div>
            <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600; letter-spacing: 0.04em;">
                Tantangan Dijawab
            </div>
            <div style="font-size: 1.5rem; font-weight: 800; color: #a855f7;">
                <?= $stats['completed_challenges'] ?> <span style="font-size: 0.95rem; font-weight: 600;">Kuis Sukses</span>
            </div>
            <div style="font-size: 0.775rem; color: var(--text-muted);">
                Akurasi tervalidasi server
            </div>
        </div>
    </div>
</div>

<!-- Recommended Next Lesson & Quick Action -->
<?php if (!empty($recommendedLesson)): ?>
<div class="card card-banner-success reveal-on-scroll" style="margin-bottom: 28px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                <span class="badge" style="background: var(--success); color: #ffffff;">Rekomendasi Pelajaran Berikutnya</span>
                <span class="badge badge-cefr" style="background: <?= get_cefr_color($recommendedLesson['level']) ?>"><?= e($recommendedLesson['level']) ?></span>
                <span class="badge badge-info"><?= e($recommendedLesson['skill']) ?></span>
            </div>
            <h2 style="font-size: 1.35rem; margin-bottom: 6px;"><?= e($recommendedLesson['title']) ?></h2>
            <p style="color: var(--text-muted); font-size: 0.925rem; max-width: 720px; line-height: 1.5; margin: 0;">
                <?= e($recommendedLesson['learning_objectives']) ?>
            </p>
        </div>
        <div>
            <a href="/lessons/<?= (int)$recommendedLesson['id'] ?>" class="btn btn-primary btn-lg">
                Lanjutkan Belajar (+<?= (int)$recommendedLesson['xp_reward'] ?> XP) →
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Main 2-Column Dashboard Grid -->
<div class="grid-2" style="margin-bottom: 28px;">
    <!-- Left Column: Skill Mastery & Recent Attempts -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
        <!-- Skill Progress -->
        <div class="card reveal-on-scroll">
            <div class="card-header">
                <h3 class="card-title">Kemajuan Berdasarkan Keterampilan</h3>
                <a href="/lessons" style="font-size: 0.85rem; font-weight: 600;">Lihat Kurikulum →</a>
            </div>
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <?php foreach ($stats['skill_progress'] as $skillName => $p): ?>
                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.875rem; margin-bottom: 5px;">
                            <span style="font-weight: 600; color: var(--text-main);"><?= e($skillName) ?></span>
                            <span style="color: var(--text-muted); font-weight: 500;">
                                <?= $p['completed'] ?> / <?= $p['total'] ?> Selesai (<?= $p['percent'] ?>%)
                            </span>
                        </div>
                        <div class="progress-container">
                            <div class="progress-fill" style="width: <?= $p['percent'] ?>%;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Recent Challenge Attempts -->
        <div class="card reveal-on-scroll">
            <div class="card-header">
                <h3 class="card-title">Hasil Tantangan Terakhir</h3>
                <a href="/academy" style="font-size: 0.85rem; font-weight: 600;">Latihan Academy →</a>
            </div>
            <?php if (!empty($stats['recent_attempts'])): ?>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <?php foreach ($stats['recent_attempts'] as $att): ?>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; background: var(--bg-alt); border-radius: var(--radius-md); font-size: 0.875rem; transition: background-color 0.2s ease;">
                            <div style="overflow: hidden; padding-right: 12px;">
                                <div style="font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--text-main);">
                                    <?= e($att['prompt']) ?>
                                </div>
                                <div style="font-size: 0.775rem; color: var(--text-muted);">
                                    <?= e($att['skill']) ?> • Level <?= e($att['level']) ?> • <?= date('d M H:i', strtotime($att['attempted_at'])) ?>
                                </div>
                            </div>
                            <div style="text-align: right; flex-shrink: 0;">
                                <?php if ($att['is_correct']): ?>
                                    <span class="badge badge-success">Benar</span>
                                    <?php if ($att['xp_awarded'] > 0): ?>
                                        <div style="font-size: 0.75rem; color: var(--success); font-weight: 700;">+<?= $att['xp_awarded'] ?> XP</div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge badge-danger">Salah</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 24px; color: var(--text-muted); font-size: 0.9rem;">
                    Belum ada riwayat tantangan. Mulai selesaikan pelajaran di <a href="/lessons">Learning Path</a>!
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Column: Achievements & Leaderboard Widget -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
        <!-- Unlocked Achievements -->
        <div class="card reveal-on-scroll">
            <div class="card-header">
                <h3 class="card-title">Pencapaian Baru Dibuka</h3>
                <a href="/profile" style="font-size: 0.85rem; font-weight: 600;">Semua Lencana →</a>
            </div>
            <?php if (!empty($stats['recent_achievements'])): ?>
                <div style="display: flex; flex-direction: column; gap: 14px;">
                    <?php foreach ($stats['recent_achievements'] as $ach): ?>
                        <div style="display: flex; align-items: center; gap: 14px; padding: 12px; border: 1px solid var(--border); border-radius: var(--radius-md); background: var(--bg-card); transition: transform 0.2s ease;">
                            <div class="stat-icon stat-icon-amber" style="width: 44px; height: 44px; font-size: 1.6rem; border-radius: var(--radius-full);">
                                🎖️
                            </div>
                            <div>
                                <div style="font-weight: 700; font-size: 0.925rem; color: var(--text-main);">
                                    <?= e($ach['title']) ?>
                                </div>
                                <div style="font-size: 0.8rem; color: var(--text-muted); line-height: 1.4;">
                                    <?= e($ach['description']) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 24px; color: var(--text-muted); font-size: 0.9rem;">
                    Belum ada lencana yang terbuka. Selesaikan modul pertama Anda untuk membuka <strong>Langkah Pertama</strong>!
                </div>
            <?php endif; ?>
        </div>

        <!-- Weekly Leaderboard Preview -->
        <div class="card reveal-on-scroll">
            <div class="card-header">
                <div>
                    <h3 class="card-title">Papan Peringkat Minggu Ini</h3>
                    <div style="font-size: 0.775rem; color: var(--text-muted);"><?= e($weeklyData['week_period']) ?></div>
                </div>
                <a href="/leaderboard" style="font-size: 0.85rem; font-weight: 600;">Lihat Lengkap →</a>
            </div>

            <?php if ($weeklyData['current_user_rank']): ?>
                <div style="background: var(--primary-light); padding: 10px 14px; border-radius: var(--radius-md); margin-bottom: 14px; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.875rem; font-weight: 600; color: var(--primary-dark);">Peringkat Anda saat ini:</span>
                    <span style="font-weight: 800; font-size: 1rem; color: var(--primary);">#<?= $weeklyData['current_user_rank'] ?></span>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">Pos</th>
                            <th>Pengguna</th>
                            <th style="text-align: right;">XP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($weeklyData['rankings'], 0, 5) as $r): ?>
                            <tr class="<?= ((int)$r['user_id'] === (int)$user['id']) ? 'highlight-user' : '' ?>">
                                <td style="font-weight: 700;">#<?= $r['rank'] ?></td>
                                <td><?= e($r['username']) ?> <?= ((int)$r['user_id'] === (int)$user['id']) ? '<span style="font-size:0.75rem;">(Anda)</span>' : '' ?></td>
                                <td style="text-align: right; font-weight: 700; color: var(--primary);"><?= number_format($r['weekly_xp']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>

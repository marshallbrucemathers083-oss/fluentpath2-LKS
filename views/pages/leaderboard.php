<?php
// views/pages/leaderboard.php - Weekly & All-Time Leaderboards
$title = "Papan Peringkat Pelajar (Leaderboard)";
require dirname(__DIR__) . '/layouts/header.php';

$activeTab = $_GET['tab'] ?? 'weekly';
?>

<div class="reveal-on-scroll" style="margin-bottom: 24px;">
    <h1 class="page-title">Papan Peringkat Pembelajar (Leaderboard)</h1>
    <p class="page-subtitle">
        Bersaing secara sehat bersama pembelajar bahasa Inggris lainnya. XP diperoleh dari penyelesaian modul, tantangan, dan simulasi tes.
    </p>

    <!-- Tab Switcher -->
    <div style="display: flex; gap: 8px; margin-bottom: 24px;">
        <a href="/leaderboard?tab=weekly" class="btn <?= $activeTab === 'weekly' ? 'btn-primary' : 'btn-secondary' ?>">
            📅 Klasemen Mingguan
        </a>
        <a href="/leaderboard?tab=alltime" class="btn <?= $activeTab === 'alltime' ? 'btn-primary' : 'btn-secondary' ?>">
            🌟 Klasemen Sepanjang Masa
        </a>
    </div>
</div>

<?php if ($activeTab === 'weekly'): ?>
    <!-- Weekly Board -->
    <div class="card card-banner-success reveal-on-scroll" style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div>
                <span class="badge badge-success" style="margin-bottom: 4px;">Periode Berjalan</span>
                <div style="font-size: 1.15rem; font-weight: 700; color: var(--success);">
                    Minggu Ini: <?= e($weeklyData['week_period']) ?>
                </div>
            </div>
            <div>
                <?php if ($weeklyData['current_user_rank']): ?>
                    <span style="font-size: 0.95rem; font-weight: 600; color: var(--text-main);">
                        Peringkat Anda: <strong style="font-size: 1.2rem; color: var(--primary);">#<?= $weeklyData['current_user_rank'] ?></strong>
                    </span>
                <?php else: ?>
                    <span style="font-size: 0.85rem; color: var(--text-muted);">Belum ada perolehan XP minggu ini.</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card reveal-on-scroll" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 80px; text-align: center;">Peringkat</th>
                        <th>Nama Pengguna</th>
                        <th style="text-align: right;">XP Mingguan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($weeklyData['rankings'])): ?>
                        <?php foreach ($weeklyData['rankings'] as $r): ?>
                            <tr class="<?= ((int)$r['user_id'] === (int)$user['id']) ? 'highlight-user' : '' ?>">
                                <td style="text-align: center; font-weight: 700; font-size: 1.05rem;">
                                    <?php
                                        echo match ($r['rank']) {
                                            1 => '🥇 #1',
                                            2 => '🥈 #2',
                                            3 => '🥉 #3',
                                            default => '#' . $r['rank']
                                        };
                                    ?>
                                </td>
                                <td>
                                    <span style="font-weight: 600;"><?= e($r['username']) ?></span>
                                    <?php if ((int)$r['user_id'] === (int)$user['id']): ?>
                                        <span class="badge badge-info" style="margin-left: 6px; font-size: 0.7rem;">Anda</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right; font-weight: 800; color: var(--primary); font-size: 1rem;">
                                    <?= number_format((int)$r['weekly_xp']) ?> XP
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                Belum ada aktivitas belajar di minggu ini. Mulai pelajaran sekarang dan jadilah juara!
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php else: ?>
    <!-- All-Time Board -->
    <div class="card card-banner-info reveal-on-scroll" style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div>
                <span class="badge badge-info" style="margin-bottom: 4px;">Akumulasi Sepanjang Masa</span>
                <div style="font-size: 1.15rem; font-weight: 700; color: var(--primary);">
                    Rekor Pembelajar Terbaik FluentPath
                </div>
            </div>
            <div>
                <?php if ($allTimeData['current_user_rank']): ?>
                    <span style="font-size: 0.95rem; font-weight: 600; color: var(--text-main);">
                        Peringkat Anda: <strong style="font-size: 1.2rem; color: var(--primary);">#<?= $allTimeData['current_user_rank'] ?></strong>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card reveal-on-scroll" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 80px; text-align: center;">Peringkat</th>
                        <th>Nama Pengguna</th>
                        <th style="text-align: right;">Total XP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($allTimeData['rankings'])): ?>
                        <?php foreach ($allTimeData['rankings'] as $r): ?>
                            <tr class="<?= ((int)$r['user_id'] === (int)$user['id']) ? 'highlight-user' : '' ?>">
                                <td style="text-align: center; font-weight: 700; font-size: 1.05rem;">
                                    <?php
                                        echo match ($r['rank']) {
                                            1 => '🥇 #1',
                                            2 => '🥈 #2',
                                            3 => '🥉 #3',
                                            default => '#' . $r['rank']
                                        };
                                    ?>
                                </td>
                                <td>
                                    <span style="font-weight: 600;"><?= e($r['username']) ?></span>
                                    <?php if ((int)$r['user_id'] === (int)$user['id']): ?>
                                        <span class="badge badge-info" style="margin-left: 6px; font-size: 0.7rem;">Anda</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right; font-weight: 800; color: var(--primary); font-size: 1rem;">
                                    <?= number_format((int)$r['total_xp']) ?> XP
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                Belum ada data peringkat all-time.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>

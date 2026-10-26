<?php
$title = 'Peringkat';
require dirname(__DIR__) . '/layouts/header.php';
$activeTab = ($_GET['tab'] ?? 'weekly') === 'alltime' ? 'alltime' : 'weekly';
$board = $activeTab === 'weekly' ? $weeklyData : $allTimeData;
$rows = $board['rankings'] ?? [];
?>
<header class="page-header"><p class="eyebrow">XP dari aktivitas belajar</p><h1>Peringkat</h1><p>Lihat XP yang terkumpul selama minggu ini atau sepanjang waktu.</p></header>
<nav class="level-filter" aria-label="Pilih periode"><a href="/leaderboard?tab=weekly"<?= $activeTab === 'weekly' ? ' aria-current="page"' : '' ?>>Minggu ini</a><a href="/leaderboard?tab=alltime"<?= $activeTab === 'alltime' ? ' aria-current="page"' : '' ?>>Sepanjang waktu</a></nav>
<section class="content-section">
    <?php if ($activeTab === 'weekly'): ?><p class="muted">Periode <?= e($weeklyData['week_period']) ?></p><?php endif; ?>
    <?php if ($board['current_user_rank']): ?><p class="current-level">Peringkat Anda: #<?= (int)$board['current_user_rank'] ?></p><?php endif; ?>
    <?php if ($rows): ?>
        <div class="table-wrap"><table><thead><tr><th>Peringkat</th><th>Nama pengguna</th><th><?= $activeTab === 'weekly' ? 'XP minggu ini' : 'Total XP' ?></th></tr></thead><tbody>
            <?php foreach ($rows as $row): $isCurrent = (int)$row['user_id'] === (int)$user['id']; ?><tr class="<?= $isCurrent ? 'current-user-row' : '' ?>"><td><?= (int)$row['rank'] ?></td><td><?= e($row['username']) ?><?= $isCurrent ? ' · Anda' : '' ?></td><td><strong><?= number_format((int)($activeTab === 'weekly' ? $row['weekly_xp'] : $row['total_xp'])) ?> XP</strong></td></tr><?php endforeach; ?>
        </tbody></table></div>
    <?php else: ?>
        <p class="surface muted">Belum ada XP pada periode ini.</p>
    <?php endif; ?>
</section>
<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>

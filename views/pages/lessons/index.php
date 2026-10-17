<?php
// views/pages/lessons/index.php - Learning Path Curriculum Catalog
$title = "Kurikulum Jalur Pembelajaran (CEFR A1 — C2)";
require dirname(__DIR__, 2) . '/layouts/header.php';

$levels = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'];
$activeLevel = $_GET['level'] ?? 'all';
?>

<div class="reveal-on-scroll" style="margin-bottom: 24px;">
    <h1 class="page-title">Kurikulum Pembelajaran Bahasa Inggris</h1>
    <p class="page-subtitle">
        Pilih tingkatan CEFR untuk mempelajari materi tata bahasa, kosakata, membaca, dan mendengarkan.
    </p>

    <!-- Level Filter Tabs -->
    <div style="display: flex; gap: 8px; overflow-x: auto; padding-bottom: 8px; margin-bottom: 20px;">
        <a href="/lessons?level=all" class="btn <?= $activeLevel === 'all' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">
            Semua Tingkat
        </a>
        <?php foreach ($levels as $lvl): ?>
            <a href="/lessons?level=<?= $lvl ?>" class="btn <?= $activeLevel === $lvl ? 'btn-primary' : 'btn-secondary' ?> btn-sm">
                Level <?= $lvl ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<div style="display: flex; flex-direction: column; gap: 36px;">
    <?php foreach ($groupedLessons as $lvl => $lessons): ?>
        <?php if ($activeLevel !== 'all' && $activeLevel !== $lvl) continue; ?>
        <div class="reveal-on-scroll">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid var(--border);">
                <span class="badge badge-cefr" style="background: <?= get_cefr_color($lvl) ?>; font-size: 0.9rem; padding: 5px 12px;">
                    CEFR <?= $lvl ?>
                </span>
                <h2 style="font-size: 1.35rem; margin: 0;">
                    <?php
                        echo match($lvl) {
                            'A1' => 'Beginner (Pemula Dasar)',
                            'A2' => 'Elementary (Dasar Lanjutan)',
                            'B1' => 'Intermediate (Menengah)',
                            'B2' => 'Upper Intermediate (Menengah Atas)',
                            'C1' => 'Advanced (Tingkat Lanjut)',
                            'C2' => 'Proficient (Kecakapan Fasih)',
                            default => $lvl
                        };
                    ?>
                </h2>
                <span style="font-size: 0.85rem; color: var(--text-muted); margin-left: auto;">
                    <?= count($lessons) ?> Modul Pelajaran
                </span>
            </div>

            <div class="grid-2">
                <?php foreach ($lessons as $lesson): ?>
                    <div class="card card-interactive reveal-on-scroll" style="display: flex; flex-direction: column; justify-content: space-between; border-left: 4px solid <?= get_cefr_color($lvl) ?>;">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <div style="display: flex; gap: 6px;">
                                    <span class="badge badge-info"><?= e($lesson['skill']) ?></span>
                                    <span style="font-size: 0.775rem; color: var(--text-muted); font-weight: 600;">Modul #<?= (int)$lesson['order_num'] ?></span>
                                </div>
                                <?php if ($lesson['is_completed']): ?>
                                    <span class="badge badge-success">✓ Selesai</span>
                                <?php else: ?>
                                    <span class="badge" style="background: var(--bg-alt); color: var(--text-muted);">Belum Selesai</span>
                                <?php endif; ?>
                            </div>

                            <h3 style="font-size: 1.15rem; margin-bottom: 8px;">
                                <a href="/lessons/<?= (int)$lesson['id'] ?>" style="color: var(--text-main); transition: color 0.15s ease;">
                                    <?= e($lesson['title']) ?>
                                </a>
                            </h3>

                            <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 16px;">
                                <?= e($lesson['learning_objectives']) ?>
                            </p>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 14px; border-top: 1px solid var(--border);">
                            <span style="font-size: 0.825rem; font-weight: 700; color: var(--primary);">
                                +<?= (int)$lesson['xp_reward'] ?> XP
                            </span>
                            <a href="/lessons/<?= (int)$lesson['id'] ?>" class="btn btn-outline btn-sm">
                                <?= $lesson['is_completed'] ? 'Pelajari Ulang →' : 'Mulai Pelajaran →' ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require dirname(__DIR__, 2) . '/layouts/footer.php'; ?>

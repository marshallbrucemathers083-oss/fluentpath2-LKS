<?php
// views/pages/exam/result.php - TOEFL Exam Result & Section Breakdown
$title = "Hasil Simulasi Ujian TOEFL";
require dirname(__DIR__, 2) . '/layouts/header.php';

$review = json_decode($attempt['answers_json'] ?? '[]', true) ?: [];
$durationMin = floor((int)$attempt['duration_seconds'] / 60);
$durationSec = (int)$attempt['duration_seconds'] % 60;
?>

<div class="reveal-on-scroll" style="margin-bottom: 24px;">
    <a href="/exam" style="font-size: 0.9rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 12px; transition: transform 0.15s ease;">
        ← Kembali ke Exam Lab
    </a>
    <h1 class="page-title">Hasil Simulasi Tes TOEFL</h1>
    <p class="page-subtitle">
        Selesai pada <?= date('d M Y, H:i', strtotime($attempt['completed_at'])) ?> &bull; Waktu Pengerjaan: <?= sprintf('%02d:%02d', $durationMin, $durationSec) ?>
    </p>
</div>

<!-- Score Highlight Card -->
<div class="card reveal-on-scroll" style="margin-bottom: 32px; text-align: center; padding: 40px 24px; border-top: 4px solid var(--primary);">
    <span class="badge badge-info" style="margin-bottom: 12px;">Unofficial Practice Score</span>
    <h2 style="font-size: 1.4rem; color: var(--text-muted); margin-bottom: 6px;">Total Skor Keseluruhan</h2>
    <div style="font-size: 3.5rem; font-weight: 800; color: var(--primary); line-height: 1; margin-bottom: 8px;">
        <?= (int)$attempt['total_score'] ?> <span style="font-size: 1.5rem; font-weight: 600; color: var(--text-muted);">/ 100</span>
    </div>

    <p style="font-size: 0.95rem; color: var(--text-muted); max-width: 540px; margin: 0 auto 24px;">
        <?php
            $ts = (int)$attempt['total_score'];
            if ($ts >= 85) {
                echo "Predikat Unggul! Penguasaan materi bahasa Inggris setara tingkat Proficient / High Advanced.";
            } elseif ($ts >= 70) {
                echo "Hasil Sangat Baik! Penguasaan materi bahasa Inggris setara tingkat Upper Intermediate.";
            } elseif ($ts >= 50) {
                echo "Hasil Cukup Baik! Anda memiliki fondasi yang kuat, terus tingkatkan latihan di materi yang masih keliru.";
            } else {
                echo "Tetap bersemangat! Gunakan tinjauan soal di bawah untuk mempelajari topik yang belum dikuasai.";
            }
        ?>
    </p>

    <!-- Section breakdown -->
    <div class="grid-3" style="max-width: 720px; margin: 0 auto 28px; text-align: left;">
        <div style="background: var(--bg-alt); padding: 18px; border-radius: var(--radius-md); border: 1px solid var(--border);">
            <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Structure & Grammar</div>
            <div style="font-size: 1.6rem; font-weight: 800; color: #a855f7; margin-top: 4px;">
                <?= (int)$attempt['structure_score'] ?> <span style="font-size: 0.9rem; font-weight: 600; color: var(--text-muted);">/ 35</span>
            </div>
        </div>

        <div style="background: var(--bg-alt); padding: 18px; border-radius: var(--radius-md); border: 1px solid var(--border);">
            <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Reading Comprehension</div>
            <div style="font-size: 1.6rem; font-weight: 800; color: var(--primary); margin-top: 4px;">
                <?= (int)$attempt['reading_score'] ?> <span style="font-size: 0.9rem; font-weight: 600; color: var(--text-muted);">/ 30</span>
            </div>
        </div>

        <div style="background: var(--bg-alt); padding: 18px; border-radius: var(--radius-md); border: 1px solid var(--border);">
            <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Listening Comprehension</div>
            <div style="font-size: 1.6rem; font-weight: 800; color: var(--success); margin-top: 4px;">
                <?= (int)$attempt['listening_score'] ?> <span style="font-size: 0.9rem; font-weight: 600; color: var(--text-muted);">/ 35</span>
            </div>
        </div>
    </div>

    <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
        <a href="/exam/take" class="btn btn-primary">Ulangi Simulasi Ujian</a>
        <a href="/exam" class="btn btn-secondary">Kembali ke Exam Lab</a>
    </div>
</div>

<!-- Detailed Question-by-Question Review -->
<div style="margin-bottom: 32px;">
    <h3 class="reveal-on-scroll" style="font-size: 1.3rem; margin-bottom: 16px;">Tinjauan Jawaban & Pembahasan Lengkap</h3>
    <div style="display: flex; flex-direction: column; gap: 16px;">
        <?php foreach ($review as $idx => $r): ?>
            <div class="card reveal-on-scroll" style="border-left: 4px solid <?= $r['is_correct'] ? 'var(--success)' : 'var(--danger)' ?>;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <span style="font-weight: 700; font-size: 0.9rem; color: var(--primary);">
                        Soal #<?= $idx + 1 ?> &bull; Bagian <?= e($r['skill']) ?>
                    </span>
                    <span class="badge <?= $r['is_correct'] ? 'badge-success' : 'badge-danger' ?>">
                        <?= $r['is_correct'] ? 'Benar' : 'Salah' ?>
                    </span>
                </div>

                <p style="font-weight: 600; font-size: 1rem; margin-bottom: 10px; color: var(--text-main);">
                    <?= e($r['prompt']) ?>
                </p>

                <div style="font-size: 0.925rem; margin-bottom: 6px;">
                    <strong>Jawaban Anda:</strong>
                    <span style="color: <?= $r['is_correct'] ? 'var(--success)' : 'var(--danger)' ?>; font-weight: 600;">
                        <?= e($r['submitted_answer'] ?: '(Tidak dijawab)') ?>
                    </span>
                </div>

                <?php if (!$r['is_correct']): ?>
                    <div style="font-size: 0.925rem; margin-bottom: 6px; color: var(--success); font-weight: 600;">
                        <strong>Kunci Jawaban yang Benar:</strong> <?= e($r['correct_answer']) ?>
                    </div>
                <?php endif; ?>

                <div class="passage-box" style="margin-top: 10px; margin-bottom: 0; font-size: 0.875rem;">
                    <strong>Penjelasan Ilmiah:</strong> <?= e($r['explanation']) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require dirname(__DIR__, 2) . '/layouts/footer.php'; ?>

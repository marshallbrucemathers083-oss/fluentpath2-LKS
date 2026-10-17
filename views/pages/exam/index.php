<?php
// views/pages/exam/index.php - Exam Lab Overview & Test History
$title = "Exam Lab — Simulasi Latihan TOEFL";
require dirname(__DIR__, 2) . '/layouts/header.php';
?>

<div class="reveal-on-scroll" style="margin-bottom: 24px;">
    <h1 class="page-title">Exam Lab (Simulasi TOEFL)</h1>
    <p class="page-subtitle">
        Uji kesiapan dan ketahanan tes bahasa Inggris Anda melalui simulasi tes objektif terstandarisasi dengan batasan waktu nyata.
    </p>
</div>

<!-- Important Unofficial Disclaimer Alert -->
<div class="alert alert-info reveal-on-scroll" style="border-left: 4px solid var(--primary); margin-bottom: 28px;">
    <span>ℹ️</span>
    <div style="font-size: 0.875rem; line-height: 1.5;">
        <strong>Pemberitahuan Resmi:</strong> Modul ini adalah simulasi latihan objektif mandiri (<em>Unofficial Practice Test</em>) yang dikembangkan secara orisinal oleh FluentPath. Kami tidak berafiliasi dengan ETS, dan skor yang dihasilkan merupakan estimasi latihan internal platform, bukan sertifikat TOEFL® resmi.
    </div>
</div>

<!-- Test Format Card -->
<div class="card reveal-on-scroll" style="margin-bottom: 36px; border-top: 4px solid var(--primary);">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 20px;">
        <div>
            <span class="badge badge-warning" style="margin-bottom: 6px;">Simulasi Tes Penuh</span>
            <h2 style="font-size: 1.35rem; margin-bottom: 4px;">Simulasi Ujian Objektif TOEFL (Komprehensif)</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">
                Durasi waktu: <strong>20 Menit (1200 detik)</strong> &bull; Total Skor Maksimal: <strong>100 Poin</strong>
            </p>
        </div>
        <div>
            <a href="/exam/take" class="btn btn-primary btn-lg">
                Mulai Ujian Sekarang →
            </a>
        </div>
    </div>

    <div class="grid-3" style="padding-top: 16px; border-top: 1px solid var(--border);">
        <div style="background: var(--bg-alt); padding: 16px; border-radius: var(--radius-md); border: 1px solid var(--border);">
            <div style="font-weight: 700; font-size: 0.95rem; margin-bottom: 4px; color: var(--primary);">
                Bagian 1: Structure & Written Expression
            </div>
            <p style="font-size: 0.825rem; color: var(--text-muted); margin: 0;">
                Menguji pemahaman sintaksis, inversi, preposisi, dan tata bahasa formal kalimat bahasa Inggris.
            </p>
        </div>

        <div style="background: var(--bg-alt); padding: 16px; border-radius: var(--radius-md); border: 1px solid var(--border);">
            <div style="font-weight: 700; font-size: 0.95rem; margin-bottom: 4px; color: var(--primary);">
                Bagian 2: Reading Comprehension
            </div>
            <p style="font-size: 0.825rem; color: var(--text-muted); margin: 0;">
                Membedah teks ilmiah dan akademis, menemukan ide pokok, referensi kata, dan makna tersirat.
            </p>
        </div>

        <div style="background: var(--bg-alt); padding: 16px; border-radius: var(--radius-md); border: 1px solid var(--border);">
            <div style="font-weight: 700; font-size: 0.95rem; margin-bottom: 4px; color: var(--primary);">
                Bagian 3: Listening Comprehension
            </div>
            <p style="font-size: 0.825rem; color: var(--text-muted); margin: 0;">
                Mendengarkan cuplikan kuliah dosen dan dialog akademis untuk menjawab pertanyaan spesifik.
            </p>
        </div>
    </div>
</div>

<!-- Exam History Table -->
<div class="card reveal-on-scroll" style="padding: 0; overflow: hidden;">
    <div style="padding: 18px 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 1.15rem; margin: 0;">Riwayat Ujian Anda</h3>
        <span style="font-size: 0.85rem; color: var(--text-muted);"><?= count($history) ?> Percobaan Tersimpan</span>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Waktu Ujian</th>
                    <th>Judul Simulasi</th>
                    <th style="text-align: center;">Structure</th>
                    <th style="text-align: center;">Reading</th>
                    <th style="text-align: center;">Listening</th>
                    <th style="text-align: right;">Total Skor</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($history)): ?>
                    <?php foreach ($history as $h): ?>
                        <tr>
                            <td style="font-size: 0.85rem; color: var(--text-muted);">
                                <?= date('d M Y, H:i', strtotime($h['completed_at'])) ?>
                            </td>
                            <td style="font-weight: 600;"><?= e($h['exam_title']) ?></td>
                            <td style="text-align: center;"><?= (int)$h['structure_score'] ?> / 35</td>
                            <td style="text-align: center;"><?= (int)$h['reading_score'] ?> / 30</td>
                            <td style="text-align: center;"><?= (int)$h['listening_score'] ?> / 35</td>
                            <td style="text-align: right; font-weight: 800; font-size: 1.05rem; color: var(--primary);">
                                <?= (int)$h['total_score'] ?> / <?= (int)$h['max_score'] ?>
                            </td>
                            <td style="text-align: center;">
                                <a href="/exam/result?id=<?= (int)$h['id'] ?>" class="btn btn-outline btn-sm">
                                    Tinjau Hasil
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 32px; color: var(--text-muted);">
                            Anda belum pernah mengambil ujian simulasi TOEFL. Klik "Mulai Ujian Sekarang" di atas untuk mencoba tes pertama Anda!
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require dirname(__DIR__, 2) . '/layouts/footer.php'; ?>

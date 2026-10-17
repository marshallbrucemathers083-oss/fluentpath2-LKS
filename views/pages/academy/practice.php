<?php
// views/pages/academy/practice.php - Interactive Academy Practice Session
$title = "Sesi Latihan Academy";
require dirname(__DIR__, 2) . '/layouts/header.php';
?>

<div class="reveal-on-scroll" style="margin-bottom: 24px;">
    <a href="/academy" style="font-size: 0.9rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 12px; transition: transform 0.15s ease;">
        ← Kembali ke Academy Hub
    </a>

    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <h1 class="page-title" style="margin-bottom: 4px;">
                <?= !empty($filters['mode']) && $filters['mode'] === 'review_zone' ? 'Zona Evaluasi: Review Soal Sebelumnya' : 'Latihan: ' . e($filters['skill'] ?? 'Campuran (Mixed)') ?>
            </h1>
            <p class="page-subtitle" style="margin-bottom: 0;">
                Jawab pertanyaan berikut dengan teliti. Skor akurasi dan penjelasan akan dievaluasi setelah sesi selesai.
            </p>
        </div>
        <div>
            <span class="badge badge-info" style="font-size: 0.85rem; padding: 6px 12px;">
                <?= count($questions) ?> Pertanyaan Tersedia
            </span>
        </div>
    </div>
</div>

<?php if (empty($questions)): ?>
    <div class="card reveal-on-scroll" style="text-align: center; padding: 48px 24px;">
        <div style="font-size: 3rem; margin-bottom: 16px;">🎉</div>
        <h2 style="font-size: 1.4rem; margin-bottom: 8px;">Tidak Ada Soal untuk Kategori Ini</h2>
        <p style="color: var(--text-muted); font-size: 0.95rem; max-width: 500px; margin: 0 auto 24px;">
            <?= !empty($filters['mode']) && $filters['mode'] === 'review_zone' ? 'Bagus sekali! Anda belum memiliki catatan jawaban salah yang perlu dievaluasi.' : 'Belum ada pertanyaan yang sesuai dengan kriteria filter yang Anda pilih.' ?>
        </p>
        <a href="/academy" class="btn btn-primary">Kembali ke Menu Academy</a>
    </div>
<?php else: ?>

    <!-- Practice Questions Container -->
    <div id="academy-questions-container">
        <form id="academy-session-form">
            <?= csrf_field() ?>

            <div style="display: flex; flex-direction: column; gap: 24px; margin-bottom: 32px;">
                <?php foreach ($questions as $idx => $q): ?>
                    <div class="card reveal-on-scroll" style="border-top: 3px solid var(--primary);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                            <span style="font-weight: 700; font-size: 0.9rem; color: var(--primary);">
                                Pertanyaan #<?= $idx + 1 ?> &bull; <?= e($q['skill']) ?>
                            </span>
                            <div style="display: flex; gap: 6px;">
                                <span class="badge badge-cefr" style="background: <?= get_cefr_color($q['level'] ?? 'B1') ?>;">Level <?= e($q['level']) ?></span>
                                <span class="badge" style="background: var(--bg-alt); color: var(--text-muted);"><?= e(ucfirst($q['difficulty'] ?? 'medium')) ?></span>
                            </div>
                        </div>

                        <!-- Reading Passage if present -->
                        <?php if (!empty($q['passage'])): ?>
                            <div class="passage-box">
                                <strong style="color: var(--text-muted); display: block; margin-bottom: 4px;">Teks Bacaan:</strong>
                                <?= nl2br(e($q['passage'])) ?>
                            </div>
                        <?php endif; ?>

                        <!-- Listening Audio Prompt if present -->
                        <?php if (!empty($q['audio_text'])): ?>
                            <div class="audio-box">
                                <span style="font-size: 0.875rem; color: var(--text-main); font-weight: 600;">Dengarkan Rekaman Suara:</span>
                                <button type="button" class="audio-play-btn" onclick="playEnglishAudio(<?= htmlspecialchars(json_encode($q['audio_text']), ENT_QUOTES, 'UTF-8') ?>, this)">
                                    🔊 Putar Audio
                                </button>
                            </div>
                        <?php endif; ?>

                        <p style="font-weight: 600; font-size: 1.05rem; margin-bottom: 16px; color: var(--text-main);">
                            <?= e($q['prompt']) ?>
                        </p>

                        <!-- Answer Options -->
                        <?php if (!empty($q['options'])): ?>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <?php foreach ($q['options'] as $opt): ?>
                                    <label class="option-label">
                                        <input type="radio" name="answers[<?= (int)$q['id'] ?>]" value="<?= e($opt['option_key']) ?>" required style="accent-color: var(--primary); width: 17px; height: 17px;">
                                        <span style="font-weight: 700; width: 20px; font-size: 0.9rem;"><?= e($opt['option_key']) ?>.</span>
                                        <span style="font-size: 0.925rem;"><?= e($opt['option_text']) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="form-group">
                                <input type="text" name="answers[<?= (int)$q['id'] ?>]" class="form-control" placeholder="Masukkan jawaban bahasa Inggris Anda di sini..." required>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 16px;">
                <a href="/academy" class="btn btn-secondary">Batalkan Sesi</a>
                <button type="submit" id="submit-session-btn" class="btn btn-primary btn-lg">
                    Selesai & Evaluasi Latihan
                </button>
            </div>
        </form>
    </div>

    <!-- Results Presentation Container (Shown after submission) -->
    <div id="academy-results-container" class="challenge-feedback-box" style="display: none;">
        <div class="card" style="margin-bottom: 24px; text-align: center; padding: 36px 20px;">
            <div style="font-size: 2.75rem; margin-bottom: 10px;">📊</div>
            <h2 style="font-size: 1.6rem; margin-bottom: 6px;">Ringkasan Hasil Latihan Academy</h2>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 24px;">
                Berikut evaluasi lengkap performa dan analisis materi yang perlu Anda tingkatkan.
            </p>

            <div class="grid-3" style="max-width: 600px; margin: 0 auto 24px;">
                <div style="background: var(--bg-alt); padding: 16px; border-radius: var(--radius-md); border: 1px solid var(--border);">
                    <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Akurasi</div>
                    <div id="res-accuracy" style="font-size: 1.8rem; font-weight: 800; color: var(--primary);">0%</div>
                </div>
                <div style="background: var(--bg-alt); padding: 16px; border-radius: var(--radius-md); border: 1px solid var(--border);">
                    <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Skor Jawaban</div>
                    <div id="res-correct" style="font-size: 1.8rem; font-weight: 800; color: var(--success);">0 / 0</div>
                </div>
                <div style="background: var(--bg-alt); padding: 16px; border-radius: var(--radius-md); border: 1px solid var(--border);">
                    <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">XP Diterima</div>
                    <div id="res-xp" style="font-size: 1.8rem; font-weight: 800; color: var(--warning);">+0 XP</div>
                </div>
            </div>

            <!-- Weak topics alert -->
            <div class="card-note-box" style="max-width: 600px; margin: 0 auto; text-align: left;">
                <strong style="color: var(--warning-text); font-size: 0.9rem; display: block; margin-bottom: 6px;">Topik yang Perlu Diperdalam:</strong>
                <ul id="res-weak-topics" style="margin-left: 20px; font-size: 0.875rem; line-height: 1.6;"></ul>
            </div>

            <div style="margin-top: 24px; display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                <a href="/academy/practice" class="btn btn-primary">Latihan Sekali Lagi</a>
                <a href="/academy" class="btn btn-secondary">Kembali ke Menu Academy</a>
            </div>
        </div>

        <h3 style="font-size: 1.25rem; margin-bottom: 16px;">Tinjauan Setiap Pertanyaan (Explanations)</h3>
        <div id="res-detailed-review"></div>
    </div>

    <script src="/assets/js/academy.js"></script>
<?php endif; ?>

<?php require dirname(__DIR__, 2) . '/layouts/footer.php'; ?>

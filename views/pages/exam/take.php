<?php
// views/pages/exam/take.php - Active Timed Exam Session
$title = "Ujian Sedang Berlangsung — Simulasi TOEFL";
require dirname(__DIR__, 2) . '/layouts/header.php';
?>

<!-- Sticky Exam Timer Header -->
<div style="position: sticky; top: 64px; z-index: 25; background: var(--header-bg); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-bottom: 2px solid var(--border); padding: 12px 24px; margin: -32px -32px 32px -32px; display: flex; justify-content: space-between; align-items: center; box-shadow: var(--shadow-sm); transition: var(--theme-transition);">
    <div>
        <span class="badge badge-warning" style="margin-right: 8px;">Ujian Sedang Berlangsung</span>
        <strong style="font-size: 0.95rem; color: var(--text-main);">Simulasi Tes Objektif TOEFL</strong>
    </div>

    <div style="display: flex; align-items: center; gap: 12px;">
        <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Sisa Waktu:</span>
        <div id="exam-timer-display" style="font-family: var(--font-mono); font-size: 1.5rem; font-weight: 800; color: var(--primary); background: var(--bg-alt); padding: 4px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border); transition: var(--theme-transition);">
            20:00
        </div>
    </div>
</div>

<form id="toefl-exam-form" data-duration="1200">
    <?= csrf_field() ?>
    <input type="hidden" name="duration_seconds" id="exam-duration-seconds" value="0">

    <div style="display: flex; flex-direction: column; gap: 28px; margin-bottom: 40px;">
        <?php foreach ($questions as $idx => $q): ?>
            <div class="card reveal-on-scroll" style="border-top: 3px solid <?= $q['skill'] === 'Reading' ? '#2563eb' : ($q['skill'] === 'Listening' ? '#10b981' : '#7c3aed') ?>;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <span style="font-weight: 700; font-size: 0.9rem; color: var(--primary);">
                        Soal #<?= $idx + 1 ?> &bull; Bagian <?= e($q['skill']) ?>
                    </span>
                    <span class="badge badge-info"><?= e($q['topic'] ?: $q['skill']) ?></span>
                </div>

                <!-- Reading passage if any -->
                <?php if (!empty($q['passage'])): ?>
                    <div class="passage-box">
                        <strong style="color: var(--text-muted); display: block; margin-bottom: 6px;">Teks Bacaan (Passage):</strong>
                        <?= nl2br(e($q['passage'])) ?>
                    </div>
                <?php endif; ?>

                <!-- Audio prompt if any -->
                <?php if (!empty($q['audio_text'])): ?>
                    <div class="audio-box">
                        <div>
                            <strong style="font-size: 0.9rem;">Audio Listening Comprehension:</strong>
                            <div style="font-size: 0.825rem; color: var(--text-muted);">Klik untuk memutar narasi audio.</div>
                        </div>
                        <button type="button" class="audio-play-btn" onclick="playEnglishAudio(<?= htmlspecialchars(json_encode($q['audio_text']), ENT_QUOTES, 'UTF-8') ?>, this)">
                            🔊 Putar Audio Percakapan
                        </button>
                    </div>
                <?php endif; ?>

                <p style="font-weight: 600; font-size: 1.05rem; margin-bottom: 16px; color: var(--text-main);">
                    <?= e($q['prompt']) ?>
                </p>

                <!-- Options -->
                <?php if (!empty($q['options'])): ?>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <?php foreach ($q['options'] as $opt): ?>
                            <label class="option-label">
                                <input type="radio" name="answers[<?= (int)$q['id'] ?>]" value="<?= e($opt['option_key']) ?>" required style="accent-color: var(--primary); width: 18px; height: 18px;">
                                <span style="font-weight: 700; width: 20px; font-size: 0.925rem;"><?= e($opt['option_key']) ?>.</span>
                                <span style="font-size: 0.95rem;"><?= e($opt['option_text']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <input type="text" name="answers[<?= (int)$q['id'] ?>]" class="form-control" placeholder="Jawaban singkat Anda..." required>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Submit Section -->
    <div class="card reveal-on-scroll" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div style="font-size: 0.9rem; color: var(--text-muted);">
            Pastikan seluruh soal telah Anda jawab sebelum menekan tombol kirim di samping.
        </div>
        <button type="submit" id="submit-exam-btn" class="btn btn-primary btn-lg">
            Kirimkan Seluruh Jawaban Ujian →
        </button>
    </div>
</form>

<script src="/assets/js/exam.js"></script>

<?php require dirname(__DIR__, 2) . '/layouts/footer.php'; ?>

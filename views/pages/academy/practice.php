<?php
$title = 'Sesi latihan';
require dirname(__DIR__, 2) . '/layouts/header.php';
$reviewMode = ($filters['mode'] ?? '') === 'review_zone';
$focus = $filters['skill'] ?? 'Campuran';
?>
<header class="practice-toolbar">
    <div><p class="eyebrow"><a href="/academy">Academy</a> · <?= $reviewMode ? 'Tinjau jawaban' : e($focus) ?></p><h1><?= $reviewMode ? 'Jawaban untuk ditinjau' : 'Sesi latihan' ?></h1><p class="muted">Jawaban Anda akan diperiksa setelah dikirim.</p></div>
    <?php if ($questions): ?><span><?= count($questions) ?> soal</span><?php endif; ?>
</header>

<?php if (!$questions): ?>
    <section class="surface content-section"><h2><?= $reviewMode ? 'Tidak ada jawaban salah untuk ditinjau' : 'Tidak ada soal yang cocok' ?></h2><p class="muted"><?= $reviewMode ? 'Kerjakan lebih banyak latihan dan jawaban yang perlu ditinjau akan tersimpan di sini.' : 'Ubah filter atau pilih keterampilan lain untuk menemukan soal.' ?></p><a class="button button-primary" href="/academy">Kembali ke Academy</a></section>
<?php else: ?>
    <div id="academy-questions-container">
        <form id="academy-session-form">
            <?= csrf_field() ?>
            <?php foreach ($questions as $index => $question): ?>
                <fieldset class="question-card">
                    <legend class="question-meta"><span>Soal <?= $index + 1 ?> · <?= e($question['skill']) ?></span><span><?= e($question['level']) ?> · <?= e(ucfirst($question['difficulty'] ?? 'medium')) ?></span></legend>
                    <?php if (!empty($question['passage'])): ?><div class="passage-box"><strong>Bacaan</strong><br><?= nl2br(e($question['passage'])) ?></div><?php endif; ?>
                    <?php if (!empty($question['audio_text'])): ?><div class="audio-box"><span>Audio latihan</span><button type="button" class="button button-secondary" onclick="playEnglishAudio(<?= htmlspecialchars(json_encode($question['audio_text']), ENT_QUOTES, 'UTF-8') ?>, this)">Putar audio</button></div><?php endif; ?>
                    <p class="question-prompt"><?= e($question['prompt']) ?></p>
                    <?php if (!empty($question['options'])): ?>
                        <div class="option-list"><?php foreach ($question['options'] as $option): ?><label class="option-label"><input type="radio" name="answers[<?= (int)$question['id'] ?>]" value="<?= e($option['option_key']) ?>" required><span><strong><?= e($option['option_key']) ?>.</strong> <?= e($option['option_text']) ?></span></label><?php endforeach; ?></div>
                    <?php else: ?>
                        <label class="form-group"><span>Jawaban</span><input type="text" name="answers[<?= (int)$question['id'] ?>]" placeholder="Ketik jawaban dalam bahasa Inggris" required></label>
                    <?php endif; ?>
                </fieldset>
            <?php endforeach; ?>
            <div class="session-actions"><a class="button button-secondary" href="/academy">Akhiri sesi</a><button type="submit" class="button button-primary" id="submit-session-btn">Kirim jawaban</button></div>
        </form>
    </div>

    <section id="academy-results-container" class="content-section" hidden>
        <p class="eyebrow">Hasil sesi</p><h2>Ringkasan latihan</h2>
        <div class="results-summary"><div><span>Akurasi</span><strong id="res-accuracy">0%</strong></div><div><span>Jawaban benar</span><strong id="res-correct">0 / 0</strong></div><div><span>XP</span><strong id="res-xp">0</strong></div></div>
        <h3>Topik untuk ditinjau</h3><ul id="res-weak-topics"></ul>
        <h3 class="content-section">Tinjauan jawaban</h3><div id="res-detailed-review"></div>
        <a class="button button-primary" href="/academy">Kembali ke Academy</a>
    </section>
    <script src="/assets/js/academy.js" defer></script>
<?php endif; ?>
<?php require dirname(__DIR__, 2) . '/layouts/footer.php'; ?>

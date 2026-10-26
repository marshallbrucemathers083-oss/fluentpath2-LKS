<?php $title = 'Sesi ujian'; require dirname(__DIR__, 2) . '/layouts/header.php'; $minutes = 20; ?>
<header class="exam-session-head">
    <div><p class="eyebrow"><a href="/exam">Latihan ujian</a> · Sesi berjalan</p><h1>Soal latihan</h1><p><?= count($questions) ?> soal · <?= $minutes ?> menit</p></div>
    <div class="timer-block"><span>Sisa waktu</span><strong id="exam-timer-display" role="timer" aria-live="off">20:00</strong></div>
</header>

<form id="exam-form" data-duration="<?= $minutes * 60 ?>">
    <?= csrf_field() ?><input type="hidden" name="duration_seconds" id="exam-duration-seconds" value="0">
    <nav class="question-index" aria-label="Navigasi soal"><strong>Ke soal:</strong><?php foreach ($questions as $index => $question): ?><a href="#question-<?= (int)$question['id'] ?>" aria-label="Soal <?= $index + 1 ?>"><?= $index + 1 ?></a><?php endforeach; ?></nav>
    <?php foreach ($questions as $index => $question): ?>
        <fieldset class="exam-question" id="question-<?= (int)$question['id'] ?>">
            <legend>Soal <?= $index + 1 ?> dari <?= count($questions) ?> · <?= e($question['skill']) ?></legend>
            <?php if (!empty($question['passage'])): ?><div class="passage-box"><strong>Bacaan</strong><br><?= nl2br(e($question['passage'])) ?></div><?php endif; ?>
            <?php if (!empty($question['audio_text'])): ?><div class="audio-box"><span>Audio latihan</span><button type="button" class="button button-secondary" onclick="playEnglishAudio(<?= htmlspecialchars(json_encode($question['audio_text']), ENT_QUOTES, 'UTF-8') ?>, this)">Putar audio</button></div><?php endif; ?>
            <p class="exam-prompt"><?= e($question['prompt']) ?></p>
            <?php if (!empty($question['options'])): ?>
                <div class="option-list"><?php foreach ($question['options'] as $option): ?><label class="exam-option"><input type="radio" name="answers[<?= (int)$question['id'] ?>]" value="<?= e($option['option_key']) ?>"><span><strong><?= e($option['option_key']) ?>.</strong> <?= e($option['option_text']) ?></span></label><?php endforeach; ?></div>
            <?php else: ?>
                <label class="form-group"><span>Jawaban</span><input type="text" name="answers[<?= (int)$question['id'] ?>]" autocomplete="off"></label>
            <?php endif; ?>
        </fieldset>
    <?php endforeach; ?>
    <section class="exam-submit"><div><h2>Sudah selesai?</h2><p>Periksa jawaban Anda. Soal yang belum dijawab dihitung sebagai salah.</p></div><button type="submit" id="submit-exam-btn" class="button button-primary">Kirim ujian</button></section>
</form>
<script src="/assets/js/exam.js" defer></script>
<?php require dirname(__DIR__, 2) . '/layouts/footer.php'; ?>

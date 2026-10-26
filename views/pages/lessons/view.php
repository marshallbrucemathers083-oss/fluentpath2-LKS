<?php
$title = $lesson['title'] . ' · ' . $lesson['level'];
require dirname(__DIR__, 2) . '/layouts/header.php';
?>
<article class="lesson-reader">
    <p class="breadcrumb"><a href="/lessons">Jalur belajar</a> / <?= e($lesson['level']) ?> / <?= e($lesson['skill']) ?></p>
    <header class="lesson-reader-head">
        <div><p class="eyebrow"><?= e($lesson['level']) ?> · <?= e($lesson['skill']) ?> · Pelajaran <?= (int)$lesson['order_num'] ?></p><h1><?= e($lesson['title']) ?></h1></div>
        <?php if ($lesson['is_completed']): ?>
            <span class="completion-state">Pelajaran selesai</span>
        <?php else: ?>
            <button type="button" id="mark-lesson-btn" class="button button-primary" data-lesson-id="<?= (int)$lesson['id'] ?>">Tandai selesai · +<?= (int)$lesson['xp_reward'] ?> XP</button>
        <?php endif; ?>
    </header>

    <div class="lesson-content">
        <section class="lesson-block lesson-objective"><p class="eyebrow">Tujuan belajar</p><p class="lesson-prose"><?= e($lesson['learning_objectives']) ?></p></section>
        <section class="lesson-block"><h2>Materi</h2><div class="lesson-prose"><?= e($lesson['explanation']) ?></div></section>
        <section class="lesson-block"><h2>Contoh</h2><div class="lesson-examples"><?= e($lesson['examples']) ?></div></section>
        <?php if (!empty($lesson['notes'])): ?><section class="lesson-block"><h2>Catatan</h2><div class="lesson-prose"><?= e($lesson['notes']) ?></div></section><?php endif; ?>
        <?php if (!empty($lesson['practice_guide'])): ?><section class="lesson-block"><h2>Panduan latihan</h2><div class="lesson-prose"><?= e($lesson['practice_guide']) ?></div></section><?php endif; ?>
        <?php if (!empty($lesson['further_reading'])): ?><section class="lesson-block"><h2>Bacaan lanjutan</h2><p><?= e($lesson['further_reading']) ?></p></section><?php endif; ?>
    </div>

    <section class="challenge-section" id="challenges">
        <header class="page-header"><p class="eyebrow">Periksa pemahaman</p><h2>Tantangan pelajaran</h2><p>Jawaban diperiksa dan penjelasannya ditampilkan setelah dikirim.</p></header>
        <?php if (!empty($lesson['challenges'])): ?>
            <?php foreach ($lesson['challenges'] as $index => $challenge): ?>
                <article class="challenge-card" id="challenge-card-<?= (int)$challenge['id'] ?>">
                    <div class="challenge-card-head"><span>Tantangan <?= $index + 1 ?> · <?= e(ucwords(str_replace('_', ' ', $challenge['type']))) ?></span><span><?php if ($challenge['user_solved']): ?>Selesai sebelumnya · <?php endif; ?>+<?= (int)$challenge['xp_reward'] ?> XP</span></div>
                    <?php if (!empty($challenge['passage'])): ?><div class="passage-box"><strong>Bahan bacaan</strong><br><?= nl2br(e($challenge['passage'])) ?></div><?php endif; ?>
                    <?php if (!empty($challenge['audio_text'])): ?><div class="audio-box"><span>Audio latihan mendengarkan</span><button type="button" class="button button-secondary" onclick="playEnglishAudio(<?= htmlspecialchars(json_encode($challenge['audio_text']), ENT_QUOTES, 'UTF-8') ?>, this)">Putar audio</button></div><?php endif; ?>
                    <p class="challenge-prompt"><?= e($challenge['prompt']) ?></p>
                    <form class="challenge-form" data-challenge-id="<?= (int)$challenge['id'] ?>">
                        <?= csrf_field() ?><input type="hidden" name="challenge_id" value="<?= (int)$challenge['id'] ?>">
                        <?php if (in_array($challenge['type'], ['multiple_choice', 'reading_comprehension', 'listening_comprehension'], true)): ?>
                            <div class="option-list"><?php foreach ($challenge['options'] as $option): ?><label class="option-label"><input type="radio" name="answer" value="<?= e($option['option_key']) ?>" required><span><strong><?= e($option['option_key']) ?>.</strong> <?= e($option['option_text']) ?></span></label><?php endforeach; ?></div>
                        <?php elseif ($challenge['type'] === 'sentence_order'): ?>
                            <p class="form-hint">Pilih kata untuk menyusun kalimat.</p><div class="sentence-order-box"><div class="target-sentence-area word-bank"></div><div class="word-bank"><?php $tokens = json_decode($challenge['correct_answer'], true) ?: []; shuffle($tokens); foreach ($tokens as $token): ?><button type="button" class="word-chip" data-word="<?= e($token) ?>"><?= e($token) ?></button><?php endforeach; ?></div><input type="hidden" name="answer" value="[]"></div>
                        <?php elseif ($challenge['type'] === 'vocab_match'): ?>
                            <?php $pairs = json_decode($challenge['correct_answer'], true) ?: []; ?>
                            <div class="match-list"><?php foreach ($pairs as $english => $definition): ?><label class="match-row"><strong><?= e($english) ?></strong><select class="form-control match-select" data-key="<?= e($english) ?>"><option value="">Pilih arti</option><?php foreach ($pairs as $choice): ?><option value="<?= e($choice) ?>"><?= e($choice) ?></option><?php endforeach; ?></select></label><?php endforeach; ?></div>
                            <input type="hidden" name="answer" id="vocab-match-answer-<?= (int)$challenge['id'] ?>" value="{}">
                            <script>document.addEventListener('DOMContentLoaded',()=>{const root=document.getElementById('challenge-card-<?= (int)$challenge['id'] ?>');const fields=root.querySelectorAll('.match-select');const output=document.getElementById('vocab-match-answer-<?= (int)$challenge['id'] ?>');const sync=()=>{const result={};fields.forEach(field=>{if(field.value)result[field.dataset.key]=field.value});output.value=JSON.stringify(result)};fields.forEach(field=>field.addEventListener('change',sync))});</script>
                        <?php else: ?>
                            <label class="form-group"><span>Jawaban Anda</span><input type="text" name="answer" placeholder="Ketik jawaban dalam bahasa Inggris" required></label>
                        <?php endif; ?>
                        <button type="submit" class="button button-primary">Kirim jawaban</button>
                    </form>
                    <div id="feedback-<?= (int)$challenge['id'] ?>" class="challenge-feedback-box" role="status" aria-live="polite" hidden></div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="empty-inline">Belum ada tantangan untuk pelajaran ini.</p>
        <?php endif; ?>
    </section>
</article>

<script src="/assets/js/challenge.js"></script>
<script>
document.getElementById('mark-lesson-btn')?.addEventListener('click', async event => {
    const button = event.currentTarget;
    if (button.disabled) return;
    button.disabled = true;
    button.setAttribute('aria-busy', 'true');
    button.textContent = 'Menyimpan…';
    const data = new FormData();
    data.append('csrf_token', <?= json_encode(csrf_token()) ?>);
    data.append('lesson_id', button.dataset.lessonId);
    try {
        const response = await fetch('/api/lesson_complete.php', {method: 'POST', body: data});
        const result = await response.json();
        if (!response.ok || !result.success) throw new Error(result.error || 'Pelajaran belum dapat diselesaikan.');
        button.replaceWith(Object.assign(document.createElement('span'), {className: 'completion-state', textContent: result.xp_awarded > 0 ? `Selesai · +${result.xp_awarded} XP` : 'Pelajaran selesai'}));
        window.showToast(result.xp_awarded > 0 ? `Pelajaran selesai. Anda mendapat ${result.xp_awarded} XP.` : 'Pelajaran selesai.', 'success');
    } catch (error) {
        button.disabled = false;
        button.removeAttribute('aria-busy');
        button.textContent = 'Coba lagi';
        window.showToast(error.message || 'Server tidak dapat dihubungi.', 'danger');
    }
});
</script>
<?php require dirname(__DIR__, 2) . '/layouts/footer.php'; ?>

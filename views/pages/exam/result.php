<?php
$title = 'Hasil ujian';
require dirname(__DIR__, 2) . '/layouts/header.php';
$review = json_decode($attempt['answers_json'] ?? '[]', true) ?: [];
$seconds = (int)$attempt['duration_seconds'];
?>
<header class="page-header"><p class="eyebrow"><a href="/exam">Latihan ujian</a> · <?= e(date('j M Y, H:i', strtotime($attempt['completed_at']))) ?></p><h1>Hasil sesi</h1><p><?= e($attempt['exam_title']) ?></p></header>

<section class="score-summary" aria-label="Ringkasan skor">
    <div class="score-total"><span>Skor keseluruhan</span><strong><?= (int)$attempt['total_score'] ?><small> / <?= (int)$attempt['max_score'] ?></small></strong><p><?= intdiv($seconds, 60) ?> menit <?= $seconds % 60 ?> detik</p></div>
    <div class="score-sections"><div>Structure<strong><?= (int)$attempt['structure_score'] ?>/35</strong></div><div>Reading<strong><?= (int)$attempt['reading_score'] ?>/30</strong></div><div>Listening<strong><?= (int)$attempt['listening_score'] ?>/35</strong></div></div>
</section>

<section class="content-section"><div class="section-header"><div><p class="eyebrow">Penjelasan</p><h2>Tinjauan soal</h2></div><a href="/exam" class="button button-secondary">Kembali ke ujian</a></div>
    <?php if (!$review): ?><p class="surface muted">Rincian jawaban tidak tersedia untuk sesi ini.</p><?php else: ?>
        <ol class="result-review"><?php foreach ($review as $index => $item): ?><li class="<?= !empty($item['is_correct']) ? 'answer-correct' : 'answer-incorrect' ?>"><h3>Soal <?= $index + 1 ?> · <?= e($item['skill']) ?> · <?= !empty($item['is_correct']) ? 'Benar' : 'Perlu ditinjau' ?></h3><p><?= e($item['prompt']) ?></p><p><strong>Jawaban Anda:</strong> <?= e($item['submitted_answer'] ?: '(Tidak dijawab)') ?></p><?php if (empty($item['is_correct'])): ?><p><strong>Jawaban benar:</strong> <?= e($item['correct_answer']) ?></p><?php endif; ?><p><?= e($item['explanation']) ?></p></li><?php endforeach; ?></ol>
    <?php endif; ?>
</section>
<?php require dirname(__DIR__, 2) . '/layouts/footer.php'; ?>

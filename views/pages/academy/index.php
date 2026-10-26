<?php
$title = 'Latihan Academy';
require dirname(__DIR__, 2) . '/layouts/header.php';
$skills = [
    ['Grammar', 'Tata bahasa dan susunan kalimat.'],
    ['Vocabulary', 'Kosakata dan pasangan kata.'],
    ['Reading', 'Pemahaman isi dan detail bacaan.'],
    ['Listening', 'Memahami konteks dan informasi lisan.'],
    ['Writing', 'Tulis jawaban dan tinjau dengan rubrik.'],
    ['Speaking', 'Latihan berbicara dengan panduan refleksi.'],
];
?>
<header class="page-header">
    <p class="eyebrow">Latihan mandiri</p><h1>Academy</h1>
    <p>Pilih keterampilan untuk membuka latihan. Soal objektif memberikan hasil dan penjelasan; menulis dan berbicara menggunakan panduan refleksi.</p>
</header>

<form class="academy-filters" action="/academy/practice" method="get">
    <label class="form-group"><span>Topik atau kata kunci</span><input type="search" name="q" placeholder="Contoh: past tense" aria-label="Cari topik latihan"></label>
    <label class="form-group"><span>Keterampilan</span><select name="skill"><option value="">Semua</option><?php foreach (['Grammar','Vocabulary','Reading','Listening'] as $skill): ?><option value="<?= e($skill) ?>"><?= e($skill) ?></option><?php endforeach; ?></select></label>
    <label class="form-group"><span>Tingkat</span><select name="level"><option value="">Semua tingkat</option><?php foreach (['A1','A2','B1','B2','C1','C2'] as $level): ?><option value="<?= e($level) ?>"><?= e($level) ?></option><?php endforeach; ?></select></label>
    <label class="form-group"><span>Kesulitan</span><select name="difficulty"><option value="">Semua</option><?php foreach (['easy'=>'Mudah','medium'=>'Sedang','hard'=>'Sulit'] as $value=>$label): ?><option value="<?= e($value) ?>"><?= e($label) ?></option><?php endforeach; ?></select></label>
    <button class="button button-primary" type="submit">Cari soal</button>
</form>

<section class="content-section" aria-labelledby="skills-title">
    <div class="section-header"><div><p class="eyebrow">Pilih fokus</p><h2 id="skills-title">Latihan berdasarkan keterampilan</h2></div></div>
    <div class="academy-list">
        <?php foreach ($skills as [$skill, $description]): ?>
            <a class="academy-item" href="/practice/<?= e($skill) ?>"><strong><?= e($skill) ?></strong><span><?= e($description) ?></span><span class="text-link">Buka →</span></a>
        <?php endforeach; ?>
    </div>
</section>

<p class="form-hint content-section">Jawaban terbuka untuk Writing dan Speaking tidak dinilai otomatis. Gunakan rubrik untuk meninjau pekerjaan Anda.</p>
<?php require dirname(__DIR__, 2) . '/layouts/footer.php'; ?>

<?php
$title = 'Belajar bahasa Inggris dengan terarah';
require dirname(__DIR__) . '/layouts/header.php';
$levels = [
    ['A1', 'Pemula'], ['A2', 'Dasar'], ['B1', 'Menengah'],
    ['B2', 'Menengah atas'], ['C1', 'Mahir'], ['C2', 'Lanjut'],
];
?>
<section class="hero">
    <div class="hero-copy">
        <p class="eyebrow">Kurikulum bahasa Inggris A1–C2</p>
        <h1>Suruh speaking cowomu yang cuman modal bilang "SOK INGGRIS" itu.</h1>
        <p>Ikuti pelajaran berurutan, kerjakan latihan, dan tinjau jawaban dengan penjelasan yang jelas.</p>
        <div class="button-row">
            <a class="button button-primary" href="/register">Mulai belajar</a>
            <a class="button button-secondary" href="#curriculum">Lihat kurikulum</a>
        </div>
    </div>
    <aside class="hero-aside" aria-label="Isi pembelajaran">
        <h2>Yang bisa Anda lakukan</h2>
        <ul>
            <li>Belajar tata bahasa dan kosakata melalui pelajaran terstruktur.</li>
            <li>Latihan membaca, mendengarkan, menulis, dan berbicara.</li>
            <li>Menyimpan progres, XP, dan hasil latihan di akun Anda.</li>
        </ul>
    </aside>
</section>

<section id="curriculum" class="content-section">
    <div class="section-header"><div><p class="eyebrow">Jalur belajar</p><h2>Enam tingkat CEFR</h2></div><a class="text-link" href="/register">Buat akun untuk mulai →</a></div>
    <ol class="level-strip"><?php foreach ($levels as [$code, $name]): ?><li><strong><?= e($code) ?></strong><span><?= e($name) ?></span></li><?php endforeach; ?></ol>
</section>

<section id="how-it-works" class="home-method content-section">
    <div><p class="eyebrow">Alur belajar</p><h2>Materi, latihan, lalu ulasan</h2></div>
    <div><p>Mulai dari pelajaran sesuai tingkat Anda. Tantangan pada setiap pelajaran memeriksa jawaban dan menjelaskan alasannya. Academy menyediakan sesi latihan berdasarkan keterampilan, tingkat, dan topik.</p><p>Untuk menulis dan berbicara, gunakan panduan dan rubrik refleksi. Ujian latihan menyimpan skor dan tinjauan jawaban.</p><a href="/register" class="text-link">Daftar dan mulai belajar →</a></div>
</section>

<section class="home-board" aria-labelledby="weekly-learners-title">
    <div><p class="eyebrow">Aktivitas belajar</p><h2 id="weekly-learners-title">Peringkat minggu ini</h2><p class="muted">XP dihitung dari aktivitas belajar yang tercatat.</p><a href="/register" class="text-link">Ikut belajar →</a></div>
    <div class="rank-list">
        <?php if (!empty($weeklyRankings)): ?>
            <?php foreach (array_slice($weeklyRankings, 0, 5) as $row): ?>
                <div class="rank-row"><span><?= (int)$row['rank'] ?></span><strong><?= e($row['username']) ?></strong><span><?= number_format((int)$row['weekly_xp']) ?> XP</span></div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="muted">Belum ada aktivitas minggu ini.</p>
        <?php endif; ?>
    </div>
</section>
<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>

<?php $title = 'Latihan ujian'; require dirname(__DIR__, 2) . '/layouts/header.php'; ?>
<header class="page-header">
    <p class="eyebrow">Latihan mandiri</p><h1>Latihan ujian</h1>
    <p>Kerjakan sesi soal objektif dengan batas waktu, lalu lihat skor per bagian dan penjelasan jawaban. Materi ini merupakan latihan FluentPath, bukan ujian resmi.</p>
</header>

<section class="exam-intro" aria-label="Informasi sesi ujian">
    <div><p class="eyebrow">Satu sesi · 20 menit</p><h2>Structure, reading, dan listening</h2><p>Jawaban yang belum diisi dihitung sebagai salah. Anda dapat meninjau soal sebelum mengirim.</p></div>
    <a class="button button-primary" href="/exam/take">Mulai latihan</a>
</section>

<section class="content-section" aria-labelledby="exam-history-title">
    <div class="section-header"><div><p class="eyebrow">Riwayat akun</p><h2 id="exam-history-title">Hasil sebelumnya</h2></div></div>
    <?php if (!$history): ?>
        <p class="surface muted">Belum ada sesi ujian yang selesai.</p>
    <?php else: ?>
        <div class="table-wrap"><table><thead><tr><th>Tanggal</th><th>Skor</th><th>Structure</th><th>Reading</th><th>Listening</th><th>Waktu</th><th></th></tr></thead><tbody>
            <?php foreach ($history as $attempt): ?><tr><td><?= e(date('j M Y', strtotime($attempt['completed_at']))) ?></td><td><strong><?= (int)$attempt['total_score'] ?>/<?= (int)$attempt['max_score'] ?></strong></td><td><?= (int)$attempt['structure_score'] ?></td><td><?= (int)$attempt['reading_score'] ?></td><td><?= (int)$attempt['listening_score'] ?></td><td><?= (int)floor((int)$attempt['duration_seconds'] / 60) ?> menit</td><td><a href="/exam/result?id=<?= (int)$attempt['id'] ?>">Tinjau</a></td></tr><?php endforeach; ?>
        </tbody></table></div>
    <?php endif; ?>
</section>
<?php require dirname(__DIR__, 2) . '/layouts/footer.php'; ?>

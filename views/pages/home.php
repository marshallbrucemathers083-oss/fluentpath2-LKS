<?php
// views/pages/home.php - Public Landing Page
$title = "Kuasai Bahasa Inggris Terstruktur dari Pemula hingga Mahir";
require dirname(__DIR__) . '/layouts/header.php';
?>

<!-- Hero Section -->
<section class="hero-section reveal-on-scroll">
    <div style="max-width: 860px; margin: 0 auto;">
        <span class="badge badge-info" style="margin-bottom: 16px; font-size: 0.85rem; padding: 5px 12px;">
            Standar CEFR Internasional • A1 hingga C2
        </span>
        <h1 style="font-size: 2.75rem; letter-spacing: -0.02em; margin-bottom: 18px; line-height: 1.2;">
            Langkah Pasti Menguasai Bahasa Inggris Secara Terstruktur
        </h1>
        <p style="font-size: 1.15rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 32px; max-width: 700px; margin-left: auto; margin-right: auto;">
            FluentPath mendampingi perjalanan belajar bahasa Inggris Anda dari tingkat dasar hingga kecakapan profesional dengan kurikulum modul sistematis, tantangan interaktif, Academy latihan mandiri, dan simulasi tes objektif TOEFL.
        </p>
        <div style="display: flex; gap: 14px; justify-content: center; flex-wrap: wrap;">
            <a href="/register" class="btn btn-primary btn-lg">
                Mulai Belajar Sekarang (Gratis)
            </a>
            <a href="/#levels" class="btn btn-secondary btn-lg">
                Jelajahi Kurikulum CEFR
            </a>
        </div>
    </div>
</section>

<!-- Overview of Learning Levels -->
<section id="levels" style="padding: 60px 24px; max-width: 1200px; margin: 0 auto;">
    <div class="reveal-on-scroll" style="text-align: center; margin-bottom: 40px;">
        <h2 style="font-size: 2rem; margin-bottom: 10px;">Jalur Pembelajaran Standar CEFR</h2>
        <p style="color: var(--text-muted); font-size: 1rem;">
            Enam tingkatan kemampuan yang dirancang sistematis untuk membangun fondasi hingga penguasaan tingkat lanjut.
        </p>
    </div>

    <div class="grid-3">
        <!-- A1 -->
        <div class="card card-interactive reveal-on-scroll">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <span class="badge badge-cefr" style="background: <?= get_cefr_color('A1') ?>">Level A1</span>
                <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">Beginner</span>
            </div>
            <h3 style="font-size: 1.15rem; margin-bottom: 8px;">Fondasi Dasar & Percakapan Harian</h3>
            <p style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.5;">
                Memahami frasa sederhana sehari-hari, kata ganti, perkenalan diri, dan tata bahasa Present Simple mendasar.
            </p>
        </div>

        <!-- A2 -->
        <div class="card card-interactive reveal-on-scroll">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <span class="badge badge-cefr" style="background: <?= get_cefr_color('A2') ?>">Level A2</span>
                <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">Elementary</span>
            </div>
            <h3 style="font-size: 1.15rem; margin-bottom: 8px;">Cerita Masa Lalu & Rutinitas</h3>
            <p style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.5;">
                Menguasai Past Simple reguler dan ireguler, membaca teks naratif pendek, dan memahami petunjuk waktu.
            </p>
        </div>

        <!-- B1 -->
        <div class="card card-interactive reveal-on-scroll">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <span class="badge badge-cefr" style="background: <?= get_cefr_color('B1') ?>">Level B1</span>
                <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">Intermediate</span>
            </div>
            <h3 style="font-size: 1.15rem; margin-bottom: 8px;">Pengalaman Hidup & Komunikasi Mandiri</h3>
            <p style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.5;">
                Membedakan Present Perfect dan Past Simple, mendengarkan dialog situasi kerja, dan mengemukakan opini.
            </p>
        </div>

        <!-- B2 -->
        <div class="card card-interactive reveal-on-scroll">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <span class="badge badge-cefr" style="background: <?= get_cefr_color('B2') ?>">Level B2</span>
                <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">Upper Intermediate</span>
            </div>
            <h3 style="font-size: 1.15rem; margin-bottom: 8px;">Argumen Kompleks & Kalimat Pengandaian</h3>
            <p style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.5;">
                Menguasai Conditional Sentences (Tipe 2, 3, dan Campuran), struktur esai persuasif, serta discourse markers formal.
            </p>
        </div>

        <!-- C1 -->
        <div class="card card-interactive reveal-on-scroll">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <span class="badge badge-cefr" style="background: <?= get_cefr_color('C1') ?>">Level C1</span>
                <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">Advanced</span>
            </div>
            <h3 style="font-size: 1.15rem; margin-bottom: 8px;">Inversi Retoris & Kosakata Akademik</h3>
            <p style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.5;">
                Struktur inversi gramatikal untuk penekanan formal, kolokasi akademik tingkat tinggi, dan argumen bernuansa presisi.
            </p>
        </div>

        <!-- C2 -->
        <div class="card card-interactive reveal-on-scroll">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <span class="badge badge-cefr" style="background: <?= get_cefr_color('C2') ?>">Level C2</span>
                <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">Proficient</span>
            </div>
            <h3 style="font-size: 1.15rem; margin-bottom: 8px;">Kecakapan Fasih & Analisis Gaya Bahasa</h3>
            <p style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.5;">
                Membedah ironi, subteks prosa literatur rumit, modulasi register diplomatik, dan penguasaan idiom tingkat tinggi.
            </p>
        </div>
    </div>
</section>

<!-- Overview of Key Modules -->
<section id="features" style="padding: 60px 24px; background: var(--bg-card); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div class="reveal-on-scroll" style="text-align: center; margin-bottom: 40px;">
            <h2 style="font-size: 2rem; margin-bottom: 10px;">Fitur Lengkap Pendukung Pembelajaran</h2>
            <p style="color: var(--text-muted); font-size: 1rem;">
                Dirancang untuk menstimulasi keterlibatan aktif dan latihan berkelanjutan.
            </p>
        </div>

        <div class="grid-4">
            <div class="card card-interactive reveal-on-scroll">
                <div style="font-size: 2.2rem; margin-bottom: 12px; transition: transform 0.2s ease;">📚</div>
                <h3 style="font-size: 1.1rem; margin-bottom: 6px;">Learning Path</h3>
                <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5;">
                    Kurikulum terstruktur mencakup 6 keterampilan utama: Grammar, Vocabulary, Reading, Listening, Writing, dan Speaking.
                </p>
            </div>

            <div class="card card-interactive reveal-on-scroll">
                <div style="font-size: 2.2rem; margin-bottom: 12px; transition: transform 0.2s ease;">⚡</div>
                <h3 style="font-size: 1.1rem; margin-bottom: 6px;">Interactive Challenges</h3>
                <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5;">
                    Uji pemahaman seketika melalui pilihan ganda, susun kalimat, koreksi tata bahasa, kuis audio, dan pencocokan kosakata.
                </p>
            </div>

            <div class="card card-interactive reveal-on-scroll">
                <div style="font-size: 2.2rem; margin-bottom: 12px; transition: transform 0.2s ease;">🏋️</div>
                <h3 style="font-size: 1.1rem; margin-bottom: 6px;">Academy Gym</h3>
                <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5;">
                    Pusat latihan mandiri dengan filter level dan topik, dilengkapi <em>Review Zone</em> untuk melatih soal yang pernah salah.
                </p>
            </div>

            <div class="card card-interactive reveal-on-scroll">
                <div style="font-size: 2.2rem; margin-bottom: 12px; transition: transform 0.2s ease;">📝</div>
                <h3 style="font-size: 1.1rem; margin-bottom: 6px;">Exam Lab (TOEFL)</h3>
                <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5;">
                    Simulasi tes objektif gaya TOEFL dengan pengatur waktu nyata, evaluasi otomatis, dan rekap skor per bagian.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Leaderboard Preview Section -->
<section id="leaderboard" style="padding: 60px 24px; max-width: 900px; margin: 0 auto;">
    <div class="reveal-on-scroll" style="text-align: center; margin-bottom: 30px;">
        <span class="badge badge-warning" style="margin-bottom: 8px;">Papan Peringkat Pelajar</span>
        <h2 style="font-size: 1.75rem;">Top Pelajar Minggu Ini</h2>
        <p style="color: var(--text-muted); font-size: 0.95rem;">
            Dapatkan XP dari setiap modul, kuis, dan latihan untuk menempati posisi teratas!
        </p>
    </div>

    <div class="card reveal-on-scroll" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 80px; text-align: center;">Peringkat</th>
                        <th>Nama Pengguna</th>
                        <th style="text-align: right;">XP Mingguan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($weeklyRankings)): ?>
                        <?php foreach (array_slice($weeklyRankings, 0, 5) as $rank): ?>
                            <tr>
                                <td style="text-align: center; font-weight: 700; color: <?= $rank['rank'] === 1 ? '#d97706' : ($rank['rank'] === 2 ? '#64748b' : ($rank['rank'] === 3 ? '#b45309' : 'inherit')) ?>;">
                                    <?= $rank['rank'] === 1 ? '🥇' : ($rank['rank'] === 2 ? '🥈' : ($rank['rank'] === 3 ? '🥉' : '#' . $rank['rank'])) ?>
                                </td>
                                <td style="font-weight: 600;"><?= e($rank['username']) ?></td>
                                <td style="text-align: right; font-weight: 700; color: var(--primary);">
                                    <?= number_format((int)$rank['weekly_xp']) ?> XP
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 30px; color: var(--text-muted);">
                                Belum ada perolehan XP di minggu ini. Jadilah yang pertama belajar!
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="reveal-on-scroll" style="text-align: center; margin-top: 32px;">
        <a href="/register" class="btn btn-primary btn-lg">Daftar Akun dan Ikuti Tantangan</a>
    </div>
</section>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>

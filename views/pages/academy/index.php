<?php
// views/pages/academy/index.php - Academy Practice Hub
$title = "Academy — Pusat Latihan Mandiri";
require dirname(__DIR__, 2) . '/layouts/header.php';
?>

<div class="reveal-on-scroll" style="margin-bottom: 24px;">
    <h1 class="page-title">FluentPath Academy</h1>
    <p class="page-subtitle">
        Pusat latihan intensif mandiri untuk memperkuat tata bahasa, kosakata, membaca, dan mendengarkan.
    </p>
</div>

<!-- Review Zone Special Banner if user has mistakes to review -->
<div class="card card-banner-danger reveal-on-scroll" style="margin-bottom: 28px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                <span class="badge" style="background: var(--danger); color: #ffffff;">Fitur Unggulan</span>
                <span class="badge badge-warning">Review Zone</span>
            </div>
            <h2 style="font-size: 1.3rem; margin-bottom: 6px; color: var(--danger-text);">Zona Evaluasi Soal (Review Zone)</h2>
            <p style="color: var(--text-muted); font-size: 0.925rem; max-width: 680px; line-height: 1.5; margin: 0;">
                Sistem secara otomatis mengumpulkan soal-soal yang pernah Anda jawab kurang tepat dalam latihan sebelumnya agar Anda dapat melatihnya kembali hingga tuntas.
            </p>
        </div>
        <div>
            <a href="/academy/practice?mode=review_zone" class="btn btn-primary" style="background: var(--danger); border-color: var(--danger);">
                Mulai Review Zone →
            </a>
        </div>
    </div>
</div>

<!-- Academy Practice Gym Modules -->
<div style="margin-bottom: 32px;">
    <h2 class="reveal-on-scroll" style="font-size: 1.4rem; margin-bottom: 16px;">Pilih Bidang Latihan (Gym Modules)</h2>
    <div class="grid-3">
        <!-- Grammar Gym -->
        <div class="card card-interactive reveal-on-scroll" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="font-size: 2.2rem; margin-bottom: 10px; transition: transform 0.2s ease;">🏋️‍♂️</div>
                <h3 style="font-size: 1.15rem; margin-bottom: 6px;">Grammar Gym</h3>
                <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 16px;">
                    Latih tenses, modal verbs, kalimat pasif, inversion, dan struktur kalimat formal dari level A1 hingga C2.
                </p>
            </div>
            <a href="/academy/practice?skill=Grammar" class="btn btn-outline btn-sm">Mulai Latihan Tata Bahasa →</a>
        </div>

        <!-- Vocab Practice -->
        <div class="card card-interactive reveal-on-scroll" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="font-size: 2.2rem; margin-bottom: 10px; transition: transform 0.2s ease;">📖</div>
                <h3 style="font-size: 1.15rem; margin-bottom: 6px;">Vocabulary Practice</h3>
                <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 16px;">
                    Perkaya perbendaharaan kosakata, sinonim presisi, phrasal verbs, dan kolokasi akademik tingkat lanjut.
                </p>
            </div>
            <a href="/academy/practice?skill=Vocabulary" class="btn btn-outline btn-sm">Mulai Latihan Kosakata →</a>
        </div>

        <!-- Reading Practice -->
        <div class="card card-interactive reveal-on-scroll" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="font-size: 2.2rem; margin-bottom: 10px; transition: transform 0.2s ease;">📰</div>
                <h3 style="font-size: 1.15rem; margin-bottom: 6px;">Reading Practice</h3>
                <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 16px;">
                    Latih kemampuan skimming, scanning, membedah subteks, serta memahami ide pokok teks bacaan ilmiah dan umum.
                </p>
            </div>
            <a href="/academy/practice?skill=Reading" class="btn btn-outline btn-sm">Mulai Latihan Membaca →</a>
        </div>

        <!-- Listening Practice -->
        <div class="card card-interactive reveal-on-scroll" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="font-size: 2.2rem; margin-bottom: 10px; transition: transform 0.2s ease;">🎧</div>
                <h3 style="font-size: 1.15rem; margin-bottom: 6px;">Listening Practice</h3>
                <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 16px;">
                    Dengarkan audio penutur bahasa Inggris, pahami intonasi, petunjuk konteks, dan tangkap detail penting percakapan.
                </p>
            </div>
            <a href="/academy/practice?skill=Listening" class="btn btn-outline btn-sm">Mulai Latihan Mendengarkan →</a>
        </div>

        <!-- Mixed Practice -->
        <div class="card card-interactive reveal-on-scroll" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="font-size: 2.2rem; margin-bottom: 10px; transition: transform 0.2s ease;">🎲</div>
                <h3 style="font-size: 1.15rem; margin-bottom: 6px;">Mixed Question Practice</h3>
                <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 16px;">
                    Kombinasi acak pertanyaan dari berbagai bidang dan tingkat kesulitan untuk menguji ketangkasan komprehensif Anda.
                </p>
            </div>
            <a href="/academy/practice?skill=Mixed" class="btn btn-outline btn-sm">Mulai Latihan Campuran →</a>
        </div>

        <!-- Custom Filter Practice -->
        <div class="card card-interactive reveal-on-scroll" style="background: var(--bg-alt); display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="font-size: 2.2rem; margin-bottom: 10px; transition: transform 0.2s ease;">⚙️</div>
                <h3 style="font-size: 1.15rem; margin-bottom: 6px;">Kustomisasi Latihan</h3>
                <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 16px;">
                    Tentukan sendiri tingkat CEFR dan tingkat kesulitan yang ingin Anda uji dalam satu sesi latihan.
                </p>
            </div>
            <form action="/academy/practice" method="GET" style="display: flex; flex-direction: column; gap: 8px;">
                <select name="level" class="form-control" style="font-size: 0.85rem; padding: 6px 10px;">
                    <option value="">Semua Level CEFR</option>
                    <option value="A1">A1 - Beginner</option>
                    <option value="A2">A2 - Elementary</option>
                    <option value="B1">B1 - Intermediate</option>
                    <option value="B2">B2 - Upper Intermediate</option>
                    <option value="C1">C1 - Advanced</option>
                    <option value="C2">C2 - Proficient</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Mulai Sesi Kustom</button>
            </form>
        </div>
    </div>
</div>

<?php require dirname(__DIR__, 2) . '/layouts/footer.php'; ?>

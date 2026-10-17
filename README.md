# FluentPath — Platform Pembelajaran Bahasa Inggris Terstruktur

FluentPath adalah platform pembelajaran bahasa Inggris berbasis web yang terstruktur menurut standar CEFR (A1, A2, B1, B2, C1, C2), dilengkapi modul tantangan interaktif, Academy latihan mandiri, simulasi ujian TOEFL objektif dengan timer, papan peringkat mingguan & all-time, serta sistem pencapaian lencana (badges).

Platform ini dibangun menggunakan teknologi ringan native tanpa framework berat:
- **Backend:** Native PHP 8+ (Session-based auth, PDO prepared statements, modular structure)
- **Database:** MariaDB / MySQL (Skema relasional ternormalisasi, transaksi atomik, pencegahan farming XP)
- **Frontend:** Vanilla HTML5, CSS3 kustom modern, dan JavaScript vanilla (Web Speech API untuk audio listening)
- **Tanpa Docker, tanpa Laravel, tanpa React/Vue, tanpa dependensi Composer/npm yang tidak perlu.**

---

## 1. Persyaratan Sistem
- Linux (Arch Linux atau distribusi lainnya)
- PHP 8.0+ dengan ekstensi `pdo_mysql` aktif
- Server MariaDB atau MySQL lokal

---

## 2. Langkah Inisialisasi Database (MariaDB / MySQL)

### Langkah Cepat (Satu Perintah Terminal):
Jalankan perintah berikut di terminal Anda:
```bash
sudo mariadb < database/setup.sql && sudo mariadb fluentpath < database/schema.sql && sudo mariadb fluentpath < database/seed.sql
```
*(Atau gunakan `sudo mysql` jika perintah mariadb menggunakan alias mysql).*

---

### Langkah Manual Langkah-demi-Langkah:

1. **Buka MariaDB sebagai root/admin:**
   ```bash
   sudo mariadb
   # atau
   sudo mysql
   ```

2. **Buat Database dan User Khusus Aplikasi:**
   Ketik perintah SQL berikut:
   ```sql
   CREATE DATABASE IF NOT EXISTS fluentpath CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

   CREATE USER IF NOT EXISTS 'fluentpath_user'@'localhost' IDENTIFIED BY 'fluentpath_secret_2026';
   CREATE USER IF NOT EXISTS 'fluentpath_user'@'127.0.0.1' IDENTIFIED BY 'fluentpath_secret_2026';

   GRANT ALL PRIVILEGES ON fluentpath.* TO 'fluentpath_user'@'localhost';
   GRANT ALL PRIVILEGES ON fluentpath.* TO 'fluentpath_user'@'127.0.0.1';

   FLUSH PRIVILEGES;
   EXIT;
   ```

3. **Impor Skema Relasional dan Data Awal (Starter Seed):**
   ```bash
   sudo mariadb fluentpath < database/schema.sql
   sudo mariadb fluentpath < database/seed.sql
   ```

4. **Konfigurasi File Kredensial `.env`:**
   Pastikan file `.env` di root direktori proyek memiliki konfigurasi berikut:
   ```ini
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_NAME=fluentpath
   DB_USER=fluentpath_user
   DB_PASS=fluentpath_secret_2026

   APP_NAME="FluentPath"
   APP_ENV=development
   APP_URL=http://localhost:8000
   ```

5. **Uji Koneksi Database Melalui CLI:**
   Jalankan script verifikasi koneksi bawaan:
   ```bash
   php bin/test_db.php
   ```
   Jika sukses, Anda akan melihat status `[OK] PDO connection established successfully!` dan daftar tabel beserta jumlah baris datanya.

---

## 3. Menjalankan Website Secara Lokal

Jalankan PHP built-in web server menggunakan router script yang telah disediakan:
```bash
php -S localhost:8000 router.php
```

Buka peramban (browser) Anda di:
👉 **[http://localhost:8000](http://localhost:8000)**

---

## 4. Akun Percobaan Bawaan (Starter Seed Accounts)

Anda dapat langsung masuk menggunakan akun pengujian yang telah terdaftar, atau mendaftarkan akun baru melalui halaman registrasi:

| Username | Password | Keterangan |
| :--- | :--- | :--- |
| `budi_santoso` | `password123` | Level A2 (190 XP, Peringkat #1 Mingguan) |
| `siti_aminah` | `password123` | Level B1 (165 XP, Peringkat #2 Mingguan) |
| `kevin_wijaya` | `password123` | Level B2 (115 XP, Peringkat #3 Mingguan) |
| `dian_lestari` | `password123` | Level A1 (75 XP, Peringkat #4 Mingguan) |

---

## 5. Fitur Utama Platform

1. **Jalur Pembelajaran CEFR (A1 — C2):**
   - 12 modul starter komprehensif mencakup Grammar, Vocabulary, Reading, Listening, Writing, dan Speaking.
   - Dilengkapi tujuan belajar, penjelasan mendalam, contoh otentik, catatan kaidah, dan panduan latihan.

2. **Tantangan Interaktif Multi-Tipe:**
   - Multiple Choice
   - Fill-in-the-Blank
   - Sentence Ordering (Drag & click interactive chips)
   - Grammar Correction
   - Reading Comprehension
   - Vocabulary Matching
   - Listening Comprehension (didukung Web Speech API audio narator)
   - Short Written Answer
   - *Validasi server-side penuh dengan sistem anti-farming XP.*

3. **Academy Gym:**
   - Grammar Gym, Vocabulary Practice, Reading Practice, Listening Practice, Mixed Practice.
   - **Review Zone:** Ruang latihan khusus yang otomatis menghimpun soal-soal yang pernah Anda jawab salah sebelumnya.
   - Evaluasi akurasi, feedback, dan analisis materi yang perlu ditingkatkan.

4. **Exam Lab (Simulasi TOEFL Objektif):**
   - Simulasi tes objektif terstandarisasi dengan batasan waktu nyata (timer mundur 20 menit).
   - Penilaian komprehensif 3 bagian: *Structure & Written Expression*, *Reading Comprehension*, dan *Listening Comprehension*.
   - Evaluasi otomatis, rekapitulasi skor per seksi, dan peninjauan kunci jawaban beserta penjelasan ilmiah.

5. **Papan Peringkat (Leaderboard):**
   - Klasemen Mingguan (dihitung otomatis berdasarkan XP minggu kalender berjalan).
   - Klasemen Sepanjang Masa (All-Time).
   - Sorotan otomatis pada posisi peringkat pengguna yang sedang masuk.

6. **Profil & Lencana Pencapaian:**
   - Pelacakan streak belajar harian berturut-turut.
   - Penguasaan kompetensi per keterampilan bahasa.
   - Sistem lencana (First Lesson, First Challenge, Five Lessons, 7-Day Streak, First Exam, Precision Learner).

---

## 6. Struktur Direktori Proyek

```
fluentpath/
├── app/
│   ├── Auth.php             # Autentikasi, session regeneration, register/login/logout, CSRF
│   ├── Database.php         # PDO singleton dengan penanganan koneksi & logging error
│   ├── Helpers.php          # View helpers, csrf_token(), e(), flash messages, redirect
│   ├── Models/
│   │   ├── User.php         # Statistik pengguna, level CEFR, progress bar
│   │   ├── Lesson.php       # Kurikulum CEFR, detail modul, rekomendasi pelajaran
│   │   ├── Challenge.php    # Evaluasi server-side multi-tipe kuis & anti-farming XP
│   │   ├── Academy.php      # Latihan Gym, Review Zone, evaluasi sesi
│   │   ├── Exam.php         # Mesin simulasi TOEFL objektif & rekap nilai
│   │   ├── Leaderboard.php  # Peringkat mingguan & all-time dengan tie-breaking stabil
│   │   └── Achievement.php  # Pengecekan & pembukaan lencana pencapaian otomatis
│   └── Services/
│       ├── XpService.php    # Mutasi XP berbasis transaksi & event_key unik
│       └── StreakService.php# Perhitungan streak belajar harian berturut-turut
├── config/
│   ├── app.php              # Pengaturan aplikasi & CEFR level
│   └── database.php         # Loader file .env & konfigurasi PDO
├── database/
│   ├── setup.sql            # Pembuatan database fluentpath & user dedicated
│   ├── schema.sql           # Skema tabel relasional (users, lessons, challenges, dll.)
│   └── seed.sql             # Materi pelajaran A1-C2, soal tantangan, user demo, & XP
├── bin/
│   ├── test_db.php          # Alat pengujian koneksi database via CLI
│   └── migrate.php          # Runner migrasi & seeder via PDO
├── public/
│   ├── index.php            # Front controller & router URL
│   ├── api/                 # Endpoint AJAX (challenge, lesson complete, academy, exam)
│   └── assets/
│       ├── css/style.css    # Desain CSS kustom modern & responsif
│       └── js/              # Logika frontend vanilla (challenge, academy, exam, audio)
├── views/
│   ├── layouts/             # Header, footer, sidebar navigasi
│   ├── pages/               # Tampilan halaman landing, auth, dashboard, modul, exam
│   └── partials/            # Alert notifikasi flash
├── router.php               # Skrip router untuk PHP built-in server
├── .env.example
├── .env
├── .gitignore
└── README.md
```

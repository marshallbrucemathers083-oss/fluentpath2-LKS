<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Belum Dikonfigurasi - FluentPath</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <script>
        (function() {
            var saved = localStorage.getItem('fluentpath_theme');
            var systemDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            var theme = (saved === 'dark' || saved === 'light') ? saved : (systemDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <style>
        .error-container {
            max-width: 640px;
            margin: 60px auto;
            padding: 32px;
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-md);
        }
        .error-badge {
            display: inline-block;
            background: var(--danger-light);
            color: var(--danger-text);
            font-size: 0.85rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: var(--radius-sm);
            margin-bottom: 16px;
        }
        .code-box {
            background: #0f172a;
            color: #f8fafc;
            padding: 16px;
            border-radius: var(--radius-sm);
            font-family: var(--font-mono);
            font-size: 0.9rem;
            overflow-x: auto;
            margin: 16px 0;
            border: 1px solid var(--border);
        }
    </style>
</head>
<body style="background: var(--bg-main); padding: 20px;">
    <div class="error-container">
        <div class="error-badge">Status Koneksi: Terputus</div>
        <h1 style="font-size: 1.5rem; margin-top: 0; color: var(--text-main);">Koneksi Database Memerlukan Inisialisasi</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6;">
            Aplikasi FluentPath belum dapat terhubung ke server MariaDB / MySQL. Hal ini biasanya terjadi jika database atau user belum dibuat di MariaDB lokal.
        </p>

        <h3 style="font-size: 1.05rem; margin-top: 24px; color: var(--text-main);">Langkah Inisialisasi Cepat di Terminal:</h3>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Buka terminal dan jalankan perintah berikut:</p>
        
        <div class="code-box">
sudo mariadb &lt; database/setup.sql<br>
sudo mariadb fluentpath &lt; database/schema.sql<br>
sudo mariadb fluentpath &lt; database/seed.sql
        </div>

        <p style="color: var(--text-muted); font-size: 0.875rem;">
            Setelah itu, muat ulang halaman ini (Refresh).
        </p>

        <div style="margin-top: 24px; border-top: 1px solid var(--border); padding-top: 16px; display: flex; justify-content: space-between; align-items: center;">
            <a href="/" class="btn btn-primary" style="padding: 8px 16px; font-weight: 500;">Coba Muat Ulang</a>
            <span style="font-size: 0.8rem; color: var(--text-muted);">FluentPath • Environment: <?= htmlspecialchars($_ENV['APP_ENV'] ?? 'development') ?></span>
        </div>
    </div>
</body>
</html>

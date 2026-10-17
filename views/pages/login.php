<?php
// views/pages/login.php - User Login Page
$title = "Masuk ke Akun Anda";
require dirname(__DIR__) . '/layouts/header.php';
?>

<div style="max-width: 440px; margin: 40px auto; padding: 0 16px;">
    <div class="card reveal-on-scroll" style="padding: 32px;">
        <div style="text-align: center; margin-bottom: 24px;">
            <div class="brand-icon" style="margin: 0 auto 12px; width: 44px; height: 44px; font-size: 1.4rem;">F</div>
            <h1 style="font-size: 1.6rem; margin-bottom: 6px;">Masuk ke FluentPath</h1>
            <p style="color: var(--text-muted); font-size: 0.925rem;">
                Lanjutkan perjalanan belajar bahasa Inggris Anda
            </p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <span>⚠️</span>
                <div><?= e($error) ?></div>
            </div>
        <?php endif; ?>

        <form action="/login" method="POST" onsubmit="const b=this.querySelector('button[type=submit]'); b.disabled=true; b.textContent='Memverifikasi...';">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="username" class="form-label">Nama Pengguna (Username)</label>
                <input type="text" id="username" name="username" class="form-control" 
                       value="<?= e($oldUsername ?? '') ?>" required autofocus autocomplete="username">
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Kata Sandi</label>
                <input type="password" id="password" name="password" class="form-control" required autocomplete="current-password">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 11px; margin-top: 8px;">
                Masuk
            </button>
        </form>

        <div style="text-align: center; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border); font-size: 0.9rem; color: var(--text-muted);">
            Belum memiliki akun? <a href="/register" style="font-weight: 600;">Daftar sekarang</a>
        </div>
    </div>
</div>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>

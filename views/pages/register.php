<?php
// views/pages/register.php - User Registration Page
$title = "Daftar Akun Baru";
require dirname(__DIR__) . '/layouts/header.php';
?>

<div style="max-width: 480px; margin: 40px auto; padding: 0 16px;">
    <div class="card reveal-on-scroll" style="padding: 32px;">
        <div style="text-align: center; margin-bottom: 24px;">
            <div class="brand-icon" style="margin: 0 auto 12px; width: 44px; height: 44px; font-size: 1.4rem;">F</div>
            <h1 style="font-size: 1.6rem; margin-bottom: 6px;">Daftar Akun FluentPath</h1>
            <p style="color: var(--text-muted); font-size: 0.925rem;">
                Akses seluruh modul kurikulum CEFR, Academy, dan simulasi TOEFL
            </p>
        </div>

        <form action="/register" method="POST" onsubmit="const b=this.querySelector('button[type=submit]'); b.disabled=true; b.textContent='Mendaftarkan Akun...';">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="username" class="form-label">Nama Pengguna (Username)</label>
                <input type="text" id="username" name="username" class="form-control" 
                       value="<?= e($oldUsername ?? '') ?>" required autofocus autocomplete="username"
                       placeholder="Contoh: alex_learner">
                <?php if (!empty($errors['username'])): ?>
                    <div class="form-error"><?= e($errors['username']) ?></div>
                <?php endif; ?>
                <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;">
                    Hanya huruf, angka, dan garis bawah (3-30 karakter).
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Kata Sandi</label>
                <input type="password" id="password" name="password" class="form-control" required autocomplete="new-password"
                       placeholder="Minimal 6 karakter">
                <?php if (!empty($errors['password'])): ?>
                    <div class="form-error"><?= e($errors['password']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password_confirm" class="form-label">Konfirmasi Kata Sandi</label>
                <input type="password" id="password_confirm" name="password_confirm" class="form-control" required autocomplete="new-password"
                       placeholder="Ulangi kata sandi Anda">
                <?php if (!empty($errors['password_confirm'])): ?>
                    <div class="form-error"><?= e($errors['password_confirm']) ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 11px; margin-top: 10px;">
                Buat Akun dan Mulai Belajar
            </button>
        </form>

        <div style="text-align: center; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border); font-size: 0.9rem; color: var(--text-muted);">
            Sudah memiliki akun? <a href="/login" style="font-weight: 600;">Masuk di sini</a>
        </div>
    </div>
</div>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>

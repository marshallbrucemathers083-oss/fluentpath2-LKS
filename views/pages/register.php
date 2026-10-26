<?php $title = 'Daftar akun'; require dirname(__DIR__) . '/layouts/header.php'; ?>
<section class="auth-layout">
    <div class="auth-panel">
        <p class="eyebrow">Mulai belajar</p><h1>Buat akun</h1><p>Simpan progres pelajaran, latihan, dan pencapaian Anda.</p>
        <form action="/register" method="post" class="form-stack">
            <?= csrf_field() ?>
            <label class="form-group" for="username"><span>Nama pengguna</span><input type="text" id="username" name="username" value="<?= e($oldUsername ?? '') ?>" autocomplete="username" placeholder="Contoh: alex_learner" required autofocus><?php if (!empty($errors['username'])): ?><span class="form-error"><?= e($errors['username']) ?></span><?php endif; ?><span class="form-hint">3–30 karakter, huruf, angka, dan garis bawah.</span></label>
            <label class="form-group" for="password"><span>Kata sandi</span><input type="password" id="password" name="password" autocomplete="new-password" placeholder="Minimal 6 karakter" required><?php if (!empty($errors['password'])): ?><span class="form-error"><?= e($errors['password']) ?></span><?php endif; ?></label>
            <label class="form-group" for="password_confirm"><span>Ulangi kata sandi</span><input type="password" id="password_confirm" name="password_confirm" autocomplete="new-password" placeholder="Ulangi kata sandi" required><?php if (!empty($errors['password_confirm'])): ?><span class="form-error"><?= e($errors['password_confirm']) ?></span><?php endif; ?></label>
            <button type="submit" class="button button-primary form-submit">Buat akun</button>
        </form>
        <p class="auth-footer">Sudah punya akun? <a href="/login">Masuk</a></p>
    </div>
</section>
<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>

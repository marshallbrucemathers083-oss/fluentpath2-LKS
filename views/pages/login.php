<?php $title = 'Masuk'; require dirname(__DIR__) . '/layouts/header.php'; ?>
<section class="auth-layout">
    <div class="auth-panel">
        <p class="eyebrow">Akun FluentPath</p><h1>Masuk</h1><p>Lanjutkan pelajaran dan latihan Anda.</p>
        <?php if (!empty($error)): ?><div class="alert alert-danger" role="alert"><?= e($error) ?></div><?php endif; ?>
        <form action="/login" method="post" class="form-stack">
            <?= csrf_field() ?>
            <label class="form-group" for="username"><span>Nama pengguna</span><input type="text" id="username" name="username" value="<?= e($oldUsername ?? '') ?>" autocomplete="username" placeholder="Nama pengguna" required autofocus></label>
            <label class="form-group" for="password"><span>Kata sandi</span><input type="password" id="password" name="password" autocomplete="current-password" placeholder="Kata sandi" required></label>
            <button type="submit" class="button button-primary form-submit">Masuk</button>
        </form>
        <p class="auth-footer">Belum punya akun? <a href="/register">Daftar</a></p>
    </div>
</section>
<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>

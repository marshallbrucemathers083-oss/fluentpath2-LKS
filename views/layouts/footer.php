<?php
// views/layouts/footer.php - Global Footer Layout
$isAuth = is_authenticated();
?>
<?php if ($isAuth): ?>
        </main>
        <footer style="padding: 20px 32px; border-top: 1px solid var(--border); background: var(--bg-card); color: var(--text-muted); font-size: 0.825rem; display: flex; justify-content: space-between; align-items: center; transition: var(--theme-transition);">
            <div>&copy; <?= date('Y') ?> FluentPath. Platform Belajar Bahasa Inggris Mandiri.</div>
            <div>Standar CEFR A1 — C2 • TOEFL Practice Simulation</div>
        </footer>
    </div>
</div>
<?php else: ?>
    </main>
    <footer class="public-footer">
        <div class="footer-container">
            <div>
                <div class="brand-logo" style="margin-bottom: 8px;">
                    <div class="brand-icon">F</div>
                    <span>FluentPath</span>
                </div>
                <p style="max-width: 420px; font-size: 0.875rem; color: var(--text-muted);">
                    Platform pembelajaran bahasa Inggris interaktif berbasis standar CEFR Eropa dari pemula (A1) hingga mahir (C2), dilengkapi tantangan latihan, Academy, dan simulasi TOEFL.
                </p>
            </div>
            <div>
                <h4 style="font-size: 0.95rem; margin-bottom: 10px;">Fitur Utama</h4>
                <ul style="list-style: none; font-size: 0.875rem; line-height: 2;">
                    <li><a href="/#levels">Jenjang CEFR A1 - C2</a></li>
                    <li><a href="/login">Academy Independent Gym</a></li>
                    <li><a href="/login">Simulasi Ujian TOEFL</a></li>
                    <li><a href="/#leaderboard">Papan Peringkat Mingguan</a></li>
                </ul>
            </div>
            <div>
                <h4 style="font-size: 0.95rem; margin-bottom: 10px;">Autentikasi</h4>
                <ul style="list-style: none; font-size: 0.875rem; line-height: 2;">
                    <li><a href="/login">Masuk ke Akun</a></li>
                    <li><a href="/register">Daftar Akun Baru</a></li>
                </ul>
            </div>
        </div>
        <div style="max-width: 1200px; margin: 32px auto 0; padding-top: 20px; border-top: 1px solid var(--border); font-size: 0.8rem; text-align: center;">
            &copy; <?= date('Y') ?> FluentPath. Dibangun dengan PHP Native 8 & MariaDB. Seluruh hak cipta dilindungi.
        </div>
    </footer>
<?php endif; ?>

<script src="/assets/js/app.js"></script>
</body>
</html>

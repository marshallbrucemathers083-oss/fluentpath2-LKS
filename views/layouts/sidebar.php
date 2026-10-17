<?php
// views/layouts/sidebar.php - Authenticated User Sidebar
$currentUri = $_SERVER['REQUEST_URI'];
$currentUser = current_user();
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <a href="/dashboard" class="brand-logo">
            <div class="brand-icon">F</div>
            <span>FluentPath</span>
        </a>
    </div>

    <nav class="sidebar-nav">
        <a href="/dashboard" class="nav-item <?= str_starts_with($currentUri, '/dashboard') ? 'active' : '' ?>">
            <span class="icon">📊</span>
            <span>Dashboard</span>
        </a>
        <a href="/lessons" class="nav-item <?= str_starts_with($currentUri, '/lessons') ? 'active' : '' ?>">
            <span class="icon">📚</span>
            <span>Learning Path</span>
        </a>
        <a href="/academy" class="nav-item <?= str_starts_with($currentUri, '/academy') ? 'active' : '' ?>">
            <span class="icon">🏋️</span>
            <span>Academy</span>
        </a>
        <a href="/exam" class="nav-item <?= str_starts_with($currentUri, '/exam') ? 'active' : '' ?>">
            <span class="icon">📝</span>
            <span>Exam Lab (TOEFL)</span>
        </a>
        <a href="/leaderboard" class="nav-item <?= str_starts_with($currentUri, '/leaderboard') ? 'active' : '' ?>">
            <span class="icon">🏆</span>
            <span>Klasemen</span>
        </a>
        <a href="/profile" class="nav-item <?= str_starts_with($currentUri, '/profile') ? 'active' : '' ?>">
            <span class="icon">👤</span>
            <span>Profil & Lencana</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-profile-badge">
            <div class="avatar-circle">
                <?= strtoupper(substr($currentUser['username'] ?? 'U', 0, 1)) ?>
            </div>
            <div style="overflow: hidden;">
                <div style="font-weight: 700; font-size: 0.875rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                    <?= e($currentUser['username'] ?? 'Learner') ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-muted);">Pelajar Aktif</div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 6px;">
            <button type="button" class="theme-toggle-btn" aria-label="Ganti Tema" title="Ganti Mode Terang / Gelap" style="width: 32px; height: 32px; font-size: 0.95rem;">
                <span class="icon-sun">☀️</span>
                <span class="icon-moon">🌙</span>
            </button>
            <a href="/logout" title="Keluar" style="color: var(--text-muted); font-size: 1.1rem; padding: 4px 6px; transition: color 0.15s ease;">
                🚪
            </a>
        </div>
    </div>
</aside>

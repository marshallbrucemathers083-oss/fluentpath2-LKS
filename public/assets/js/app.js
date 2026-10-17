// public/assets/js/app.js - FluentPath Core Client Scripts & Dynamic Micro-interactions

/**
 * 1. Theme Management (Dark Mode & Light Mode)
 */
const THEME_KEY = 'fluentpath_theme';

function getPreferredTheme() {
    const stored = localStorage.getItem(THEME_KEY);
    if (stored === 'dark' || stored === 'light') {
        return stored;
    }
    // Respect OS system preference
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        return 'dark';
    }
    return 'light';
}

function applyTheme(theme, save = true) {
    document.documentElement.setAttribute('data-theme', theme);
    if (save) {
        localStorage.setItem(THEME_KEY, theme);
    }

    // Update aria labels and tooltips on toggle buttons
    const toggles = document.querySelectorAll('.theme-toggle-btn');
    toggles.forEach(btn => {
        const isDark = theme === 'dark';
        btn.setAttribute('aria-label', isDark ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap');
        btn.setAttribute('title', isDark ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap');
    });

    window.dispatchEvent(new CustomEvent('fluentpath:themechange', { detail: { theme } }));
}

function toggleTheme() {
    const current = document.documentElement.getAttribute('data-theme') || getPreferredTheme();
    const next = current === 'dark' ? 'light' : 'dark';
    applyTheme(next, true);
}

// Listen for OS system theme changes if user has not set a preference manually
if (window.matchMedia) {
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        if (!localStorage.getItem(THEME_KEY)) {
            applyTheme(e.matches ? 'dark' : 'light', false);
        }
    });
}

// Immediately synchronize theme attribute before paint
if (typeof document !== 'undefined' && document.documentElement) {
    applyTheme(getPreferredTheme(), false);
}

/**
 * 2. Toast Notification System
 */
function ensureToastContainer() {
    let container = document.getElementById('fluentpath-toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'fluentpath-toast-container';
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    return container;
}

window.showToast = function(message, type = 'info', icon = null, duration = 4000) {
    const container = ensureToastContainer();
    const toast = document.createElement('div');
    toast.className = `toast-item toast-${type}`;

    const defaultIcon = type === 'success' ? '✓' : (type === 'xp' ? '⚡' : (type === 'danger' ? '⚠️' : 'ℹ️'));
    const displayIcon = icon || defaultIcon;

    toast.innerHTML = `
        <span style="font-size: 1.2rem; flex-shrink: 0;">${displayIcon}</span>
        <div style="flex-grow: 1; font-weight: 500;">${message}</div>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('toast-leave');
        setTimeout(() => toast.remove(), 320);
    }, duration);
};

/**
 * 3. Button Loading Helper
 */
window.setButtonLoading = function(button, isLoading, loadingText = null) {
    if (!button) return;
    if (isLoading) {
        button.dataset.originalHtml = button.innerHTML;
        button.disabled = true;
        button.classList.add('is-loading');
        if (loadingText) {
            button.innerHTML = loadingText;
        }
    } else {
        if (button.dataset.originalHtml) {
            button.innerHTML = button.dataset.originalHtml;
        }
        button.disabled = false;
        button.classList.remove('is-loading');
    }
};

/**
 * 4. Native Web Speech API for English Listening Practice
 */
function playEnglishAudio(text, btnElement = null) {
    if (!('speechSynthesis' in window)) {
        alert('Browser Anda tidak mendukung Web Speech Audio. Silakan gunakan Chrome/Edge/Firefox terbaru.');
        return;
    }

    window.speechSynthesis.cancel(); // Stop any pending speech

    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = 'en-US';
    utterance.rate = 0.95; // Slightly slower, crystal-clear enunciation

    // Select natural English voice if available
    const voices = window.speechSynthesis.getVoices();
    const enVoice = voices.find(v => v.lang.startsWith('en') && (v.name.includes('Natural') || v.name.includes('Google') || v.name.includes('US')));
    if (enVoice) {
        utterance.voice = enVoice;
    }

    if (btnElement) {
        const originalText = btnElement.innerHTML;
        btnElement.innerHTML = '🔊 Memutar audio...';
        btnElement.disabled = true;
        btnElement.style.opacity = '0.85';

        const resetBtn = () => {
            btnElement.innerHTML = originalText;
            btnElement.disabled = false;
            btnElement.style.opacity = '1';
        };

        utterance.onend = resetBtn;
        utterance.onerror = resetBtn;
    }

    window.speechSynthesis.speak(utterance);
}

/**
 * 5. Initialize DOM-dependent interactions
 */
document.addEventListener('DOMContentLoaded', () => {
    // A. Apply theme and bind buttons
    applyTheme(getPreferredTheme(), false);
    document.querySelectorAll('.theme-toggle-btn').forEach(btn => {
        btn.addEventListener('click', toggleTheme);
    });

    // B. Auto-dismiss flash alerts with smooth collapse
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.4s ease, transform 0.4s ease, margin 0.4s ease, max-height 0.4s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 400);
        }, 5000);
    });

    // C. Scroll Animations using IntersectionObserver
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const revealElements = document.querySelectorAll('.reveal-on-scroll');

    if (prefersReducedMotion) {
        revealElements.forEach(el => el.classList.add('is-revealed'));
    } else if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-revealed');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.08,
            rootMargin: '0px 0px -30px 0px',
        });

        revealElements.forEach((el, index) => {
            // Apply subtle stagger if not already specified
            if (!el.className.includes('stagger-') && el.parentElement) {
                const siblings = Array.from(el.parentElement.children).filter(c => c.classList.contains('reveal-on-scroll'));
                if (siblings.length > 1) {
                    const idx = siblings.indexOf(el);
                    const staggerClass = `stagger-${Math.min((idx % 6) + 1, 6)}`;
                    el.classList.add(staggerClass);
                }
            }
            observer.observe(el);
        });
    } else {
        revealElements.forEach(el => el.classList.add('is-revealed'));
    }

    // D. Animate Progress Bars when visible
    const progressBars = document.querySelectorAll('.progress-fill');
    if ('IntersectionObserver' in window && !prefersReducedMotion) {
        const progressObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const fill = entry.target;
                    const targetWidth = fill.style.width || fill.getAttribute('data-width') || '0%';
                    fill.style.width = '0%';
                    requestAnimationFrame(() => {
                        fill.style.width = targetWidth;
                    });
                    progressObserver.unobserve(fill);
                }
            });
        }, { threshold: 0.2 });

        progressBars.forEach(bar => progressObserver.observe(bar));
    }

    // E. Smooth Scroll with Sticky Header Offset for Anchor Links
    document.querySelectorAll('a[href^="#"], a[href^="/#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            const hash = href.includes('#') ? href.substring(href.indexOf('#')) : null;
            if (!hash || hash === '#') return;

            // If it's on the same page
            const target = document.querySelector(hash);
            if (target && (href.startsWith('#') || window.location.pathname === '/')) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                // Update history without page jump
                if (history.pushState) {
                    history.pushState(null, null, hash);
                }
            }
        });
    });

    // F. Micro-interaction: Form input active polish
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('focus', () => {
            input.parentElement?.classList.add('input-focused');
        });
        input.addEventListener('blur', () => {
            input.parentElement?.classList.remove('input-focused');
        });
    });
});

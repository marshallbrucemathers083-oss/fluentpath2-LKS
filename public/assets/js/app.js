window.showToast = function (message, type = 'info', _icon = null, duration = 4200) {
    let region = document.getElementById('site-notices');
    if (!region) {
        region = document.createElement('div');
        region.id = 'site-notices';
        region.className = 'toast-region';
        region.setAttribute('aria-live', 'polite');
        region.setAttribute('aria-relevant', 'additions');
        document.body.append(region);
    }

    while (region.children.length >= 4) {
        const oldest = region.firstElementChild;
        window.clearTimeout(oldest.dismissTimer);
        oldest.remove();
    }
    const notice = document.createElement('div');
    notice.className = `toast toast-${type}`;
    notice.setAttribute('role', type === 'danger' ? 'alert' : 'status');
    const text = document.createElement('span');
    text.textContent = String(message);
    const dismiss = document.createElement('button');
    dismiss.type = 'button';
    dismiss.className = 'toast-dismiss';
    dismiss.setAttribute('aria-label', 'Tutup notifikasi');
    dismiss.textContent = '×';
    const remove = () => {
        window.clearTimeout(notice.dismissTimer);
        notice.classList.add('is-leaving');
        window.setTimeout(() => notice.remove(), 160);
    };
    dismiss.addEventListener('click', remove);
    notice.append(text, dismiss);
    region.append(notice);
    notice.dismissTimer = window.setTimeout(remove, Math.max(1800, duration));
};

window.setButtonLoading = function (button, loading, loadingText = 'Please wait…') {
    if (!button) return;
    if (loading) {
        if (!button.hasAttribute('data-loading')) {
            button.dataset.label = button.textContent;
            button.dataset.wasDisabled = String(button.disabled);
        }
        button.dataset.loading = 'true';
        button.setAttribute('aria-busy', 'true');
        button.textContent = loadingText;
        button.disabled = true;
    } else if (button.hasAttribute('data-loading')) {
        button.textContent = button.dataset.label || button.textContent;
        button.disabled = button.dataset.wasDisabled === 'true';
        button.removeAttribute('aria-busy');
        delete button.dataset.loading;
        delete button.dataset.label;
        delete button.dataset.wasDisabled;
    }
};

window.playEnglishAudio = function (text, button = null) {
    if (!('speechSynthesis' in window)) {
        window.alert('Audio playback is not available in this browser.');
        return;
    }
    window.speechSynthesis.cancel();
    const utterance = new SpeechSynthesisUtterance(String(text));
    utterance.lang = 'en-US';
    utterance.rate = 0.95;
    const voice = window.speechSynthesis.getVoices().find(item => item.lang.startsWith('en'));
    if (voice) utterance.voice = voice;
    if (button) {
        const original = button.textContent;
        button.disabled = true;
        button.setAttribute('aria-busy', 'true');
        button.textContent = 'Playing…';
        const reset = () => {
            button.disabled = false;
            button.removeAttribute('aria-busy');
            button.textContent = original;
        };
        utterance.onend = reset;
        utterance.onerror = reset;
    }
    window.speechSynthesis.speak(utterance);
};

document.addEventListener('DOMContentLoaded', () => {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const revealSelector = [
        '.page-header', '.section-header', '.hero-copy', '.hero-aside',
        '.dashboard-welcome', '.practice-toolbar', '.lesson-block', '.level-section',
        '.next-lesson', '.progress-list', '.activity-list', '.home-method',
        '.home-board', '.academy-item', '.profile-facts', '.achievement-list',
        '.site-footer .footer-inner', '.review-item'
    ].join(',');

    const revealBatch = root => {
        if (!(root instanceof Element || root instanceof Document)) return;
        if (root instanceof Element && root.matches(revealSelector)) prepareReveal(root);
        root.querySelectorAll?.(revealSelector).forEach(prepareReveal);
    };

    let revealObserver = null;
    function prepareReveal(element) {
        if (element.hasAttribute('data-reveal')) return;
        element.setAttribute('data-reveal', '');
        const siblingCandidates = [...element.parentElement.children].filter(node => node.matches(revealSelector));
        const position = siblingCandidates.indexOf(element);
        element.style.setProperty('--reveal-delay', `${Math.min(Math.max(position, 0), 3) * 65}ms`);
        revealObserver.observe(element);
    }

    if (!reducedMotion && 'IntersectionObserver' in window && 'MutationObserver' in window) {
        try {
            revealObserver = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (!entry.isIntersecting) return;
                    entry.target.setAttribute('data-revealed', '');
                    revealObserver.unobserve(entry.target);
                });
            }, {threshold: 0.12, rootMargin: '0px 0px -36px 0px'});
            revealBatch(document);
            document.body.classList.add('motion-ready');
            const dynamicContentObserver = new MutationObserver(records => {
                records.forEach(record => record.addedNodes.forEach(revealBatch));
            });
            dynamicContentObserver.observe(document.body, {childList: true, subtree: true});
            window.setTimeout(() => {
                document.querySelectorAll('[data-reveal]:not([data-revealed])').forEach(element => {
                    element.setAttribute('data-revealed', '');
                    revealObserver.unobserve(element);
                });
            }, 3200);
        } catch (_error) {
            document.querySelectorAll('[data-reveal]').forEach(element => element.setAttribute('data-revealed', ''));
            document.body.classList.remove('motion-ready');
        }
    }

    const toggle = document.querySelector('.nav-toggle');
    const nav = document.getElementById('primary-navigation');
    if (toggle && nav) {
        const closeMenu = returnFocus => {
            toggle.setAttribute('aria-expanded', 'false');
            nav.classList.remove('is-open');
            if (returnFocus) toggle.focus();
        };
        toggle.addEventListener('click', () => {
            const open = toggle.getAttribute('aria-expanded') !== 'true';
            toggle.setAttribute('aria-expanded', String(open));
            nav.classList.toggle('is-open', open);
        });
        nav.addEventListener('click', event => {
            if (event.target.closest('a')) closeMenu(false);
        });
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') closeMenu(true);
        });
        document.addEventListener('click', event => {
            if (toggle.getAttribute('aria-expanded') === 'true' && !nav.contains(event.target) && !toggle.contains(event.target)) closeMenu(false);
        });
    }

    document.querySelectorAll('[data-dismiss-alert]').forEach(button => {
        button.addEventListener('click', () => button.closest('.alert')?.remove());
    });

    const progress = document.createElement('div');
    progress.className = 'scroll-progress';
    progress.setAttribute('aria-hidden', 'true');
    document.body.append(progress);
    let frame = 0;
    const updateProgress = () => {
        if (frame) return;
        frame = window.requestAnimationFrame(() => {
            frame = 0;
            const height = Math.max(document.documentElement.scrollHeight, document.body.scrollHeight);
            const scrollable = height - window.innerHeight;
            const useful = height > 1500 && scrollable > window.innerHeight * 0.6;
            progress.classList.toggle('is-visible', useful);
            progress.style.transform = `scaleX(${useful ? Math.min(1, Math.max(0, window.scrollY / scrollable)) : 0})`;
        });
    };
    window.addEventListener('scroll', updateProgress, {passive: true});
    window.addEventListener('resize', updateProgress, {passive: true});
    if ('ResizeObserver' in window) new ResizeObserver(updateProgress).observe(document.documentElement);
    updateProgress();
});

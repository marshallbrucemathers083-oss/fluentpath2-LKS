// Timed practice session and submission handler.

document.addEventListener('DOMContentLoaded', () => {
    const examForm = document.getElementById('exam-form');
    if (!examForm) return;

    let timeRemaining = parseInt(examForm.dataset.duration || '1200', 10);
    const timerDisplay = document.getElementById('exam-timer-display');
    const durationInput = document.getElementById('exam-duration-seconds');
    let startTime = Date.now();
    let isSubmitting = false;

    function formatTime(sec) {
        const m = Math.floor(sec / 60);
        const s = sec % 60;
        return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
    }

    timerDisplay.textContent = formatTime(timeRemaining);

    const timerInterval = setInterval(() => {
        timeRemaining--;
        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            timerDisplay.textContent = '00:00';
            timerDisplay.style.color = 'var(--red)';
            alert('Waktu habis. Jawaban Anda akan dikirim untuk diperiksa.');
            submitExam(true);
            return;
        }

        timerDisplay.textContent = formatTime(timeRemaining);

        // Highlight red if less than 2 minutes left
        if (timeRemaining <= 120) {
            timerDisplay.style.color = 'var(--red)';
            timerDisplay.style.fontWeight = '800';
        }
    }, 1000);

    examForm.addEventListener('submit', (e) => {
        e.preventDefault();
        if (confirm('Kirim jawaban sekarang? Soal yang belum dijawab akan dihitung sebagai salah.')) {
            clearInterval(timerInterval);
            submitExam(false);
        }
    });

    async function submitExam(isAuto = false) {
        if (isSubmitting) return;
        isSubmitting = true;
        const elapsed = Math.round((Date.now() - startTime) / 1000);
        durationInput.value = elapsed;

        const submitBtn = document.getElementById('submit-exam-btn');
        window.setButtonLoading(submitBtn, true, 'Memeriksa jawaban…');

        const formData = new FormData(examForm);

        try {
            const res = await fetch('/api/exam_submit.php', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData,
            });

            const data = await res.json();
            if ((data.success || data.redirect_url) && data.redirect_url) {
                if (typeof window.showToast === 'function') {
                    window.showToast('Jawaban telah diperiksa.', 'success');
                }
                window.location.href = data.redirect_url;
            } else {
                throw new Error(data.error || 'Terjadi kesalahan saat memproses hasil ujian.');
            }
        } catch (err) {
            isSubmitting = false;
            window.setButtonLoading(submitBtn, false);
            window.showToast(err.message || 'Tidak dapat mengirim jawaban. Periksa koneksi dan coba lagi.', 'danger');
        }
    }
});

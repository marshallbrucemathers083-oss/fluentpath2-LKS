// public/assets/js/exam.js - TOEFL-Style Exam Timer and Submission Controller

document.addEventListener('DOMContentLoaded', () => {
    const examForm = document.getElementById('toefl-exam-form');
    if (!examForm) return;

    let timeRemaining = parseInt(examForm.dataset.duration || '1200', 10); // default 20 minutes
    const timerDisplay = document.getElementById('exam-timer-display');
    const durationInput = document.getElementById('exam-duration-seconds');
    let startTime = Date.now();

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
            timerDisplay.style.color = 'var(--danger)';
            alert('Waktu ujian telah habis! Jawaban Anda akan otomatis dikirimkan untuk evaluasi.');
            submitExam(true);
            return;
        }

        timerDisplay.textContent = formatTime(timeRemaining);

        // Highlight red if less than 2 minutes left
        if (timeRemaining <= 120) {
            timerDisplay.style.color = 'var(--danger)';
            timerDisplay.style.fontWeight = '800';
        }
    }, 1000);

    examForm.addEventListener('submit', (e) => {
        e.preventDefault();
        if (confirm('Apakah Anda yakin ingin menyelesaikan dan mengirimkan seluruh jawaban ujian TOEFL ini?')) {
            clearInterval(timerInterval);
            submitExam(false);
        }
    });

    async function submitExam(isAuto = false) {
        const elapsed = Math.round((Date.now() - startTime) / 1000);
        durationInput.value = elapsed;

        const submitBtn = document.getElementById('submit-exam-btn');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Menghitung Skor TOEFL...';
        }

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
                    window.showToast('Ujian berhasil dievaluasi! Menampilkan rekap skor...', 'success');
                }
                window.location.href = data.redirect_url;
            } else {
                alert(data.error || 'Terjadi kesalahan saat memproses hasil ujian.');
                if (submitBtn) submitBtn.disabled = false;
            }
        } catch (err) {
            alert('Terjadi kesalahan saat mengirim hasil ujian. Periksa koneksi Anda.');
            if (submitBtn) submitBtn.disabled = false;
        }
    }
});

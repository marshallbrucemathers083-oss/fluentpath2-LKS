// public/assets/js/academy.js - Academy Practice Session Controller

document.addEventListener('DOMContentLoaded', () => {
    const sessionForm = document.getElementById('academy-session-form');
    if (!sessionForm) return;

    sessionForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const submitBtn = document.getElementById('submit-session-btn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Mengevaluasi Sesi...';

        const formData = new FormData(sessionForm);

        try {
            const res = await fetch('/api/academy_submit.php', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData,
            });

            const data = await res.json();
            submitBtn.disabled = false;
            submitBtn.textContent = 'Selesai & Evaluasi Latihan';

            if (!data.success) {
                alert(data.error || 'Gagal mengevaluasi sesi latihan.');
                return;
            }

            // Render result view
            const quizContainer = document.getElementById('academy-questions-container');
            const resultContainer = document.getElementById('academy-results-container');

            quizContainer.style.display = 'none';
            resultContainer.style.display = 'block';

            // Populate summary
            document.getElementById('res-accuracy').textContent = data.accuracy + '%';
            document.getElementById('res-correct').textContent = `${data.correct_count} / ${data.total_questions}`;
            document.getElementById('res-xp').textContent = `+${data.xp_earned} XP`;

            if (data.xp_earned > 0 && typeof window.showToast === 'function') {
                window.showToast(`Latihan Selesai! Meraih +${data.xp_earned} XP 🎉`, 'xp');
            }

            // Weak topics
            const topicsList = document.getElementById('res-weak-topics');
            topicsList.innerHTML = '';
            if (data.weak_topics && data.weak_topics.length > 0) {
                data.weak_topics.forEach(t => {
                    const li = document.createElement('li');
                    li.textContent = t;
                    topicsList.appendChild(li);
                });
            } else {
                topicsList.innerHTML = '<li style="color:var(--success); font-weight:600;">Luar biasa! Tidak ada topik yang memerlukan perbaikan segera.</li>';
            }

            // Detailed question reviews
            const reviewList = document.getElementById('res-detailed-review');
            reviewList.innerHTML = '';
            data.results.forEach((r, idx) => {
                const item = document.createElement('div');
                item.className = 'card';
                item.style.marginBottom = '16px';
                item.style.borderLeft = r.is_correct ? '4px solid var(--success)' : '4px solid var(--danger)';
                item.innerHTML = `
                    <div style="display:flex; justify-content:space-between; margin-bottom:8px; align-items:center;">
                        <span style="font-weight:700; font-size:0.9rem; color:var(--primary);">Soal #${idx + 1} (${r.skill} - ${r.topic || 'General'})</span>
                        <span class="badge ${r.is_correct ? 'badge-success' : 'badge-danger'}">${r.is_correct ? 'Benar' : 'Salah'}</span>
                    </div>
                    <p style="font-weight:600; margin-bottom:10px; color:var(--text-main); font-size:1rem;">${r.prompt}</p>
                    <div style="font-size:0.925rem; margin-bottom:6px;">
                        <strong>Jawaban Anda:</strong> <span style="color:${r.is_correct ? 'var(--success)' : 'var(--danger)'}; font-weight:600;">${r.submitted_answer || '(Kosong)'}</span>
                    </div>
                    ${!r.is_correct ? `<div style="font-size:0.925rem; margin-bottom:6px; color:var(--success);"><strong>Kunci Jawaban:</strong> ${r.correct_answer}</div>` : ''}
                    <div class="passage-box" style="margin-top:10px; margin-bottom:0; font-size:0.875rem;">
                        <strong style="color:var(--text-main);">Penjelasan:</strong> ${r.explanation}
                    </div>
                `;
                reviewList.appendChild(item);
            });

            // Smooth scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } catch (err) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Selesai & Evaluasi Latihan';
            alert('Terjadi kesalahan teknis saat mengirim data sesi latihan.');
        }
    });
});

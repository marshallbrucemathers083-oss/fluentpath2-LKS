// public/assets/js/challenge.js - Interactive Challenge Handler

document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize Sentence Ordering Challenges
    document.querySelectorAll('.sentence-order-box').forEach(box => {
        const bank = box.querySelector('.word-bank:not(.target-sentence-area)');
        const target = box.querySelector('.target-sentence-area');
        const hiddenInput = box.querySelector('input[type="hidden"]');
        
        let orderedWords = [];

        function updateSentence() {
            hiddenInput.value = JSON.stringify(orderedWords);
        }

        if (bank && target) {
            bank.querySelectorAll('.word-chip').forEach(chip => {
                chip.addEventListener('click', () => {
                    if (chip.classList.contains('selected')) return;

                    chip.classList.add('selected');
                    const word = chip.dataset.word;
                    orderedWords.push(word);

                    // Add to target area
                    const slotChip = document.createElement('span');
                    slotChip.className = 'word-chip slot-chip';
                    slotChip.textContent = word;
                    
                    slotChip.addEventListener('click', () => {
                        // Remove from ordered
                        const idx = orderedWords.indexOf(word);
                        if (idx > -1) {
                            orderedWords.splice(idx, 1);
                        }
                        slotChip.remove();
                        chip.classList.remove('selected');
                        updateSentence();
                    });

                    target.appendChild(slotChip);
                    updateSentence();
                });
            });
        }
    });

    // 2. Form submission for challenges
    document.querySelectorAll('.challenge-form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const challengeId = form.dataset.challengeId;
            const feedbackContainer = document.getElementById(`feedback-${challengeId}`);
            const submitBtn = form.querySelector('button[type="submit"]');

            const formData = new FormData(form);
            const answer = formData.get('answer');

            if (!answer || (typeof answer === 'string' && answer.trim() === '')) {
                alert('Silakan pilih atau masukkan jawaban Anda terlebih dahulu.');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.textContent = 'Memeriksa...';

            try {
                const response = await fetch('/api/challenge_submit.php', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });

                const data = await response.json();

                submitBtn.disabled = false;
                submitBtn.textContent = 'Kirim Jawaban';

                if (!data.success) {
                    alert(data.error || 'Terjadi kesalahan saat memeriksa jawaban.');
                    return;
                }

                feedbackContainer.style.display = 'block';
                feedbackContainer.className = 'card challenge-feedback-box ' + (data.is_correct ? 'alert-success' : 'alert-danger');

                if (data.is_correct) {
                    feedbackContainer.innerHTML = `
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; flex-wrap:wrap; gap:8px;">
                            <strong style="font-size:1.05rem;">🎉 Jawaban Benar!</strong>
                            ${data.xp_awarded > 0 ? `<span class="badge badge-success">+${data.xp_awarded} XP Diterima</span>` : '<span style="font-size:0.8rem; opacity:0.85;">(XP sudah pernah diklaim)</span>'}
                        </div>
                        <p style="margin-bottom:0; font-size:0.925rem; line-height: 1.5;">${data.explanation}</p>
                    `;
                    if (data.xp_awarded > 0 && typeof window.showToast === 'function') {
                        window.showToast(`Jawaban Benar! +${data.xp_awarded} XP diraih 🎉`, 'xp');
                    }
                } else {
                    feedbackContainer.innerHTML = `
                        <div style="margin-bottom:8px;">
                            <strong style="font-size:1.05rem;">❌ Jawaban Belum Tepat</strong>
                        </div>
                        <p style="margin-bottom:6px; font-size:0.925rem;"><strong>Jawaban yang benar:</strong> ${data.correct_answer}</p>
                        <p style="margin-bottom:0; font-size:0.9rem; line-height: 1.5; opacity: 0.95;">${data.explanation}</p>
                    `;
                }
            } catch (err) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Kirim Jawaban';
                alert('Gagal menghubungi server. Periksa koneksi internet Anda.');
            }
        });
    });
});

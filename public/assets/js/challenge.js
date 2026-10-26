document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.sentence-order-box').forEach(box => {
        const bank = box.querySelector('.word-bank:not(.target-sentence-area)');
        const target = box.querySelector('.target-sentence-area');
        const hiddenInput = box.querySelector('input[name="answer"]');
        let orderedWords = [];

        function updateSentence() {
            hiddenInput.value = JSON.stringify(orderedWords);
        }

        if (bank && target) {
            bank.querySelectorAll('.word-chip').forEach(chip => {
                chip.setAttribute('role', 'button');
                chip.setAttribute('tabindex', '0');
                chip.setAttribute('aria-pressed', 'false');
                const selectChip = () => {
                    if (chip.classList.contains('selected')) return;

                    chip.classList.add('selected');
                    chip.setAttribute('aria-pressed', 'true');
                    const word = chip.dataset.word;
                    orderedWords.push(word);

                    const slotChip = document.createElement('span');
                    slotChip.className = 'word-chip slot-chip';
                    slotChip.textContent = word;
                    
                    slotChip.setAttribute('role', 'button');
                    slotChip.setAttribute('tabindex', '0');
                    slotChip.setAttribute('aria-label', `Remove ${word} from answer`);
                    const removeSlot = () => {
                        const idx = orderedWords.indexOf(word);
                        if (idx > -1) {
                            orderedWords.splice(idx, 1);
                        }
                        slotChip.remove();
                        chip.classList.remove('selected');
                        chip.setAttribute('aria-pressed', 'false');
                        updateSentence();
                    };
                    slotChip.addEventListener('click', removeSlot);
                    slotChip.addEventListener('keydown', event => {
                        if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); removeSlot(); chip.focus(); }
                    });

                    target.appendChild(slotChip);
                    updateSentence();
                };
                chip.addEventListener('click', selectChip);
                chip.addEventListener('keydown', event => {
                    if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); selectChip(); }
                });
            });
        }
    });

    document.querySelectorAll('.challenge-form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const challengeId = form.dataset.challengeId;
            const feedbackContainer = document.getElementById(`feedback-${challengeId}`);
            const submitBtn = form.querySelector('button[type="submit"]');
            if (!submitBtn || submitBtn.disabled) return;

            const formData = new FormData(form);
            const answer = formData.get('answer');

            if (!answer || (typeof answer === 'string' && answer.trim() === '')) {
                alert('Silakan pilih atau masukkan jawaban Anda terlebih dahulu.');
                return;
            }

            const originalLabel = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.setAttribute('aria-busy', 'true');
            submitBtn.textContent = 'Memeriksa...';
            form.setAttribute('aria-busy', 'true');

            try {
                const response = await fetch('/api/challenge_submit.php', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });

                const data = await response.json();
                if (!response.ok || !data.success) throw new Error(data.error || 'Jawaban belum dapat diperiksa.');

                feedbackContainer.hidden = false;
                feedbackContainer.className = `challenge-feedback alert ${data.is_correct ? 'alert-success' : 'alert-danger'}`;
                feedbackContainer.replaceChildren();
                const title = document.createElement('strong');
                title.textContent = data.is_correct ? 'Jawaban benar' : 'Jawaban belum tepat';
                feedbackContainer.append(title);
                if (data.is_correct && data.xp_awarded > 0) {
                    const reward = document.createElement('span');
                    reward.className = 'feedback-reward';
                    reward.textContent = `+${Number(data.xp_awarded)} XP`;
                    feedbackContainer.append(reward);
                }
                if (!data.is_correct) {
                    const correct = document.createElement('p');
                    const label = document.createElement('strong');
                    label.textContent = 'Jawaban yang benar: ';
                    correct.append(label, document.createTextNode(data.correct_answer || ''));
                    feedbackContainer.append(correct);
                }
                const explanation = document.createElement('p');
                explanation.textContent = data.explanation || '';
                feedbackContainer.append(explanation);

                if (data.is_correct) {
                    if (data.xp_awarded > 0 && typeof window.showToast === 'function') {
                        window.showToast(`Jawaban benar · +${data.xp_awarded} XP`, 'success');
                    }
                }
            } catch (err) {
                window.showToast(err.message || 'Gagal menghubungi server. Periksa koneksi Anda.', 'danger');
            } finally {
                submitBtn.disabled = false;
                submitBtn.removeAttribute('aria-busy');
                submitBtn.textContent = originalLabel;
                form.removeAttribute('aria-busy');
            }
        });
    });
});

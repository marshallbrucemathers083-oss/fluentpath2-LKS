document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('academy-session-form');
    if (!form) return;
    const button = document.getElementById('submit-session-btn');
    const questions = document.getElementById('academy-questions-container');
    const results = document.getElementById('academy-results-container');

    form.addEventListener('submit', async event => {
        event.preventDefault();
        if (button.disabled) return;
        button.disabled = true;
        button.setAttribute('aria-busy', 'true');
        button.textContent = 'Memeriksa jawaban…';
        try {
            const response = await fetch('/api/academy_submit.php', {
                method: 'POST',
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                body: new FormData(form),
            });
            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.error || 'Latihan belum dapat diperiksa.');

            document.getElementById('res-accuracy').textContent = `${data.accuracy}%`;
            document.getElementById('res-correct').textContent = `${data.correct_count} / ${data.total_questions}`;
            document.getElementById('res-xp').textContent = `+${data.xp_earned} XP`;

            const topics = document.getElementById('res-weak-topics');
            topics.replaceChildren();
            (data.weak_topics || []).forEach(topic => {
                const item = document.createElement('li');
                item.textContent = topic;
                topics.append(item);
            });
            if (!topics.children.length) {
                const item = document.createElement('li');
                item.textContent = 'Tidak ada topik yang perlu ditinjau dari sesi ini.';
                topics.append(item);
            }

            const review = document.getElementById('res-detailed-review');
            review.replaceChildren();
            (data.results || []).forEach((result, index) => {
                const item = document.createElement('article');
                item.className = `review-item ${result.is_correct ? 'answer-correct' : 'answer-incorrect'}`;
                const heading = document.createElement('h3');
                heading.textContent = `Soal ${index + 1} · ${result.skill}${result.topic ? ` · ${result.topic}` : ''}`;
                const prompt = document.createElement('p');
                prompt.textContent = result.prompt;
                const answer = document.createElement('p');
                answer.textContent = `Jawaban Anda: ${result.submitted_answer || '(Kosong)'}`;
                const expected = document.createElement('p');
                expected.textContent = `Jawaban yang benar: ${result.correct_answer}`;
                const explanation = document.createElement('p');
                explanation.textContent = result.explanation;
                item.append(heading, prompt, answer);
                if (!result.is_correct) item.append(expected);
                item.append(explanation);
                review.append(item);
            });

            questions.hidden = true;
            results.hidden = false;
            if (data.xp_earned > 0) window.showToast(`Latihan selesai. Anda mendapat ${data.xp_earned} XP.`, 'success');
            results.querySelector('h2')?.focus();
        } catch (error) {
            window.showToast(error.message || 'Server tidak dapat dihubungi.', 'danger');
            button.disabled = false;
            button.removeAttribute('aria-busy');
            button.textContent = 'Kirim jawaban';
        }
    });
});

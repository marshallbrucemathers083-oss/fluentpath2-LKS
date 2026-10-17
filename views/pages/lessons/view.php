<?php
// views/pages/lessons/view.php - Detailed Lesson & Interactive Challenge View
$title = $lesson['title'] . " (CEFR " . $lesson['level'] . ")";
require dirname(__DIR__, 2) . '/layouts/header.php';
?>

<div class="reveal-on-scroll" style="margin-bottom: 24px;">
    <a href="/lessons" style="font-size: 0.9rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 12px; transition: transform 0.15s ease;">
        ← Kembali ke Katalog Kurikulum
    </a>

    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                <span class="badge badge-cefr" style="background: <?= get_cefr_color($lesson['level']) ?>;">Level <?= e($lesson['level']) ?></span>
                <span class="badge badge-info"><?= e($lesson['skill']) ?></span>
                <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Modul #<?= (int)$lesson['order_num'] ?></span>
            </div>
            <h1 class="page-title"><?= e($lesson['title']) ?></h1>
        </div>

        <div style="display: flex; align-items: center; gap: 12px;">
            <?php if ($lesson['is_completed']): ?>
                <span class="badge badge-success" style="font-size: 0.9rem; padding: 6px 14px;">✓ Modul Telah Selesai</span>
            <?php else: ?>
                <button type="button" id="mark-lesson-btn" class="btn btn-primary" onclick="markLessonCompleted(<?= (int)$lesson['id'] ?>)">
                    Tandai Selesai (+<?= (int)$lesson['xp_reward'] ?> XP)
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Lesson Content Sections -->
<div style="display: flex; flex-direction: column; gap: 24px; margin-bottom: 40px;">
    <!-- 1. Learning Objectives -->
    <div class="card card-callout-info reveal-on-scroll">
        <h3 style="font-size: 1.05rem; margin-bottom: 8px; color: var(--primary);">🎯 Tujuan Pembelajaran (Learning Objectives)</h3>
        <p style="font-size: 0.95rem; line-height: 1.6; color: var(--text-main); margin: 0;">
            <?= e($lesson['learning_objectives']) ?>
        </p>
    </div>

    <!-- 2. Topic Explanation -->
    <div class="card reveal-on-scroll">
        <h2 style="font-size: 1.3rem; margin-bottom: 16px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
            📖 Penjelasan Materi (Topic Explanation)
        </h2>
        <div style="font-size: 1rem; line-height: 1.7; color: var(--text-main); white-space: pre-line;">
            <?= e($lesson['explanation']) ?>
        </div>
    </div>

    <!-- 3. Original Examples -->
    <div class="card reveal-on-scroll">
        <h3 style="font-size: 1.15rem; margin-bottom: 14px; color: var(--success);">
            💡 Contoh Penggunaan Otentik (Original Examples)
        </h3>
        <div class="card-example-box">
            <?= e($lesson['examples']) ?>
        </div>
    </div>

    <!-- 4. Notes / Tips -->
    <?php if (!empty($lesson['notes'])): ?>
    <div class="card card-note-box reveal-on-scroll">
        <h3 style="font-size: 1.05rem; margin-bottom: 8px; color: var(--warning-text);">
            📌 Catatan Tata Bahasa & Kosakata (Grammar & Vocab Notes)
        </h3>
        <p style="font-size: 0.925rem; line-height: 1.6; margin: 0;">
            <?= e($lesson['notes']) ?>
        </p>
    </div>
    <?php endif; ?>

    <!-- 5. Practice Guide -->
    <?php if (!empty($lesson['practice_guide'])): ?>
    <div class="card reveal-on-scroll">
        <h3 style="font-size: 1.1rem; margin-bottom: 8px;">
            ✍️ Panduan Latihan Mandiri (Practice Exercises)
        </h3>
        <p style="font-size: 0.925rem; line-height: 1.6; color: var(--text-muted); margin: 0;">
            <?= e($lesson['practice_guide']) ?>
        </p>
    </div>
    <?php endif; ?>

    <!-- 6. References & Further Reading -->
    <?php if (!empty($lesson['further_reading'])): ?>
    <div class="reveal-on-scroll" style="font-size: 0.85rem; color: var(--text-muted); padding: 12px 16px; background: var(--bg-alt); border-radius: var(--radius-md); border: 1px solid var(--border);">
        <strong>Referensi & Bacaan Lanjutan:</strong> <?= e($lesson['further_reading']) ?>
    </div>
    <?php endif; ?>
</div>

<!-- Interactive Challenges Section -->
<div id="challenges" class="reveal-on-scroll" style="margin-top: 40px; padding-top: 32px; border-top: 2px dashed var(--border);">
    <div style="margin-bottom: 24px;">
        <span class="badge badge-warning" style="margin-bottom: 6px;">Tantangan Penguasaan</span>
        <h2 style="font-size: 1.6rem;">Tantangan Interaktif Modul Ini</h2>
        <p style="color: var(--text-muted); font-size: 0.95rem;">
            Selesaikan kuis tantangan di bawah untuk menguji pemahaman materi Anda. Jawaban diverifikasi langsung di server.
        </p>
    </div>

    <?php if (!empty($lesson['challenges'])): ?>
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <?php foreach ($lesson['challenges'] as $index => $ch): ?>
                <div class="card reveal-on-scroll" style="border-top: 4px solid var(--primary);" id="challenge-card-<?= (int)$ch['id'] ?>">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
                        <span style="font-weight: 700; font-size: 0.95rem; color: var(--primary);">
                            Tantangan #<?= $index + 1 ?> &bull; <?= ucwords(str_replace('_', ' ', $ch['type'])) ?>
                        </span>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <?php if ($ch['user_solved']): ?>
                                <span class="badge badge-success">✓ Pernah Dipecahkan</span>
                            <?php endif; ?>
                            <span class="badge badge-info">+<?= (int)$ch['xp_reward'] ?> XP</span>
                        </div>
                    </div>

                    <!-- Reading Passage if present -->
                    <?php if (!empty($ch['passage'])): ?>
                        <div class="passage-box">
                            <strong style="color: var(--text-muted); display: block; margin-bottom: 6px;">Bahan Bacaan:</strong>
                            <?= nl2br(e($ch['passage'])) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Audio prompt for Listening Comprehension -->
                    <?php if (!empty($ch['audio_text'])): ?>
                        <div class="audio-box">
                            <div>
                                <strong style="font-size: 0.9rem;">Audio Percakapan Bahasa Inggris:</strong>
                                <div style="font-size: 0.825rem; color: var(--text-muted);">Klik tombol di samping untuk mendengarkan audio pengucapan.</div>
                            </div>
                            <button type="button" class="audio-play-btn" onclick="playEnglishAudio(<?= htmlspecialchars(json_encode($ch['audio_text']), ENT_QUOTES, 'UTF-8') ?>, this)">
                                🔊 Putar Audio
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Question Prompt -->
                    <p style="font-weight: 600; font-size: 1.05rem; margin-bottom: 18px; color: var(--text-main);">
                        <?= e($ch['prompt']) ?>
                    </p>

                    <form class="challenge-form" data-challenge-id="<?= (int)$ch['id'] ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="challenge_id" value="<?= (int)$ch['id'] ?>">

                        <!-- Input rendering depending on challenge type -->
                        <?php if (in_array($ch['type'], ['multiple_choice', 'reading_comprehension', 'listening_comprehension'])): ?>
                            <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px;">
                                <?php foreach ($ch['options'] as $opt): ?>
                                    <label class="option-label">
                                        <input type="radio" name="answer" value="<?= e($opt['option_key']) ?>" required style="accent-color: var(--primary); width: 18px; height: 18px;">
                                        <span style="font-weight: 700; width: 20px;"><?= e($opt['option_key']) ?>.</span>
                                        <span style="font-size: 0.95rem;"><?= e($opt['option_text']) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>

                        <?php elseif ($ch['type'] === 'sentence_order'): ?>
                            <div class="sentence-order-box" style="margin-bottom: 20px;">
                                <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 6px;">
                                    Klik kata di bawah untuk menyusun kalimat yang benar:
                                </div>
                                <div class="target-sentence-area word-bank"></div>
                                <div class="word-bank">
                                    <?php
                                        $tokens = json_decode($ch['correct_answer'], true) ?: [];
                                        shuffle($tokens); // shuffle words for student to reassemble
                                        foreach ($tokens as $token):
                                    ?>
                                        <span class="word-chip" data-word="<?= e($token) ?>"><?= e($token) ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <input type="hidden" name="answer" value="[]">
                            </div>

                        <?php elseif ($ch['type'] === 'vocab_match'): ?>
                            <?php $pairs = json_decode($ch['correct_answer'], true) ?: []; ?>
                            <div style="margin-bottom: 20px;">
                                <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 10px;">
                                    Cocokkan kosakata bahasa Inggris dengan artinya yang tepat:
                                </div>
                                <div style="display: grid; gap: 10px;">
                                    <?php foreach ($pairs as $enWord => $idDef): ?>
                                        <div style="display: flex; align-items: center; gap: 14px; background: var(--bg-alt); padding: 10px 14px; border-radius: var(--radius-md); border: 1px solid var(--border);">
                                            <span style="font-weight: 700; width: 140px; color: var(--primary);"><?= e($enWord) ?></span>
                                            <span style="color: var(--text-muted);">=</span>
                                            <select class="form-control match-select" data-key="<?= e($enWord) ?>" style="flex-grow: 1;">
                                                <option value="">-- Pilih Arti Kata --</option>
                                                <?php foreach ($pairs as $k => $v): ?>
                                                    <option value="<?= e($v) ?>"><?= e($v) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <input type="hidden" name="answer" id="vocab-match-answer-<?= (int)$ch['id'] ?>" value="{}">
                            </div>
                            <script>
                                document.addEventListener('DOMContentLoaded', () => {
                                    const selects = document.querySelectorAll('#challenge-card-<?= (int)$ch['id'] ?> .match-select');
                                    const hidden = document.getElementById('vocab-match-answer-<?= (int)$ch['id'] ?>');
                                    function updatePairs() {
                                        const res = {};
                                        selects.forEach(s => {
                                            if (s.value) res[s.dataset.key] = s.value;
                                        });
                                        hidden.value = JSON.stringify(res);
                                    }
                                    selects.forEach(s => s.addEventListener('change', updatePairs));
                                });
                            </script>

                        <?php else: // fill_blank, grammar_correction, short_answer ?>
                            <div class="form-group">
                                <label class="form-label">Jawaban Anda:</label>
                                <input type="text" name="answer" class="form-control" placeholder="Ketik jawaban bahasa Inggris Anda di sini..." required>
                            </div>
                        <?php endif; ?>

                        <div style="display: flex; justify-content: flex-end;">
                            <button type="submit" class="btn btn-primary">
                                Kirim Jawaban
                            </button>
                        </div>
                    </form>

                    <!-- Feedback Container -->
                    <div id="feedback-<?= (int)$ch['id'] ?>" class="challenge-feedback-box" style="display: none; margin-top: 16px; padding: 16px;"></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p style="color: var(--text-muted);">Belum ada tantangan kuis untuk pelajaran ini.</p>
    <?php endif; ?>
</div>

<script src="/assets/js/challenge.js"></script>
<script>
async function markLessonCompleted(lessonId) {
    const btn = document.getElementById('mark-lesson-btn');
    if (!btn) return;
    btn.disabled = true;
    btn.textContent = 'Menyimpan...';

    const formData = new FormData();
    formData.append('csrf_token', <?= json_encode(csrf_token()) ?>);
    formData.append('lesson_id', lessonId);

    try {
        const res = await fetch('/api/lesson_complete.php', {
            method: 'POST',
            body: formData,
        });
        const data = await res.json();
        if (data.success) {
            btn.className = 'badge badge-success';
            btn.style.fontSize = '0.9rem';
            btn.style.padding = '6px 14px';
            btn.textContent = '✓ Modul Telah Selesai' + (data.xp_awarded > 0 ? ` (+${data.xp_awarded} XP)` : '');
            if (typeof window.showToast === 'function') {
                window.showToast(data.xp_awarded > 0 ? `Selamat! Anda menyelesaikan pelajaran ini dan meraih +${data.xp_awarded} XP!` : 'Pelajaran telah ditandai selesai.', 'xp');
            } else {
                alert(data.xp_awarded > 0 ? `Selamat! Anda menyelesaikan pelajaran ini dan meraih +${data.xp_awarded} XP!` : 'Pelajaran telah ditandai selesai.');
            }
        } else {
            btn.disabled = false;
            btn.textContent = 'Tandai Selesai';
            alert(data.error || 'Gagal menandai pelajaran.');
        }
    } catch (e) {
        btn.disabled = false;
        btn.textContent = 'Tandai Selesai';
        alert('Gagal menghubungi server.');
    }
}
</script>

<?php require dirname(__DIR__, 2) . '/layouts/footer.php'; ?>

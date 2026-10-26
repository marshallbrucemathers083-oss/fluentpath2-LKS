<?php
$title = $skill . ' Practice';
require dirname(__DIR__) . '/layouts/header.php';
$isWriting = $skill === 'Writing';
?>
<section class="skill-guide content-section">
    <a href="/academy" class="text-link">← Kembali ke Latihan</a>
    <p class="eyebrow" style="margin-top:20px">Latihan mandiri · refleksi</p>
    <h1><?= e($skill) ?></h1>
    <p class="lead">Use this structured task to practise at your own CEFR level. Your work stays in this browser; FluentPath does not claim to score it automatically.</p>
    <form class="writing-task surface" onsubmit="return false">
        <label for="cefr-level">Choose a level</label>
        <select id="cefr-level" class="form-control">
            <?php foreach (['A1','A2','B1','B2','C1','C2'] as $level): ?><option><?= $level ?></option><?php endforeach; ?>
        </select>
        <h2>Today's prompt</h2>
        <p id="task-prompt"><?= $isWriting ? 'Describe a familiar person, place, or daily routine using clear, simple sentences.' : 'Speak for one minute about a familiar person, place, or daily routine. Organize your ideas and use clear, simple sentences.' ?></p>
        <h2><?= $isWriting ? 'Writing space' : 'Speaking workflow' ?></h2>
        <?php if ($isWriting): ?>
            <p>Plan your main idea, write a short response, then revise for meaning, organization, vocabulary, and grammar.</p>
            <textarea class="form-control practice-editor" rows="12" placeholder="Write your response here…" aria-label="Your writing response"></textarea>
        <?php else: ?>
            <ol><li>Take 30 seconds to note three key words.</li><li>Speak aloud for one minute. Record yourself with any device if useful.</li><li>Listen once and note one strength and one change for next time.</li></ol>
            <textarea class="form-control practice-editor" rows="5" placeholder="Optional notes after speaking…" aria-label="Speaking reflection notes"></textarea>
        <?php endif; ?>
        <h2>Self-assessment rubric</h2>
        <p class="muted">Rate each area honestly after you finish. This reflection is saved in this browser only; it is not an automated score.</p>
        <div class="self-ratings">
            <?php foreach (['Task completion','Organization','Vocabulary range','Grammar and clarity'] as $i=>$criterion): ?>
            <label for="rubric-<?= $i ?>"><?= e($criterion) ?><select id="rubric-<?= $i ?>" class="rubric-score"><option value="">Choose a rating</option><option value="1">1 · Needs work</option><option value="2">2 · Developing</option><option value="3">3 · Confident</option></select></label>
            <?php endforeach; ?>
        </div>
        <label for="next-step">One thing to carry into your next session</label>
        <textarea id="next-step" class="form-control" rows="3" placeholder="A specific improvement or strength to remember…"></textarea>
        <button type="button" class="button button-primary" id="save-reflection">Simpan refleksi</button>
        <p id="reflection-status" class="muted" role="status" aria-live="polite"></p>
        <script>
        (() => {
          const prefix = 'fluentpath-reflection-<?= e(strtolower($skill)) ?>-';
          const levelSelect = document.getElementById('cefr-level');
          const fields = [...document.querySelectorAll('.rubric-score')];
          const note = document.getElementById('next-step');
          const prompt = document.getElementById('task-prompt');
          const writing = <?= $isWriting ? 'true' : 'false' ?>;
          const prompts = {
            A: writing ? 'Describe a familiar person, place, or daily routine using clear, simple sentences.' : 'Speak for one minute about a familiar person, place, or daily routine. Organize your ideas and use clear, simple sentences.',
            B: writing ? 'Explain a change in your community and give reasons and examples.' : 'Explain a change in your community, giving reasons and examples in a short spoken response.',
            C: writing ? 'Discuss the implications of a current social or academic issue, weighing evidence and counterarguments.' : 'Discuss the implications of a current social or academic issue, weighing evidence and counterarguments aloud.'
          };
          const updatePrompt = () => { prompt.textContent = prompts[levelSelect.value[0]]; };
          const load = () => {
            const saved = JSON.parse(localStorage.getItem(prefix + levelSelect.value) || '{}');
            fields.forEach((field,i) => field.value = saved.ratings?.[i] || '');
            note.value = saved.note || '';
          };
          load();
          levelSelect.addEventListener('change', () => { load(); updatePrompt(); });
          document.getElementById('save-reflection').addEventListener('click', () => {
            localStorage.setItem(prefix + levelSelect.value, JSON.stringify({ratings:fields.map(f=>f.value), note:note.value, savedAt:new Date().toISOString()}));
            document.getElementById('reflection-status').textContent = 'Reflection saved on this device.';
          });
        })();
        </script>
    </form>
</section>
<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>

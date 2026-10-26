<?php
$alertMessages = [
    'success' => flash('success'),
    'error' => flash('error'),
    'info' => flash('info'),
];
?>
<?php foreach ($alertMessages as $type => $message): ?>
    <?php if ($message): ?>
        <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?>" role="status">
            <span><?= e($message) ?></span>
            <button class="alert-dismiss" type="button" data-dismiss-alert aria-label="Tutup pesan">×</button>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<?php
// views/partials/alerts.php - Flash Notification Alerts
$successMsg = flash('success');
$errorMsg = flash('error');
$infoMsg = flash('info');
?>
<?php if ($successMsg): ?>
    <div class="alert alert-success alert-dismissible">
        <span>✓</span>
        <div><?= e($successMsg) ?></div>
    </div>
<?php endif; ?>

<?php if ($errorMsg): ?>
    <div class="alert alert-danger alert-dismissible">
        <span>⚠️</span>
        <div><?= e($errorMsg) ?></div>
    </div>
<?php endif; ?>

<?php if ($infoMsg): ?>
    <div class="alert alert-info alert-dismissible">
        <span>ℹ️</span>
        <div><?= e($infoMsg) ?></div>
    </div>
<?php endif; ?>

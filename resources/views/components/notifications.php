<?php
// Usage: include 'components/notifications.php';
// Expects: $success (string|null), $error (string|null), $errors (array|null)
 $successMsg = $success ?? \App\Core\Session::flash('success');
 $errorMsg   = $error ?? \App\Core\Session::flash('error');
 $errorBag   = $errors ?? \App\Core\Session::flash('errors');
?>

<?php if ($successMsg): ?>
<div id="toast-success" class="toast show bg-white border border-green-200 shadow-card-hover">
    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
    </div>
    <div class="flex-1">
        <p class="text-sm font-medium text-green-800"><?php echo htmlspecialchars($successMsg, ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
    <button onclick="this.closest('.toast').remove()" class="text-green-400 hover:text-green-600">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>
<script>
    setTimeout(function() {
        var el = document.getElementById('toast-success');
        if (el) { el.classList.remove('show'); setTimeout(function() { el.remove(); }, 300); }
    }, 5000);
</script>
<?php endif; ?>

<?php if ($errorMsg): ?>
<div id="toast-error" class="toast show bg-white border border-red-200 shadow-card-hover">
    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-red-100 flex items-center justify-center">
        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </div>
    <div class="flex-1">
        <p class="text-sm font-medium text-red-800"><?php echo htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
    <button onclick="this.closest('.toast').remove()" class="text-red-400 hover:text-red-600">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>
<script>
    setTimeout(function() {
        var el = document.getElementById('toast-error');
        if (el) { el.classList.remove('show'); setTimeout(function() { el.remove(); }, 300); }
    }, 7000);
</script>
<?php endif; ?>

<?php if ($errorBag && count($errorBag) > 0): ?>
<div id="toast-errors" class="toast show bg-white border border-amber-200 shadow-card-hover">
    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center">
        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>
    <div class="flex-1">
        <p class="text-sm font-medium text-amber-800">Please fix the following errors:</p>
        <ul class="mt-1 text-xs text-amber-700 list-disc list-inside">
            <?php foreach ($errorBag as $fieldErrors): ?>
                <?php foreach ($fieldErrors as $err): ?>
                    <li><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </ul>
    </div>
    <button onclick="this.closest('.toast').remove()" class="text-amber-400 hover:text-amber-600">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>
<script>
    setTimeout(function() {
        var el = document.getElementById('toast-errors');
        if (el) { el.classList.remove('show'); setTimeout(function() { el.remove(); }, 300); }
    }, 10000);
</script>
<?php endif; ?>
<?php
// Usage: include 'components/empty_state.php';
// Expects: $icon (svg string), $title (string), $description (string), $actionHtml (string — optional button/link)
?>
<div class="flex flex-col items-center justify-center py-16 px-4 text-center">
    <?php echo $icon ?? ''; ?>
    <h3 class="mt-4 text-sm font-semibold text-ink-light"><?php echo htmlspecialchars($title ?? 'No data found', ENT_QUOTES, 'UTF-8'); ?></h3>
    <p class="mt-1 text-xs text-ink-faint max-w-sm"><?php echo htmlspecialchars($description ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
    <?php if (!empty($actionHtml)): ?>
    <div class="mt-5"><?php echo $actionHtml; ?></div>
    <?php endif; ?>
</div>
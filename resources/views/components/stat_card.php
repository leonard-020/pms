<?php
// Usage: include 'components/stat_card.php';
// Expects: $label (string), $value (string), $icon (svg string), $color (string — tailwind color key like 'primary', 'gold', 'burgundy')
 $colors = [
    'primary'  => 'bg-primary-50 text-primary-700',
    'gold'     => 'bg-gold-50 text-gold-600',
    'burgundy' => 'bg-burgundy-50 text-burgundy-500',
    'blue'     => 'bg-sky-50 text-sky-600',
    'green'    => 'bg-green-50 text-green-600',
    'purple'   => 'bg-purple-50 text-purple-600',
];
 $colorClass = $colors[$color ?? 'primary'] ?? $colors['primary'];
?>
<div class="stat-card">
    <div class="w-10 h-10 rounded-lg <?php echo $colorClass; ?> flex items-center justify-center flex-shrink-0">
        <?php echo $icon ?? ''; ?>
    </div>
    <div class="min-w-0">
        <p class="text-xs text-ink-faint font-medium truncate"><?php echo htmlspecialchars($label ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
        <p class="text-xl font-bold text-ink mt-0.5"><?php echo $value ?? '0'; ?></p>
    </div>
</div>
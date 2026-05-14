<div class="page-header">
    <div>
        <h1 class="text-2xl font-display font-bold text-ink">Dashboard</h1>
        <p class="text-sm text-ink-muted mt-1">Welcome back, <?php echo htmlspecialchars($user['role_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

    <?php if (isset($total_members)): ?>
    <?php
    include __DIR__ . '/../components/stat_card.php';
    $icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
    $label = 'Total Members';
    $value = number_format($total_members);
    $color = 'primary';
    include __DIR__ . '/../components/stat_card.php';
    ?>
    <?php endif; ?>

    <?php if (isset($total_income)): ?>
    <?php
    $icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>';
    $label = 'Total Income';
    $value = '₦' . number_format($total_income, 2);
    $color = 'green';
    include __DIR__ . '/../components/stat_card.php';
    ?>
    <?php endif; ?>

    <?php if (isset($total_expense)): ?>
    <?php
    $icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>';
    $label = 'Total Expenses';
    $value = '₦' . number_format($total_expense, 2);
    $color = 'burgundy';
    include __DIR__ . '/../components/stat_card.php';
    ?>
    <?php endif; ?>

    <?php if (isset($net_balance)): ?>
    <?php
    $icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
    $label = 'Net Balance';
    $value = '₦' . number_format($net_balance, 2);
    $color = $net_balance >= 0 ? 'gold' : 'burgundy';
    include __DIR__ . '/../components/stat_card.php';
    ?>
    <?php endif; ?>

    <?php if (isset($pending_txns) && $pending_txns > 0): ?>
    <?php
    $icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
    $label = 'Pending Approvals';
    $value = $pending_txns;
    $color = 'gold';
    include __DIR__ . '/../components/stat_card.php';
    ?>
    <?php endif; ?>

    <?php if (isset($total_groups)): ?>
    <?php
    $icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>';
    $label = 'Active Groups';
    $value = $total_groups;
    $color = 'purple';
    include __DIR__ . '/../components/stat_card.php';
    ?>
    <?php endif; ?>
</div>

<!-- Upcoming Events -->
<?php if (!empty($upcoming_events)): ?>
<div class="card p-5">
    <h2 class="text-sm font-semibold text-ink mb-4">Upcoming Events</h2>
    <div class="space-y-3">
        <?php foreach ($upcoming_events as $evt): ?>
        <div class="flex items-start gap-3 p-3 rounded-lg bg-surface-50 hover:bg-surface-100 transition-colors">
            <div class="w-11 h-11 rounded-lg bg-primary-50 flex flex-col items-center justify-center flex-shrink-0">
                <span class="text-[10px] font-semibold text-primary-600 uppercase">
                    <?php echo date('M', strtotime($evt['event_date'])); ?>
                </span>
                <span class="text-sm font-bold text-primary-800 leading-none">
                    <?php echo date('d', strtotime($evt['event_date'])); ?>
                </span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-ink truncate"><?php echo htmlspecialchars($evt['title'], ENT_QUOTES, 'UTF-8'); ?></p>
                <?php if ($evt['location']): ?>
                <p class="text-xs text-ink-faint mt-0.5"><?php echo htmlspecialchars($evt['location'], ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>
            </div>
            <?php if ($evt['start_time']): ?>
            <span class="text-xs text-ink-faint whitespace-nowrap"><?php echo date('g:i A', strtotime($evt['start_time'])); ?></span>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>
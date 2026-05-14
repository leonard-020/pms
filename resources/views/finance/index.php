<div class="page-header">
    <div>
        <h1 class="text-2xl font-display font-bold text-ink">Finance</h1>
        <p class="text-sm text-ink-muted mt-1">Transaction management</p>
    </div>
    <?php if (in_array($role ?? '', ['super_admin', 'finance_officer'])): ?>
    <a href="/finance/create" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Record Transaction
    </a>
    <?php endif; ?>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <?php
    $incomeIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>';
    include __DIR__ . '/../components/stat_card.php'; $label='Income'; $value='₦'.number_format($summary['total_income'],2); $color='green'; $icon=$incomeIcon;
    include __DIR__ . '/../components/stat_card.php';

    $expenseIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>';
    $label='Expenses'; $value='₦'.number_format($summary['total_expense'],2); $color='burgundy'; $icon=$expenseIcon;
    include __DIR__ . '/../components/stat_card.php';

    $netIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
    $label='Net Balance'; $value='₦'.number_format($summary['net'],2); $color=$summary['net']>=0?'gold':'burgundy'; $icon=$netIcon;
    include __DIR__ . '/../components/stat_card.php';

    $pendingIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
    $label='Pending Approval'; $value=$summary['pending_count']; $color='gold'; $icon=$pendingIcon;
    include __DIR__ . '/../components/stat_card.php';
    ?>
</div>

<?php
 $searchFilters = [
    ['name' => 'type', 'label' => 'All Types', 'options' => ['income' => 'Income', 'expense' => 'Expense']],
    ['name' => 'status', 'label' => 'All Statuses', 'options' => ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']],
];
 $searchParams = array_merge($filters, ['search' => $search]);
include __DIR__ . '/../components/search_filter.php';
?>

<?php if (empty($transactions['data'])): ?>
<?php
 $emptyIcon = '<svg class="w-12 h-12 text-ink-faint" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
 $emptyTitle = 'No transactions found';
include __DIR__ . '/../components/empty_state.php';
?>
<?php else: ?>
<div class="table-container">
    <table class="w-full">
        <thead>
            <tr class="table-header">
                <th class="px-4 py-3">Date</th>
                <th class="px-4 py-3">Type</th>
                <th class="px-4 py-3">Category</th>
                <th class="px-4 py-3 text-right">Amount</th>
                <th class="px-4 py-3 hidden md:table-cell">Recorded By</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions['data'] as $t): ?>
            <tr class="hover:bg-surface-50 transition-colors">
                <td class="table-cell text-ink-muted"><?php echo date('d M Y', strtotime($t['transaction_date'])); ?></td>
                <td class="table-cell">
                    <?php if ($t['type'] === 'income'): ?>
                    <span class="badge-success">Income</span>
                    <?php else: ?>
                    <span class="badge-danger">Expense</span>
                    <?php endif; ?>
                </td>
                <td class="table-cell font-medium"><?php echo htmlspecialchars($t['category'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="table-cell text-right font-semibold <?php echo $t['type'] === 'income' ? 'text-green-700' : 'text-burgundy-500'; ?>">
                    <?php echo $t['type'] === 'income' ? '+' : '-'; ?>₦<?php echo number_format($t['amount'], 2); ?>
                </td>
                <td class="table-cell hidden md:table-cell text-ink-muted text-xs"><?php echo htmlspecialchars($t['recorder_email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="table-cell">
                    <?php
                    $stCls = ['pending' => 'badge-warning', 'approved' => 'badge-success', 'rejected' => 'badge-danger'];
                    ?>
                    <span class="<?php echo $stCls[$t['status']] ?? 'badge-neutral'; ?>"><?php echo ucfirst($t['status']); ?></span>
                </td>
                <td class="table-cell text-right">
                    <?php if ($t['status'] === 'pending' && in_array($role ?? '', ['super_admin', 'parish_priest'])): ?>
                    <?php if ($t['recorded_by'] != ($user['id'] ?? 0)): ?>
                    <form method="POST" action="/finance/<?php echo $t['id']; ?>/approve" class="inline-flex" onsubmit="return confirm('Approve this transaction?')">
                        <?php echo \App\Core\CSRF::field(); ?>
                        <button type="submit" class="btn btn-sm btn-primary" title="Approve">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                    </form>
                    <button type="button" onclick="openRejectModal(<?php echo $t['id']; ?>)" class="btn btn-sm btn-danger ml-1" title="Reject">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    <?php else: ?>
                    <span class="text-[10px] text-ink-faint italic" title="Cannot approve own transaction">Own</span>
                    <?php endif; ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php $baseUrl = '/finance'; include __DIR__ . '/../components/pagination.php'; ?>
<?php endif; ?>
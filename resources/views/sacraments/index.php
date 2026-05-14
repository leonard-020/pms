<div class="page-header">
    <div>
        <h1 class="text-2xl font-display font-bold text-ink">Sacraments</h1>
        <p class="text-sm text-ink-muted mt-1">Sacramental records</p>
    </div>
    <?php if (in_array($role ?? '', ['super_admin', 'parish_priest', 'parish_secretary'])): ?>
    <a href="/sacraments/create" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Record Sacrament
    </a>
    <?php endif; ?>
</div>

<?php
 $searchFilters = [
    ['name' => 'type', 'label' => 'All Types', 'options' => [
        'baptism' => 'Baptism', 'first_communion' => 'First Communion', 'confirmation' => 'Confirmation',
        'marriage' => 'Marriage', 'holy_orders' => 'Holy Orders', 'anointing_sick' => 'Anointing of the Sick',
    ]],
];
 $searchParams = ['search' => $search, 'type' => $type];
include __DIR__ . '/../components/search_filter.php';
?>

<?php if (empty($sacraments['data'])): ?>
<?php
 $emptyIcon = '<svg class="w-12 h-12 text-ink-faint" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>';
 $emptyTitle = 'No sacrament records found';
include __DIR__ . '/../components/empty_state.php';
?>
<?php else: ?>
<div class="table-container">
    <table class="w-full">
        <thead>
            <tr class="table-header">
                <th class="px-4 py-3">Member</th>
                <th class="px-4 py-3">Sacrament</th>
                <th class="px-4 py-3">Date</th>
                <th class="px-4 py-3 hidden md:table-cell">Minister</th>
                <th class="px-4 py-3 hidden lg:table-cell">Place</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sacraments['data'] as $s): ?>
            <tr class="hover:bg-surface-50 transition-colors">
                <td class="table-cell">
                    <a href="/members/<?php echo $s['member_id']; ?>" class="font-medium text-ink hover:text-primary-700">
                        <?php echo htmlspecialchars($s['first_name'] . ' ' . $s['last_name'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                    <p class="text-[10px] text-ink-faint font-mono"><?php echo htmlspecialchars($s['member_number'], ENT_QUOTES, 'UTF-8'); ?></p>
                </td>
                <td class="table-cell">
                    <span class="badge-info"><?php echo ucwords(str_replace('_', ' ', $s['type'])); ?></span>
                </td>
                <td class="table-cell text-ink-muted"><?php echo date('d M Y', strtotime($s['date'])); ?></td>
                <td class="table-cell hidden md:table-cell text-ink-muted"><?php echo htmlspecialchars($s['minister'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="table-cell hidden lg:table-cell text-ink-muted"><?php echo htmlspecialchars($s['place'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php $baseUrl = '/sacraments'; include __DIR__ . '/../components/pagination.php'; ?>
<?php endif; ?>
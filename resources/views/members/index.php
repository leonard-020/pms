<div class="page-header">
    <div>
        <h1 class="text-2xl font-display font-bold text-ink">Parish Members</h1>
        <p class="text-sm text-ink-muted mt-1">Manage parishioner records</p>
    </div>
    <?php if (in_array($role ?? '', ['super_admin', 'parish_priest', 'parish_secretary'])): ?>
    <a href="/members/create" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Register Member
    </a>
    <?php endif; ?>
</div>

<?php
 $searchFilters = [
    ['name' => 'status', 'label' => 'All Statuses', 'options' => [
        'active' => 'Active', 'inactive' => 'Inactive', 'deceased' => 'Deceased', 'transferred' => 'Transferred',
    ]],
];
 $searchParams = ['search' => $search, 'status' => $status];
include __DIR__ . '/../components/search_filter.php';
?>

<?php if (empty($members['data'])): ?>
<?php
 $emptyIcon = '<svg class="w-12 h-12 text-ink-faint" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
 $emptyTitle = 'No members found';
 $emptyDesc = 'Try adjusting your search or filter criteria.';
include __DIR__ . '/../components/empty_state.php';
?>
<?php else: ?>
<div class="table-container">
    <table class="w-full">
        <thead>
            <tr class="table-header">
                <th class="px-4 py-3">ID</th>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3 hidden md:table-cell">Phone</th>
                <th class="px-4 py-3 hidden lg:table-cell">City</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($members['data'] as $m): ?>
            <tr class="hover:bg-surface-50 transition-colors">
                <td class="table-cell font-mono text-xs text-ink-muted"><?php echo htmlspecialchars($m['member_number'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="table-cell">
                    <a href="/members/<?php echo $m['id']; ?>" class="font-medium text-ink hover:text-primary-700 transition-colors">
                        <?php echo htmlspecialchars($m['first_name'] . ' ' . $m['last_name'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                    <?php if ($m['middle_name']): ?>
                    <span class="text-ink-faint text-xs ml-1"><?php echo htmlspecialchars(substr($m['middle_name'], 0, 1) . '.', ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php endif; ?>
                </td>
                <td class="table-cell hidden md:table-cell text-ink-muted"><?php echo htmlspecialchars($m['phone'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="table-cell hidden lg:table-cell text-ink-muted"><?php echo htmlspecialchars($m['city'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="table-cell">
                    <?php
                    $statusClasses = ['active' => 'badge-success', 'inactive' => 'badge-neutral', 'deceased' => 'badge-danger', 'transferred' => 'badge-warning'];
                    $cls = $statusClasses[$m['status']] ?? 'badge-neutral';
                    ?>
                    <span class="<?php echo $cls; ?>"><?php echo ucfirst($m['status']); ?></span>
                </td>
                <td class="table-cell text-right">
                    <a href="/members/<?php echo $m['id']; ?>" class="btn btn-outline btn-sm">View</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
 $baseUrl = '/members';
include __DIR__ . '/../components/pagination.php';
?>
<?php endif; ?>
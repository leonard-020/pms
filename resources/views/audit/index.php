<div class="page-header">
    <div>
        <h1 class="text-2xl font-display font-bold text-ink">Audit Logs</h1>
        <p class="text-sm text-ink-muted mt-1">Immutable system activity trail</p>
    </div>
</div>

<form method="GET" action="/audit-logs" class="flex flex-col sm:flex-row gap-3 mb-5 flex-wrap">
    <div class="relative flex-1 min-w-[200px]">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-faint" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" name="search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Search..." class="form-input pl-9">
    </div>
    <select name="action" class="form-input w-full sm:w-auto">
        <option value="">All Actions</option>
        <?php foreach (['create','update','delete','login','logout','approve','reject','activate','deactivate'] as $a): ?>
        <option value="<?php echo $a; ?>" <?php echo ($filters['action'] ?? '') === $a ? 'selected' : ''; ?>><?php echo ucfirst($a); ?></option>
        <?php endforeach; ?>
    </select>
    <select name="module" class="form-input w-full sm:w-auto">
        <option value="">All Modules</option>
        <?php foreach (['users','members','finance','sacraments','events','groups','auth','profile'] as $m): ?>
        <option value="<?php echo $m; ?>" <?php echo ($filters['module'] ?? '') === $m ? 'selected' : ''; ?>><?php echo ucfirst($m); ?></option>
        <?php endforeach; ?>
    </select>
    <input type="date" name="date_from" value="<?php echo htmlspecialchars($filters['date_from'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="form-input w-full sm:w-auto">
    <input type="date" name="date_to" value="<?php echo htmlspecialchars($filters['date_to'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="form-input w-full sm:w-auto">
    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    <a href="/audit-logs" class="btn btn-outline btn-sm">Clear</a>
</form>

<?php if (empty($logs['data'])): ?>
<?php
 $emptyIcon = '<svg class="w-12 h-12 text-ink-faint" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>';
 $emptyTitle = 'No audit logs found';
include __DIR__ . '/../components/empty_state.php';
?>
<?php else: ?>
<div class="table-container">
    <table class="w-full">
        <thead>
            <tr class="table-header">
                <th class="px-4 py-3">Timestamp</th>
                <th class="px-4 py-3">User</th>
                <th class="px-4 py-3">Action</th>
                <th class="px-4 py-3">Module</th>
                <th class="px-4 py-3 hidden lg:table-cell">Description</th>
                <th class="px-4 py-3 hidden xl:table-cell">IP Address</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs['data'] as $l): ?>
            <tr class="hover:bg-surface-50 transition-colors">
                <td class="table-cell text-ink-muted text-xs whitespace-nowrap">
                    <?php echo date('d M Y H:i:s', strtotime($l['created_at'])); ?>
                </td>
                <td class="table-cell text-xs">
                    <?php echo htmlspecialchars($l['user_email'] ?? 'System', ENT_QUOTES, 'UTF-8'); ?>
                </td>
                <td class="table-cell">
                    <?php
                    $acCls = [
                        'create' => 'badge-success', 'update' => 'badge-info', 'delete' => 'badge-danger',
                        'login' => 'badge-neutral', 'logout' => 'badge-neutral',
                        'approve' => 'badge-success', 'reject' => 'badge-danger',
                        'activate' => 'badge-success', 'deactivate' => 'badge-warning',
                    ];
                    ?>
                    <span class="<?php echo $acCls[$l['action']] ?? 'badge-neutral'; ?>">
                        <?php echo ucfirst($l['action']); ?>
                    </span>
                </td>
                <td class="table-cell text-ink-muted text-xs"><?php echo ucfirst($l['module']); ?></td>
                <td class="table-cell hidden lg:table-cell text-xs text-ink-light max-w-xs truncate">
                    <?php echo htmlspecialchars($l['description'], ENT_QUOTES, 'UTF-8'); ?>
                </td>
                <td class="table-cell hidden xl:table-cell text-ink-faint text-xs font-mono">
                    <?php echo htmlspecialchars($l['ip_address'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php $baseUrl = '/audit-logs'; $queryParams = array_filter($filters) + ['search' => $search]; include __DIR__ . '/../components/pagination.php'; ?>
<?php endif; ?>
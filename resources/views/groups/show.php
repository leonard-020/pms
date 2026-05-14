<div class="page-header">
    <div>
        <h1 class="text-2xl font-display font-bold text-ink"><?php echo htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8'); ?></h1>
        <p class="text-sm text-ink-muted mt-1"><?php echo htmlspecialchars($group['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
    <a href="/groups" class="btn btn-outline">Back</a>
</div>

<?php if (empty($members)): ?>
<?php
 $emptyIcon = '<svg class="w-12 h-12 text-ink-faint" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
 $emptyTitle = 'No members in this group';
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
                <th class="px-4 py-3">Role</th>
                <th class="px-4 py-3 hidden md:table-cell">Joined</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($members as $m): ?>
            <tr class="hover:bg-surface-50 transition-colors">
                <td class="table-cell font-mono text-xs text-ink-muted"><?php echo htmlspecialchars($m['member_number'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="table-cell">
                    <a href="/members/<?php echo $m['id']; ?>" class="font-medium text-ink hover:text-primary-700">
                        <?php echo htmlspecialchars($m['first_name'] . ' ' . $m['last_name'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </td>
                <td class="table-cell hidden md:table-cell text-ink-muted"><?php echo htmlspecialchars($m['phone'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="table-cell">
                    <span class="badge-info"><?php echo ucfirst($m['member_role'] ?? 'Member'); ?></span>
                </td>
                <td class="table-cell hidden md:table-cell text-ink-muted text-xs"><?php echo date('d M Y', strtotime($m['joined_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
<div class="page-header">
    <div>
        <h1 class="text-2xl font-display font-bold text-ink">Ministries & Groups</h1>
        <p class="text-sm text-ink-muted mt-1">Parish organizations and societies</p>
    </div>
    <?php if (in_array($role ?? '', ['super_admin', 'ministry_leader', 'parish_priest'])): ?>
    <a href="/groups/create" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Create Group
    </a>
    <?php endif; ?>
</div>

<form method="GET" action="/groups" class="mb-5">
    <div class="relative max-w-sm">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-faint" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" name="search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Search groups..." class="form-input pl-9">
    </div>
</form>

<?php if (empty($groups['data'])): ?>
<?php
 $emptyIcon = '<svg class="w-12 h-12 text-ink-faint" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>';
 $emptyTitle = 'No groups found';
include __DIR__ . '/../components/empty_state.php';
?>
<?php else: ?>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach ($groups['data'] as $g): ?>
    <a href="/groups/<?php echo $g['id']; ?>" class="card p-5 hover:shadow-card-hover transition-shadow block">
        <div class="flex items-start justify-between gap-2">
            <h3 class="text-sm font-semibold text-ink"><?php echo htmlspecialchars($g['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
            <span class="badge-neutral flex-shrink-0"><?php echo (int)$g['member_count']; ?> members</span>
        </div>
        <?php if ($g['description']): ?>
        <p class="text-xs text-ink-muted mt-2 line-clamp-2"><?php echo htmlspecialchars($g['description'], ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <?php if ($g['leader_name']): ?>
        <p class="text-[10px] text-ink-faint mt-2">Led by: <?php echo htmlspecialchars($g['leader_name'], ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
    </a>
    <?php endforeach; ?>
</div>

<?php $baseUrl = '/groups'; include __DIR__ . '/../components/pagination.php'; ?>
<?php endif; ?>
<div class="page-header">
    <div>
        <h1 class="text-2xl font-display font-bold text-ink">User Management</h1>
        <p class="text-sm text-ink-muted mt-1">System accounts and access control</p>
    </div>
    <a href="/users/create" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Create User
    </a>
</div>

<form method="GET" action="/users" class="mb-5">
    <div class="relative max-w-sm">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-faint" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" name="search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Search by email or role..." class="form-input pl-9">
    </div>
</form>

<?php if (empty($users['data'])): ?>
<?php
 $emptyIcon = '<svg class="w-12 h-12 text-ink-faint" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
 $emptyTitle = 'No users found';
include __DIR__ . '/../components/empty_state.php';
?>
<?php else: ?>
<div class="table-container">
    <table class="w-full">
        <thead>
            <tr class="table-header">
                <th class="px-4 py-3">ID</th>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">Role</th>
                <th class="px-4 py-3 hidden md:table-cell">Last Login</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $currentUserId = \App\Core\Session::get('user.id'); ?>
            <?php foreach ($users['data'] as $u): ?>
            <tr class="hover:bg-surface-50 transition-colors">
                <td class="table-cell text-ink-muted">#<?php echo $u['id']; ?></td>
                <td class="table-cell font-medium text-ink"><?php echo htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="table-cell">
                    <span class="badge-info"><?php echo htmlspecialchars($u['role_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                </td>
                <td class="table-cell hidden md:table-cell text-ink-muted text-xs">
                    <?php echo $u['last_login_at'] ? date('d M Y, g:i A', strtotime($u['last_login_at'])) : 'Never'; ?>
                </td>
                <td class="table-cell">
                    <span class="<?php echo $u['status'] === 'active' ? 'badge-success' : 'badge-danger'; ?>">
                        <?php echo ucfirst($u['status']); ?>
                    </span>
                </td>
                <td class="table-cell text-right">
                    <?php if ($u['id'] != $currentUserId): ?>
                        <?php if ($u['status'] === 'inactive'): ?>
                        <form method="POST" action="/users/<?php echo $u['id']; ?>/activate" class="inline-flex" onsubmit="return confirm('Activate this user?')">
                            <?php echo \App\Core\CSRF::field(); ?>
                            <button type="submit" class="btn btn-sm btn-primary" title="Activate">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Activate
                            </button>
                        </form>
                        <?php else: ?>
                        <form method="POST" action="/users/<?php echo $u['id']; ?>/deactivate" class="inline-flex" onsubmit="return confirm('Deactivate this user? They will lose access immediately.')">
                            <?php echo \App\Core\CSRF::field(); ?>
                            <button type="submit" class="btn btn-sm btn-danger" title="Deactivate">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                Deactivate
                            </button>
                        </form>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="text-[10px] text-ink-faint italic">Current</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php $baseUrl = '/users'; include __DIR__ . '/../components/pagination.php'; ?>
<?php endif; ?>
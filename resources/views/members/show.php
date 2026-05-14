<div class="page-header">
    <div>
        <h1 class="text-2xl font-display font-bold text-ink">
            <?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name'], ENT_QUOTES, 'UTF-8'); ?>
        </h1>
        <p class="text-sm text-ink-muted mt-1 font-mono"><?php echo htmlspecialchars($member['member_number'], ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
    <div class="flex gap-2">
        <a href="/members/<?php echo $member['id']; ?>/edit" class="btn btn-outline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit
        </a>
        <a href="/members" class="btn btn-outline">Back</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Personal Info -->
    <div class="card p-6 lg:col-span-2">
        <h2 class="text-sm font-semibold text-ink mb-4 pb-3 border-b border-surface-200">Personal Information</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8">
            <div>
                <p class="text-xs text-ink-faint">Full Name</p>
                <p class="text-sm text-ink mt-0.5"><?php echo htmlspecialchars(
                    trim($member['first_name'] . ' ' . ($member['middle_name'] ?? '') . ' ' . $member['last_name']), ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
            <div>
                <p class="text-xs text-ink-faint">Gender</p>
                <p class="text-sm text-ink mt-0.5"><?php echo ucfirst($member['gender'] ?? 'Not specified'); ?></p>
            </div>
            <div>
                <p class="text-xs text-ink-faint">Date of Birth</p>
                <p class="text-sm text-ink mt-0.5"><?php echo $member['date_of_birth'] ? date('d M Y', strtotime($member['date_of_birth'])) : '—'; ?></p>
            </div>
            <div>
                <p class="text-xs text-ink-faint">Phone</p>
                <p class="text-sm text-ink mt-0.5"><?php echo htmlspecialchars($member['phone'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
            <div class="sm:col-span-2">
                <p class="text-xs text-ink-faint">Address</p>
                <p class="text-sm text-ink mt-0.5"><?php echo htmlspecialchars(
                    trim(($member['address'] ?? '') . ', ' . ($member['city'] ?? '') . ', ' . ($member['state'] ?? ''), ', '), ENT_QUOTES, 'UTF-8') ?: '—'; ?></p>
            </div>
            <div>
                <p class="text-xs text-ink-faint">Occupation</p>
                <p class="text-sm text-ink mt-0.5"><?php echo htmlspecialchars($member['occupation'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
            <div>
                <p class="text-xs text-ink-faint">Status</p>
                <?php
                $sc = ['active' => 'badge-success', 'inactive' => 'badge-neutral', 'deceased' => 'badge-danger', 'transferred' => 'badge-warning'];
                ?>
                <span class="<?php echo $sc[$member['status']] ?? 'badge-neutral'; ?> mt-1 inline-block"><?php echo ucfirst($member['status']); ?></span>
            </div>
        </div>
    </div>

    <!-- Groups -->
    <div class="card p-6">
        <h2 class="text-sm font-semibold text-ink mb-4 pb-3 border-b border-surface-200">Group Memberships</h2>
        <?php if (empty($groups)): ?>
        <p class="text-sm text-ink-faint">No group memberships.</p>
        <?php else: ?>
        <div class="space-y-2">
            <?php foreach ($groups as $g): ?>
            <a href="/groups/<?php echo $g['id']; ?>" class="block p-2.5 rounded-lg bg-surface-50 hover:bg-surface-100 transition-colors">
                <p class="text-sm font-medium text-ink"><?php echo htmlspecialchars($g['name'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p class="text-[10px] text-ink-faint mt-0.5"><?php echo ucfirst($g['member_role'] ?? 'Member'); ?></p>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Sacraments -->
<div class="card p-6 mt-6">
    <h2 class="text-sm font-semibold text-ink mb-4 pb-3 border-b border-surface-200">Sacraments</h2>
    <?php if (empty($sacraments)): ?>
    <p class="text-sm text-ink-faint">No sacrament records.</p>
    <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        <?php foreach ($sacraments as $s): ?>
        <div class="p-4 rounded-lg border border-surface-200 bg-surface-50">
            <p class="text-xs font-semibold text-primary-700 uppercase tracking-wider"><?php echo str_replace('_', ' ', $s['type']); ?></p>
            <p class="text-sm text-ink mt-1 font-medium"><?php echo date('d M Y', strtotime($s['date'])); ?></p>
            <?php if ($s['place']): ?>
            <p class="text-xs text-ink-faint mt-0.5"><?php echo htmlspecialchars($s['place'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <?php if ($s['minister']): ?>
            <p class="text-xs text-ink-faint">Minister: <?php echo htmlspecialchars($s['minister'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
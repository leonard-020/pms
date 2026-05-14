<div class="page-header">
    <div>
        <h1 class="text-2xl font-display font-bold text-ink">Events</h1>
        <p class="text-sm text-ink-muted mt-1">Parish events and activities</p>
    </div>
    <?php if (in_array($role ?? '', ['super_admin', 'parish_priest', 'parish_secretary'])): ?>
    <a href="/events/create" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Create Event
    </a>
    <?php endif; ?>
</div>

<form method="GET" action="/events" class="mb-5">
    <div class="relative max-w-sm">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-faint" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" name="search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Search events..." class="form-input pl-9">
    </div>
</form>

<?php if (empty($events['data'])): ?>
<?php
 $emptyIcon = '<svg class="w-12 h-12 text-ink-faint" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>';
 $emptyTitle = 'No events found';
include __DIR__ . '/../components/empty_state.php';
?>
<?php else: ?>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach ($events['data'] as $e): ?>
    <div class="card p-5 hover:shadow-card-hover transition-shadow">
        <div class="flex items-start gap-3">
            <div class="w-12 h-12 rounded-lg bg-primary-50 flex flex-col items-center justify-center flex-shrink-0">
                <span class="text-[10px] font-semibold text-primary-600 uppercase"><?php echo date('M', strtotime($e['event_date'])); ?></span>
                <span class="text-sm font-bold text-primary-800 leading-none"><?php echo date('d', strtotime($e['event_date'])); ?></span>
            </div>
            <div class="min-w-0 flex-1">
                <h3 class="text-sm font-semibold text-ink truncate"><?php echo htmlspecialchars($e['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <?php if ($e['location']): ?>
                <p class="text-xs text-ink-faint mt-0.5"><?php echo htmlspecialchars($e['location'], ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>
                <?php if ($e['start_time']): ?>
                <p class="text-xs text-ink-muted mt-0.5">
                    <?php echo date('g:i A', strtotime($e['start_time'])); ?>
                    <?php if ($e['end_time']): ?> — <?php echo date('g:i A', strtotime($e['end_time'])); ?><?php endif; ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($e['description']): ?>
        <p class="text-xs text-ink-muted mt-3 line-clamp-2"><?php echo htmlspecialchars(substr($e['description'], 0, 120), ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <div class="mt-3 pt-3 border-t border-surface-200 flex items-center justify-between">
            <span class="<?php
                $sc = ['upcoming' => 'badge-info', 'ongoing' => 'badge-success', 'completed' => 'badge-neutral', 'cancelled' => 'badge-danger'];
                echo $sc[$e['status']] ?? 'badge-neutral';
            ?>"><?php echo ucfirst($e['status']); ?></span>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php $baseUrl = '/events'; include __DIR__ . '/../components/pagination.php'; ?>
<?php endif; ?>
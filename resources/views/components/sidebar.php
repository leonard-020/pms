<?php
// Usage: include 'components/sidebar.php';
// Expects: $role (string — role slug)
 $user = \App\Core\Session::get('user');
 $role = $role ?? ($user['role_slug'] ?? 'parish_member');
 $currentUri = '/' . trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/') ?: '/';

function isActive(string $uri, string $current): string
{
    return ($uri === $current || strpos($current, $uri) === 0) ? 'active' : '';
}
?>

<aside id="sidebar" class="fixed top-16 left-0 bottom-0 w-64 bg-white border-r border-surface-200 z-30
                               transform -translate-x-full lg:translate-x-0 transition-transform duration-200
                               overflow-y-auto">
    <div class="p-4 flex flex-col h-full">

        <!-- Brand -->
        <div class="flex items-center gap-3 px-3 pb-5 mb-4 border-b border-surface-200">
            <div class="w-9 h-9 rounded-lg bg-primary-800 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M11 2L4 6v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V6l-7-4zm0 2.18l5 2.88V12c0 4.52-3.13 8.69-7 9.93V4.18z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-display font-semibold text-ink truncate">PMS</p>
                <p class="text-[10px] text-ink-faint truncate">Parish Management</p>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 space-y-1">
            <!-- Dashboard — all authenticated users -->
            <a href="/dashboard" class="sidebar-link <?php echo isActive('/dashboard', $currentUri); ?>">
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"/>
                </svg>
                <span>Dashboard</span>
            </a>

            <?php if (in_array($role, ['super_admin', 'parish_priest', 'parish_secretary', 'system_auditor', 'ministry_leader'])): ?>
            <a href="/members" class="sidebar-link <?php echo isActive('/members', $currentUri); ?>">
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Members</span>
            </a>
            <?php endif; ?>

            <?php if (in_array($role, ['super_admin', 'parish_priest', 'parish_secretary'])): ?>
            <a href="/sacraments" class="sidebar-link <?php echo isActive('/sacraments', $currentUri); ?>">
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span>Sacraments</span>
            </a>
            <?php endif; ?>

            <?php if (in_array($role, ['super_admin', 'parish_priest', 'finance_officer', 'system_auditor'])): ?>
            <a href="/finance" class="sidebar-link <?php echo isActive('/finance', $currentUri); ?>">
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Finance</span>
            </a>
            <?php endif; ?>

            <?php if (in_array($role, ['super_admin', 'parish_priest', 'parish_secretary', 'parish_member', 'ministry_leader'])): ?>
            <a href="/events" class="sidebar-link <?php echo isActive('/events', $currentUri); ?>">
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Events</span>
            </a>
            <?php endif; ?>

            <?php if (in_array($role, ['super_admin', 'ministry_leader', 'parish_priest'])): ?>
            <a href="/groups" class="sidebar-link <?php echo isActive('/groups', $currentUri); ?>">
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span>Groups</span>
            </a>
            <?php endif; ?>

            <!-- Separator -->
            <div class="pt-3 mt-3 border-t border-surface-200"></div>

            <?php if (in_array($role, ['super_admin', 'system_auditor'])): ?>
            <a href="/audit-logs" class="sidebar-link <?php echo isActive('/audit-logs', $currentUri); ?>">
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Audit Logs</span>
            </a>
            <?php endif; ?>

            <?php if ($role === 'super_admin'): ?>
            <a href="/users" class="sidebar-link <?php echo isActive('/users', $currentUri); ?>">
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Users</span>
            </a>
            <?php endif; ?>

            <?php if ($role === 'parish_member'): ?>
            <a href="/profile" class="sidebar-link <?php echo isActive('/profile', $currentUri); ?>">
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>My Profile</span>
            </a>
            <?php endif; ?>
        </nav>

        <!-- Footer -->
        <div class="pt-4 mt-auto border-t border-surface-200">
            <p class="text-[10px] text-ink-faint text-center">PMS v1.0.0</p>
        </div>
    </div>
</aside>

<!-- Sidebar overlay (mobile) -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/30 z-20 hidden lg:hidden" onclick="closeSidebar()"></div>

<script>
function closeSidebar() {
    document.getElementById('sidebar').classList.add('-translate-x-full');
    document.getElementById('sidebar-overlay').classList.add('hidden');
}
document.getElementById('sidebar-toggle')?.addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('-translate-x-full');
    document.getElementById('sidebar-overlay').classList.toggle('hidden');
});
</script>
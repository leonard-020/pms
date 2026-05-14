<?php
// Usage: include 'components/navbar.php';
// Expects: $user (array), $title (string)
 $user = $user ?? \App\Core\Session::get('user');
 $title = $title ?? '';
?>
<nav class="fixed top-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-sm border-b border-surface-200 shadow-nav">
    <div class="flex items-center justify-between h-16 px-4 lg:px-6">
        <!-- Mobile menu toggle -->
        <button id="sidebar-toggle" class="lg:hidden p-2 rounded-lg text-ink-muted hover:bg-surface-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <!-- Page title (mobile) / Breadcrumb area -->
        <div class="hidden sm:block">
            <h2 class="text-sm font-semibold text-ink-light"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h2>
        </div>

        <!-- Right side -->
        <div class="flex items-center gap-3">
            <!-- User info -->
            <div class="hidden sm:flex items-center gap-2 text-sm">
                <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center">
                    <span class="text-xs font-semibold text-primary-700">
                        <?php echo strtoupper(substr($user['email'] ?? 'U', 0, 1)); ?>
                    </span>
                </div>
                <div class="hidden md:block">
                    <p class="text-xs font-medium text-ink-light leading-tight"><?php echo htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                    <p class="text-[10px] text-ink-faint"><?php echo htmlspecialchars($user['role_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </div>

            <!-- Logout -->
            <form method="POST" action="/logout" class="inline-flex">
                <?php echo \App\Core\CSRF::field(); ?>
                <button type="submit" class="p-2 rounded-lg text-ink-muted hover:text-burgundy-500 hover:bg-red-50 transition-colors" title="Sign Out">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</nav>
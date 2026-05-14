<div class="bg-white rounded-2xl shadow-card-hover border border-surface-200 p-8">
    <!-- Logo -->
    <div class="flex flex-col items-center mb-8">
        <div class="w-14 h-14 rounded-2xl bg-primary-800 flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M11 2L4 6v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V6l-7-4z"/>
            </svg>
        </div>
        <h1 class="text-xl font-display font-bold text-ink">Parish Management System</h1>
        <p class="text-xs text-ink-faint mt-1">Sign in to your account</p>
    </div>

    <form method="POST" action="/login">
        <?php echo $_token ?? ''; ?>

        <!-- Email -->
        <div class="mb-4">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" id="email" name="email" required autocomplete="email"
                   value="<?php echo htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input <?php echo !empty($errors['email']) ? 'border-red-400 focus:ring-red-300 focus:border-red-400' : ''; ?>"
                   placeholder="you@parish.com">
            <?php if (!empty($errors['email'])): ?>
            <p class="form-error"><?php echo htmlspecialchars($errors['email'][0], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
        </div>

        <!-- Password -->
        <div class="mb-6">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" required autocomplete="current-password"
                   class="form-input <?php echo !empty($errors['password']) ? 'border-red-400 focus:ring-red-300 focus:border-red-400' : ''; ?>"
                   placeholder="Enter your password">
            <?php if (!empty($errors['password'])): ?>
            <p class="form-error"><?php echo htmlspecialchars($errors['password'][0], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
        </div>

        <!-- Submit -->
        <button type="submit" class="btn btn-primary w-full">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            Sign In
        </button>
    </form>

    <!-- Demo credentials -->
    <!-- <div class="mt-6 p-4 rounded-lg bg-surface-100 border border-surface-200">
        <p class="text-[10px] font-semibold text-ink-muted uppercase tracking-wider mb-2">Demo Accounts</p>
        <div class="space-y-1 text-[11px] text-ink-muted">
            <p><span class="font-medium text-ink-light">Super Admin:</span> admin@parish.com</p>
            <p><span class="font-medium text-ink-light">Parish Priest:</span> priest@parish.com</p>
            <p><span class="font-medium text-ink-light">Finance:</span> finance@parish.com</p>
            <p><span class="font-medium text-ink-light">Secretary:</span> secretary@parish.com</p>
            <p><span class="font-medium text-ink-light">Auditor:</span> auditor@parish.com</p>
            <p class="pt-1 text-ink-faint">Password: <code class="bg-white px-1.5 py-0.5 rounded text-ink-light">Password@123</code></p>
        </div>
    </div> -->
</div>
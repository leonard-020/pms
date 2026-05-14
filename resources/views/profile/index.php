<div class="page-header">
    <div>
        <h1 class="text-2xl font-display font-bold text-ink">My Profile</h1>
        <p class="text-sm text-ink-muted mt-1">View and update your information</p>
    </div>
</div>

<?php include __DIR__ . '/../components/notifications.php'; ?>

<!-- Account Info -->
<div class="card p-6 mb-6">
    <h2 class="text-sm font-semibold text-ink mb-4 pb-3 border-b border-surface-200">Account Information</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8">
        <div>
            <p class="text-xs text-ink-faint">Email</p>
            <p class="text-sm text-ink mt-0.5"><?php echo htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div>
            <p class="text-xs text-ink-faint">Role</p>
            <p class="text-sm text-ink mt-0.5"><?php echo htmlspecialchars($user['role_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
    </div>
</div>

<?php if ($member): ?>
<!-- Member Profile (editable) -->
<form method="POST" action="/profile" class="card p-6">
    <h2 class="text-sm font-semibold text-ink mb-4 pb-3 border-b border-surface-200">Personal Information</h2>
    <?php echo \App\Core\CSRF::field(); ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="form-label">Full Name</label>
            <input type="text" disabled
                   value="<?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name'], ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input bg-surface-100">
            <p class="text-[10px] text-ink-faint mt-1">Contact the secretary to change your name.</p>
        </div>
        <div>
            <label class="form-label">Parish ID</label>
            <input type="text" disabled
                   value="<?php echo htmlspecialchars($member['member_number'], ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input bg-surface-100 font-mono">
        </div>
        <div>
            <label for="phone" class="form-label">Phone Number</label>
            <input type="tel" id="phone" name="phone"
                   value="<?php echo htmlspecialchars($member['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input">
        </div>
        <div>
            <label for="occupation" class="form-label">Occupation</label>
            <input type="text" id="occupation" name="occupation"
                   value="<?php echo htmlspecialchars($member['occupation'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input">
        </div>
        <div class="md:col-span-2">
            <label for="address" class="form-label">Address</label>
            <input type="text" id="address" name="address"
                   value="<?php echo htmlspecialchars($member['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input">
        </div>
        <div>
            <label for="city" class="form-label">City</label>
            <input type="text" id="city" name="city"
                   value="<?php echo htmlspecialchars($member['city'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input">
        </div>
        <div>
            <label for="state" class="form-label">State</label>
            <input type="text" id="state" name="state"
                   value="<?php echo htmlspecialchars($member['state'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input">
        </div>
    </div>

    <div class="mt-6 pt-5 border-t border-surface-200">
        <button type="submit" class="btn btn-primary">Update Profile</button>
    </div>
</form>
<?php else: ?>
<div class="card p-6">
    <div class="text-center py-8">
        <svg class="w-12 h-12 text-ink-faint mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
        <p class="text-sm text-ink-muted">No member profile linked to your account.</p>
        <p class="text-xs text-ink-faint mt-1">Contact the parish secretary to set up your member profile.</p>
    </div>
</div>
<?php endif; ?>
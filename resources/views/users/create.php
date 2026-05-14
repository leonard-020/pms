<div class="page-header">
    <div>
        <h1 class="text-2xl font-display font-bold text-ink">Create User</h1>
        <p class="text-sm text-ink-muted mt-1">Add a new system account</p>
    </div>
    <a href="/users" class="btn btn-outline">Back</a>
</div>

<form method="POST" action="/users" class="card p-6 max-w-lg">
    <?php echo $_token ?? ''; ?>

    <div class="space-y-4">
        <div>
            <label for="email" class="form-label">Email Address <span class="text-burgundy-500">*</span></label>
            <input type="email" id="email" name="email" required
                   value="<?php echo htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input" placeholder="user@parish.com">
        </div>

        <div>
            <label for="role_id" class="form-label">Role <span class="text-burgundy-500">*</span></label>
            <select id="role_id" name="role_id" required class="form-input">
                <option value="">Select Role...</option>
                <?php foreach ($roles as $r): ?>
                <option value="<?php echo $r['id']; ?>" <?php echo ($old['role_id'] ?? '') == $r['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($r['name'] . ' — ' . $r['description'], ENT_QUOTES, 'UTF-8'); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="password" class="form-label">Password <span class="text-burgundy-500">*</span></label>
            <input type="password" id="password" name="password" required minlength="8"
                   class="form-input" placeholder="Min. 8 characters">
        </div>

        <div>
            <label for="password_confirmation" class="form-label">Confirm Password <span class="text-burgundy-500">*</span></label>
            <input type="password" id="password_confirmation" name="password_confirmation" required
                   class="form-input" placeholder="Re-enter password">
        </div>
    </div>

    <div class="flex gap-3 mt-6 pt-5 border-t border-surface-200">
        <button type="submit" class="btn btn-primary">Create User</button>
        <a href="/users" class="btn btn-outline">Cancel</a>
    </div>
</form>
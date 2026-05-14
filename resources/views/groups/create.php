<div class="page-header">
    <div>
        <h1 class="text-2xl font-display font-bold text-ink">Create Group</h1>
        <p class="text-sm text-ink-muted mt-1">Add a new ministry or group</p>
    </div>
    <a href="/groups" class="btn btn-outline">Back</a>
</div>

<form method="POST" action="/groups" class="card p-6 max-w-lg">
    <?php echo $_token ?? ''; ?>

    <div class="space-y-4">
        <div>
            <label for="name" class="form-label">Group Name <span class="text-burgundy-500">*</span></label>
            <input type="text" id="name" name="name" required
                   value="<?php echo htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input" placeholder="e.g., Altar Servers, CMO">
        </div>

        <div>
            <label for="leader_id" class="form-label">Group Leader</label>
            <select id="leader_id" name="leader_id" class="form-input">
                <option value="">Select Leader (optional)...</option>
                <?php foreach ($members as $m): ?>
                <option value="<?php echo $m['id']; ?>" <?php echo ($old['leader_id'] ?? '') == $m['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($m['member_number'] . ' — ' . $m['first_name'] . ' ' . $m['last_name'], ENT_QUOTES, 'UTF-8'); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" rows="3"
                      class="form-input"><?php echo htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>
    </div>

    <div class="flex gap-3 mt-6 pt-5 border-t border-surface-200">
        <button type="submit" class="btn btn-primary">Create Group</button>
        <a href="/groups" class="btn btn-outline">Cancel</a>
    </div>
</form>
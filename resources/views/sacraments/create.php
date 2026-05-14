<div class="page-header">
    <div>
        <h1 class="text-2xl font-display font-bold text-ink">Record Sacrament</h1>
        <p class="text-sm text-ink-muted mt-1">Add a sacramental record</p>
    </div>
    <a href="/sacraments" class="btn btn-outline">Back</a>
</div>

<form method="POST" action="/sacraments" class="card p-6 max-w-2xl">
    <?php echo $_token ?? ''; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="md:col-span-2">
            <label for="member_id" class="form-label">Member <span class="text-burgundy-500">*</span></label>
            <select id="member_id" name="member_id" required class="form-input">
                <option value="">Select Member...</option>
                <?php foreach ($members as $m): ?>
                <option value="<?php echo $m['id']; ?>" <?php echo ($old['member_id'] ?? '') == $m['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($m['member_number'] . ' — ' . $m['first_name'] . ' ' . $m['last_name'], ENT_QUOTES, 'UTF-8'); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="type" class="form-label">Sacrament <span class="text-burgundy-500">*</span></label>
            <select id="type" name="type" required class="form-input">
                <option value="">Select...</option>
                <?php foreach (['baptism','first_communion','confirmation','marriage','holy_orders','anointing_sick'] as $t): ?>
                <option value="<?php echo $t; ?>" <?php echo ($old['type'] ?? '') === $t ? 'selected' : ''; ?>>
                    <?php echo ucwords(str_replace('_', ' ', $t)); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="date" class="form-label">Date <span class="text-burgundy-500">*</span></label>
            <input type="date" id="date" name="date" required
                   value="<?php echo htmlspecialchars($old['date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input">
        </div>

        <div>
            <label for="minister" class="form-label">Minister</label>
            <input type="text" id="minister" name="minister"
                   value="<?php echo htmlspecialchars($old['minister'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input">
        </div>

        <div>
            <label for="witness_name" class="form-label">Godparent / Sponsor / Spouse</label>
            <input type="text" id="witness_name" name="witness_name"
                   value="<?php echo htmlspecialchars($old['witness_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input">
        </div>

        <div>
            <label for="place" class="form-label">Place</label>
            <input type="text" id="place" name="place"
                   value="<?php echo htmlspecialchars($old['place'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input">
        </div>

        <div class="md:col-span-2">
            <label for="notes" class="form-label">Notes</label>
            <textarea id="notes" name="notes" rows="3"
                      class="form-input"><?php echo htmlspecialchars($old['notes'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>
    </div>

    <div class="flex gap-3 mt-6 pt-5 border-t border-surface-200">
        <button type="submit" class="btn btn-primary">Record Sacrament</button>
        <a href="/sacraments" class="btn btn-outline">Cancel</a>
    </div>
</form>
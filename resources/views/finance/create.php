<div class="page-header">
    <div>
        <h1 class="text-2xl font-display font-bold text-ink">Record Transaction</h1>
        <p class="text-sm text-ink-muted mt-1">Submit for approval</p>
    </div>
    <a href="/finance" class="btn btn-outline">Back</a>
</div>

<form method="POST" action="/finance" class="card p-6">
    <?php echo $_token ?? ''; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label for="type" class="form-label">Type <span class="text-burgundy-500">*</span></label>
            <select id="type" name="type" required class="form-input">
                <option value="">Select...</option>
                <option value="income" <?php echo ($old['type'] ?? '') === 'income' ? 'selected' : ''; ?>>Income</option>
                <option value="expense" <?php echo ($old['type'] ?? '') === 'expense' ? 'selected' : ''; ?>>Expense</option>
            </select>
        </div>

        <div>
            <label for="category" class="form-label">Category <span class="text-burgundy-500">*</span></label>
            <input type="text" id="category" name="category" required list="category-list"
                   value="<?php echo htmlspecialchars($old['category'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input" placeholder="e.g., Tithe, Offering, Salary">
            <datalist id="category-list">
                <option value="Tithe">
                <option value="Sunday Offering">
                <option value="Thanksgiving">
                <option value="Donation">
                <option value="Salary">
                <option value="Maintenance">
                <option value="Utilities">
                <option value="Church Building">
                <option value="Welfare">
                <option value="Other">
            </datalist>
        </div>

        <div>
            <label for="amount" class="form-label">Amount (₦) <span class="text-burgundy-500">*</span></label>
            <input type="number" id="amount" name="amount" required step="0.01" min="0.01"
                   value="<?php echo htmlspecialchars($old['amount'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input" placeholder="0.00">
        </div>

        <div>
            <label for="transaction_date" class="form-label">Transaction Date <span class="text-burgundy-500">*</span></label>
            <input type="date" id="transaction_date" name="transaction_date" required
                   value="<?php echo htmlspecialchars($old['transaction_date'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input">
        </div>

        <div class="md:col-span-2">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" rows="2"
                      class="form-input"><?php echo htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>

        <div class="md:col-span-2">
            <label for="notes" class="form-label">Additional Notes</label>
            <textarea id="notes" name="notes" rows="2"
                      class="form-input"><?php echo htmlspecialchars($old['notes'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>
    </div>

    <div class="flex gap-3 mt-6 pt-5 border-t border-surface-200">
        <button type="submit" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Submit for Approval
        </button>
        <a href="/finance" class="btn btn-outline">Cancel</a>
    </div>
</form>
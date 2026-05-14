<div class="page-header">
    <div>
        <h1 class="text-2xl font-display font-bold text-ink">Edit Member</h1>
        <p class="text-sm text-ink-muted mt-1 font-mono"><?php echo htmlspecialchars($member['member_number'], ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
    <a href="/members/<?php echo $member['id']; ?>" class="btn btn-outline">Cancel</a>
</div>

<form method="POST" action="/members/<?php echo $member['id']; ?>" class="card p-6">
    <?php echo $_token ?? ''; ?>
    <input type="hidden" name="_method" value="PUT">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <div>
            <label for="first_name" class="form-label">First Name <span class="text-burgundy-500">*</span></label>
            <input type="text" id="first_name" name="first_name" required
                   value="<?php echo htmlspecialchars($old['first_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input">
        </div>
        <div>
            <label for="last_name" class="form-label">Last Name <span class="text-burgundy-500">*</span></label>
            <input type="text" id="last_name" name="last_name" required
                   value="<?php echo htmlspecialchars($old['last_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input">
        </div>
        <div>
            <label for="middle_name" class="form-label">Middle Name</label>
            <input type="text" id="middle_name" name="middle_name"
                   value="<?php echo htmlspecialchars($old['middle_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input">
        </div>
        <div>
            <label for="date_of_birth" class="form-label">Date of Birth</label>
            <input type="date" id="date_of_birth" name="date_of_birth"
                   value="<?php echo htmlspecialchars($old['date_of_birth'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input">
        </div>
        <div>
            <label for="gender" class="form-label">Gender</label>
            <select id="gender" name="gender" class="form-input">
                <option value="">Select...</option>
                <option value="male" <?php echo ($old['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>Male</option>
                <option value="female" <?php echo ($old['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
            </select>
        </div>
        <div>
            <label for="phone" class="form-label">Phone</label>
            <input type="tel" id="phone" name="phone"
                   value="<?php echo htmlspecialchars($old['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input">
        </div>
        <div class="md:col-span-2 lg:col-span-3">
            <label for="address" class="form-label">Address</label>
            <input type="text" id="address" name="address"
                   value="<?php echo htmlspecialchars($old['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input">
        </div>
        <div>
            <label for="city" class="form-label">City</label>
            <input type="text" id="city" name="city"
                   value="<?php echo htmlspecialchars($old['city'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input">
        </div>
        <div>
            <label for="state" class="form-label">State</label>
            <input type="text" id="state" name="state"
                   value="<?php echo htmlspecialchars($old['state'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input">
        </div>
        <div>
            <label for="occupation" class="form-label">Occupation</label>
            <input type="text" id="occupation" name="occupation"
                   value="<?php echo htmlspecialchars($old['occupation'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input">
        </div>
    </div>

    <div class="flex gap-3 mt-6 pt-5 border-t border-surface-200">
        <button type="submit" class="btn btn-primary">Update Member</button>
        <a href="/members/<?php echo $member['id']; ?>" class="btn btn-outline">Cancel</a>
    </div>
</form>
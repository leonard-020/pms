<div class="page-header">
    <div>
        <h1 class="text-2xl font-display font-bold text-ink">Register New Member</h1>
        <p class="text-sm text-ink-muted mt-1">Add a new parishioner to the system</p>
    </div>
    <a href="/members" class="btn btn-outline">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back
    </a>
</div>

<form method="POST" action="/members" class="card p-6">
    <?php echo $_token ?? ''; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <!-- First Name -->
        <div>
            <label for="first_name" class="form-label">First Name <span class="text-burgundy-500">*</span></label>
            <input type="text" id="first_name" name="first_name" required
                   value="<?php echo htmlspecialchars($old['first_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input">
        </div>

        <!-- Last Name -->
        <div>
            <label for="last_name" class="form-label">Last Name <span class="text-burgundy-500">*</span></label>
            <input type="text" id="last_name" name="last_name" required
                   value="<?php echo htmlspecialchars($old['last_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input">
        </div>

        <!-- Middle Name -->
        <div>
            <label for="middle_name" class="form-label">Middle Name</label>
            <input type="text" id="middle_name" name="middle_name"
                   value="<?php echo htmlspecialchars($old['middle_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input">
        </div>

        <!-- Date of Birth -->
        <div>
            <label for="date_of_birth" class="form-label">Date of Birth</label>
            <input type="date" id="date_of_birth" name="date_of_birth"
                   value="<?php echo htmlspecialchars($old['date_of_birth'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input">
        </div>

        <!-- Gender -->
        <div>
            <label for="gender" class="form-label">Gender</label>
            <select id="gender" name="gender" class="form-input">
                <option value="">Select...</option>
                <option value="male" <?php echo ($old['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>Male</option>
                <option value="female" <?php echo ($old['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
            </select>
        </div>

        <!-- Phone -->
        <div>
            <label for="phone" class="form-label">Phone Number</label>
            <input type="tel" id="phone" name="phone"
                   value="<?php echo htmlspecialchars($old['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input" placeholder="+234...">
        </div>

        <!-- Address — full width -->
        <div class="md:col-span-2 lg:col-span-3">
            <label for="address" class="form-label">Address</label>
            <input type="text" id="address" name="address"
                   value="<?php echo htmlspecialchars($old['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input">
        </div>

        <!-- City -->
        <div>
            <label for="city" class="form-label">City</label>
            <input type="text" id="city" name="city"
                   value="<?php echo htmlspecialchars($old['city'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input">
        </div>

        <!-- State -->
        <div>
            <label for="state" class="form-label">State</label>
            <input type="text" id="state" name="state"
                   value="<?php echo htmlspecialchars($old['state'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input">
        </div>

        <!-- Occupation -->
        <div>
            <label for="occupation" class="form-label">Occupation</label>
            <input type="text" id="occupation" name="occupation"
                   value="<?php echo htmlspecialchars($old['occupation'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input">
        </div>
    </div>

    <div class="flex gap-3 mt-6 pt-5 border-t border-surface-200">
        <button type="submit" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Register Member
        </button>
        <a href="/members" class="btn btn-outline">Cancel</a>
    </div>
</form>
<div class="page-header">
    <div>
        <h1 class="text-2xl font-display font-bold text-ink">Create Event</h1>
        <p class="text-sm text-ink-muted mt-1">Schedule a new parish event</p>
    </div>
    <a href="/events" class="btn btn-outline">Back</a>
</div>

<form method="POST" action="/events" class="card p-6 max-w-2xl">
    <?php echo $_token ?? ''; ?>

    <div class="space-y-4">
        <div>
            <label for="title" class="form-label">Event Title <span class="text-burgundy-500">*</span></label>
            <input type="text" id="title" name="title" required
                   value="<?php echo htmlspecialchars($old['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input" placeholder="e.g., Parish Feast Day">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label for="event_date" class="form-label">Date <span class="text-burgundy-500">*</span></label>
                <input type="date" id="event_date" name="event_date" required
                       value="<?php echo htmlspecialchars($old['event_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input">
            </div>
            <div>
                <label for="start_time" class="form-label">Start Time</label>
                <input type="time" id="start_time" name="start_time"
                       value="<?php echo htmlspecialchars($old['start_time'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input">
            </div>
            <div>
                <label for="end_time" class="form-label">End Time</label>
                <input type="time" id="end_time" name="end_time"
                       value="<?php echo htmlspecialchars($old['end_time'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input">
            </div>
        </div>

        <div>
            <label for="location" class="form-label">Location</label>
            <input type="text" id="location" name="location"
                   value="<?php echo htmlspecialchars($old['location'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input" placeholder="e.g., Church Hall">
        </div>

        <div>
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" rows="4"
                      class="form-input"><?php echo htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>
    </div>

    <div class="flex gap-3 mt-6 pt-5 border-t border-surface-200">
        <button type="submit" class="btn btn-primary">Create Event</button>
        <a href="/events" class="btn btn-outline">Cancel</a>
    </div>
</form>
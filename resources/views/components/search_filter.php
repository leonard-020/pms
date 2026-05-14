<?php
// Usage: include 'components/search_filter.php';
// Expects: $searchAction (string), $search (string), $filters (array of [name => value, label => label, options => [...]])
?>
<form method="GET" action="<?php echo htmlspecialchars($searchAction, ENT_QUOTES, 'UTF-8'); ?>"
      class="flex flex-col sm:flex-row gap-3 mb-5">
    <!-- Search input -->
    <div class="relative flex-1 min-w-0">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-faint" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" name="search" value="<?php echo htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8'); ?>"
               placeholder="Search..."
               class="form-input pl-9">
    </div>

    <?php foreach ($filters ?? [] as $f): ?>
    <select name="<?php echo htmlspecialchars($f['name'], ENT_QUOTES, 'UTF-8'); ?>" class="form-input w-full sm:w-auto">
        <option value=""><?php echo htmlspecialchars($f['label'] ?? 'All', ENT_QUOTES, 'UTF-8'); ?></option>
        <?php foreach ($f['options'] ?? [] as $val => $label): ?>
        <option value="<?php echo htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?>"
            <?php echo (isset($_GET[$f['name']]) && $_GET[$f['name']] == $val) ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
        </option>
        <?php endforeach; ?>
    </select>
    <?php endforeach; ?>

    <div class="flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
            </svg>
            Filter
        </button>
        <a href="<?php echo htmlspecialchars($searchAction, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-outline btn-sm">Clear</a>
    </div>
</form>
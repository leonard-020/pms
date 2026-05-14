<?php
// Usage: include 'components/pagination.php';
// Expects: $paginator (array with data, total, page, per_page, last_page)
//          $baseUrl (string — e.g., '/members')
//          $queryParams (array — optional extra query params)
 $p = $paginator ?? [];
 $baseUrl = $baseUrl ?? '/members';
 $queryParams = $queryParams ?? [];
 $current = $p['page'] ?? 1;
 $last = $p['last_page'] ?? 1;
 $total = $p['total'] ?? 0;
 $from = $p['from'] ?? 0;
 $to = $p['to'] ?? 0;

if ($last <= 1) return;

function buildPageUrl(int $page, string $base, array $params): string
{
    $params['page'] = $page;
    return $base . '?' . http_build_query($params);
}
?>

<div class="flex flex-col sm:flex-row items-center justify-between gap-3 mt-6 px-1">
    <p class="text-xs text-ink-faint">
        Showing <span class="font-medium text-ink-light"><?php echo $from; ?></span>
        to <span class="font-medium text-ink-light"><?php echo $to; ?></span>
        of <span class="font-medium text-ink-light"><?php echo number_format($total); ?></span> results
    </p>

    <div class="flex items-center gap-1">
        <!-- Previous -->
        <?php if ($current > 1): ?>
        <a href="<?php echo buildPageUrl($current - 1, $baseUrl, $queryParams); ?>"
           class="btn btn-sm btn-outline">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Prev
        </a>
        <?php else: ?>
        <span class="btn btn-sm btn-outline opacity-40 cursor-not-allowed">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Prev
        </span>
        <?php endif; ?>

        <!-- Page numbers -->
        <?php
        $start = max(1, $current - 2);
        $end = min($last, $current + 2);
        if ($start > 1) {
            echo '<a href="' . buildPageUrl(1, $baseUrl, $queryParams) . '" class="btn btn-sm btn-outline">1</a>';
            if ($start > 2) echo '<span class="px-1 text-ink-faint text-xs">...</span>';
        }
        for ($i = $start; $i <= $end; $i++):
            if ($i === $current):
        ?>
            <span class="btn btn-sm bg-primary-700 text-white cursor-default"><?php echo $i; ?></span>
        <?php else: ?>
            <a href="<?php echo buildPageUrl($i, $baseUrl, $queryParams); ?>" class="btn btn-sm btn-outline"><?php echo $i; ?></a>
        <?php endif; endfor;
        if ($end < $last) {
            if ($end < $last - 1) echo '<span class="px-1 text-ink-faint text-xs">...</span>';
            echo '<a href="' . buildPageUrl($last, $baseUrl, $queryParams) . '" class="btn btn-sm btn-outline">' . $last . '</a>';
        }
        ?>

        <!-- Next -->
        <?php if ($current < $last): ?>
        <a href="<?php echo buildPageUrl($current + 1, $baseUrl, $queryParams); ?>"
           class="btn btn-sm btn-outline">
            Next
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
        <?php else: ?>
        <span class="btn btn-sm btn-outline opacity-40 cursor-not-allowed">
            Next
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </span>
        <?php endif; ?>
    </div>
</div>
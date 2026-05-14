<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(($title ?? 'Dashboard') . ' — PMS', ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="min-h-screen">
    <?php include __DIR__ . '/../components/navbar.php'; ?>
    <?php include __DIR__ . '/../components/sidebar.php'; ?>

    <!-- Main content area -->
    <main class="pt-16 lg:pl-64 min-h-screen">
        <div class="p-4 md:p-6 lg:p-8">
            <?php include __DIR__ . '/../components/notifications.php'; ?>
            <?php echo $content ?? ''; ?>
        </div>
    </main>

    <!-- Modal container (for reject modal, etc.) -->
    <?php if (in_array($role ?? '', ['super_admin', 'parish_priest'])): ?>
    <?php include __DIR__ . '/../components/modals/reject_transaction.php'; ?>
    <?php endif; ?>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo htmlspecialchars($title ?? 'PMS', ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="min-h-screen flex items-center justify-center bg-surface-100">
    <!-- Decorative background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-100 rounded-full opacity-30 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gold-100 rounded-full opacity-30 blur-3xl"></div>
    </div>

    <main class="relative w-full max-w-md mx-4">
        <?php include __DIR__ . '/../components/notifications.php'; ?>
        <?php echo $content ?? ''; ?>
    </main>
</body>
</html>
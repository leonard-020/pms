<?php

namespace App\Core;

use ErrorException;

class ErrorHandler
{
    public static function register(): void
    {
        error_reporting(E_ALL);

        set_error_handler(function ($severity, $message, $file, $line) {
            if (!(error_reporting() & $severity)) {
                return;
            }
            throw new ErrorException($message, 0, $severity, $file, $line);
        });

        set_exception_handler(function (\Throwable $e) {
            $config = require dirname(__DIR__, 2) . '/config/app.php';

            if ($config['debug']) {
                echo '<div style="padding:20px;font-family:monospace;background:#1a1a2e;color:#fff;">';
                echo '<h2 style="color:#F87171;">' . get_class($e) . '</h2>';
                echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '<p style="color:#9CA3AF;">' . $e->getFile() . ':' . $e->getLine() . '</p>';
                echo '<pre style="margin-top:10px;padding:10px;background:#0F172A;border-radius:8px;overflow:auto;">';
                echo htmlspecialchars($e->getTraceAsString());
                echo '</pre></div>';
            } else {
                // Log to file
                $log = sprintf(
                    "[%s] %s in %s:%d\n%s\n",
                    date('Y-m-d H:i:s'),
                    get_class($e),
                    $e->getFile(),
                    $e->getLine(),
                    $e->getMessage()
                );
                @file_put_contents(dirname(__DIR__, 2) . '/storage/logs/error.log', $log, FILE_APPEND);
                Response::error(500);
            }
            exit;
        });
    }
}
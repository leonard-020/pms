<?php

namespace App\Core;

class Response
{
    public static function redirect(string $url, int $code = 302): void
    {
        // Automatically prepend base path if running in a subfolder
        $base = defined('BASE_URL') ? BASE_URL : '';
        if (strpos($url, '/') === 0 && $base) {
            $url = $base . $url;
        }

        http_response_code($code);
        header("Location: {$url}");
        exit;
    }

    public static function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        self::redirect($referer);
    }

    public static function json(array $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function error(int $code): void
    {
        http_response_code($code);
        $viewPath = dirname(__DIR__, 2) . "/resources/views/errors/{$code}.php";

        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "<h1>Error {$code}</h1>";
        }
        exit;
    }
}
<?php

namespace App\Core;

class Session
{
    private static bool $started = false;

    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $config = require dirname(__DIR__, 2) . '/config/app.php';
        $sess = $config['session'];

        ini_set('session.cookie_httponly', $sess['cookie_httponly'] ? '1' : '0');
        ini_set('session.use_strict_mode', '1');
        ini_set('session.cookie_samesite', $sess['cookie_samesite']);

        session_name($sess['name']);
        session_set_cookie_params([
            'lifetime'  => $sess['lifetime'],
            'path'      => '/',
            'secure'    => $sess['cookie_secure'],
            'httponly'  => $sess['cookie_httponly'],
            'samesite'  => $sess['cookie_samesite'],
        ]);

        session_start();
        self::$started = true;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, mixed $value = null): mixed
    {
        self::start();

        if ($value !== null) {
            $_SESSION["_flash_{$key}"] = $value;
            return $value;
        }

        $val = $_SESSION["_flash_{$key}"] ?? null;
        unset($_SESSION["_flash_{$key}"]);
        return $val;
    }

    public static function regenerateId(): bool
    {
        self::start();
        return session_regenerate_id(true);
    }

    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        self::$started = false;
    }

    public static function id(): string
    {
        self::start();
        return session_id();
    }
}
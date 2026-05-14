<?php

namespace App\Core;

class Cache
{
    private static string $path;

    public static function init(): void
    {
        self::$path = dirname(__DIR__, 2) . '/storage/cache/';
        if (!is_dir(self::$path)) {
            mkdir(self::$path, 0755, true);
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $file = self::$path . md5($key) . '.cache';
        if (!file_exists($file)) {
            return $default;
        }

        $data = unserialize(file_get_contents($file));
        if ($data['expires'] !== 0 && $data['expires'] < time()) {
            @unlink($file);
            return $default;
        }

        return $data['value'];
    }

    public static function set(string $key, mixed $value, int $ttl = 3600): void
    {
        $file = self::$path . md5($key) . '.cache';
        $data = [
            'value'   => $value,
            'expires' => $ttl === 0 ? 0 : time() + $ttl,
        ];
        file_put_contents($file, serialize($data), LOCK_EX);
    }

    public static function forget(string $key): void
    {
        $file = self::$path . md5($key) . '.cache';
        @unlink($file);
    }

    public static function flush(): void
    {
        $files = glob(self::$path . '*.cache');
        foreach ($files as $file) {
            @unlink($file);
        }
    }
}
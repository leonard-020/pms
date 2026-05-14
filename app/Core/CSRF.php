<?php

namespace App\Core;

class CSRF
{
    private const TOKEN_KEY = '_csrf_token';
    private const TOKEN_AGE = 3600; // 1 hour

    public static function generate(): string
    {
        $token = bin2hex(random_bytes(32));
        Session::set(self::TOKEN_KEY, [
            'token' => $token,
            'expires' => time() + self::TOKEN_AGE,
        ]);
        return $token;
    }

    public static function token(): string
    {
        $data = Session::get(self::TOKEN_KEY);

        if (!$data || time() > $data['expires']) {
            return self::generate();
        }

        return $data['token'];
    }

    public static function field(): string
    {
        $token = self::token();
        return '<input type="hidden" name="_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function validate(string $submittedToken): bool
    {
        $data = Session::get(self::TOKEN_KEY);

        if (!$data || time() > $data['expires']) {
            return false;
        }

        return hash_equals($data['token'], $submittedToken);
    }

    public static function check(): bool
    {
        $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        return self::validate($token);
    }
}
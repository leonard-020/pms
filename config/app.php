<?php

return [
    'name' => 'Parish Management System',
    'version' => '1.0.0',
    'env' => getenv('APP_ENV') ?: 'production',
    'debug' => (getenv('APP_DEBUG') === 'false'),
    // 'debug' => true, // Once login appear change to false
    'timezone' => 'UTC',
    'url' => getenv('APP_URL') ?: 'http://localhost/pms/public',
    'session' => [
        'lifetime' => 7200,
        'name' => 'pms_session',
        'cookie_httponly' => true,
        'cookie_secure' => false,
        'cookie_samesite' => 'Lax',
    ],
    'pagination' => [
        'per_page' => 15,
    ],
];
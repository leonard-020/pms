<?php

namespace App\Services;

use App\Core\Session;
use App\Core\CSRF;
use App\Core\Validator;
use App\Core\Response;
use App\Core\Cache;
use App\Models\User;
use App\Models\AuditLog;

class AuthService
{
    private User $userModel;
    private AuditLog $auditLog;

    public function __construct()
    {
        $this->userModel = new User();
        $this->auditLog = new AuditLog();
    }

    public function login(array $credentials): array
    {
        // Validate
        $validator = new Validator($credentials, [
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (!$validator->validate()) {
            return ['success' => false, 'errors' => $validator->errors()];
        }

        // CSRF check
        if (!CSRF::check()) {
            return ['success' => false, 'errors' => ['_token' => ['Invalid security token. Please try again.']]];
        }

        // Find user
        $user = $this->userModel->findByEmail($credentials['email']);

        if (!$user || !password_verify($credentials['password'], $user['password'])) {
            return ['success' => false, 'errors' => ['email' => ['Invalid email or password.']]];
        }

        // Check status
        if ($user['status'] !== 'active') {
            return ['success' => false, 'errors' => ['email' => ['Your account is ' . $user['status'] . '. Contact the administrator.']]];
        }

        // Regenerate session ID to prevent fixation
        Session::regenerateId();

        // Store user in session (exclude password)
        $sessionUser = [
            'id'       => (int) $user['id'],
            'email'    => $user['email'],
            'role_id'  => (int) $user['role_id'],
            'role_name'=> $user['role_name'],
            'role_slug'=> $user['role_slug'],
            'status'   => $user['status'],
        ];
        Session::set('user', $sessionUser);

        // Update last login
        $this->userModel->updateLogin($user['id'], $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');

        // Audit log
        $this->auditLog->log(
            $user['id'], 'login', 'auth',
            "User {$user['email']} logged in",
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        // Clear role permission cache on login
        Cache::forget("role_permissions_{$user['role_id']}");

        return ['success' => true, 'user' => $sessionUser];
    }

    public function logout(): void
    {
        $user = Session::get('user');
        if ($user) {
            $this->auditLog->log(
                $user['id'], 'logout', 'auth',
                "User {$user['email']} logged out",
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            );
        }
        Session::destroy();
    }
}
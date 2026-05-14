<?php

namespace App\Services;

use App\Core\Validator;
use App\Core\CSRF;
use App\Core\Cache;
use App\Models\User;

class UserService
{
    private User $userModel;
    private AuditService $audit;

    public function __construct()
    {
        $this->userModel = new User();
        $this->audit = new AuditService();
    }

    public function create(array $data): array
    {
        if (!CSRF::check()) {
            return ['success' => false, 'errors' => ['_token' => ['Invalid security token.']]];
        }

        $validator = new Validator($data, [
            'email'    => 'required|email|max:191',
            'password' => 'required|min:8|confirmed',
            'role_id'  => 'required|numeric',
        ]);

        if (!$validator->validate()) {
            return ['success' => false, 'errors' => $validator->errors()];
        }

        // Check unique email
        if ($this->userModel->findByEmail($data['email'])) {
            return ['success' => false, 'errors' => ['email' => ['This email is already registered.']]];
        }

        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        $data['status'] = 'active';

        $id = $this->userModel->create($data);

        // Clear cache
        Cache::flush();

        $this->audit->log('create', 'users', "Created user account: {$data['email']}");

        return ['success' => true, 'id' => $id];
    }

    public function activate(int $id): array
    {
        $currentUserId = \App\Core\Session::get('user.id');

        // Prevent self-activation (shouldn't be needed, but safety)
        if ($id == $currentUserId) {
            return ['success' => false, 'message' => 'Cannot modify your own account status.'];
        }

        $user = $this->userModel->find($id, true); // withTrashed
        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        $oldStatus = $user['status'];
        $this->userModel->update($id, ['status' => 'active', 'deleted_at' => null]);

        $this->audit->log('activate', 'users', "Activated user {$user['email']} (was: {$oldStatus})");

        return ['success' => true, 'message' => 'User activated successfully.'];
    }

    public function deactivate(int $id): array
    {
        $currentUserId = \App\Core\Session::get('user.id');

        // Prevent self-deactivation — CRITICAL
        if ($id == $currentUserId) {
            return ['success' => false, 'message' => 'You cannot deactivate your own account.'];
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        $oldStatus = $user['status'];
        $this->userModel->softDelete($id);

        $this->audit->log('deactivate', 'users', "Deactivated user {$user['email']} (was: {$oldStatus})");

        return ['success' => true, 'message' => 'User deactivated successfully.'];
    }
}
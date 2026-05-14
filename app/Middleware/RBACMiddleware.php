<?php

namespace App\Middleware;

use App\Core\Session;
use App\Core\Response;
use App\Core\Cache;

class RBACMiddleware
{
    /**
     * Expects a single permission slug passed via route:
     *   ->get('/finance', 'FinanceController@index', ['AuthMiddleware', 'RBACMiddleware:finance.view'])
     *
     * The Router passes params; we read the permission from the route definition.
     * Instead, we use a simpler approach: the middleware name includes the permission.
     */
    private string $requiredPermission;

    public function handle(array $params = []): bool
    {
        // The permission is encoded in the middleware name after ':'
        // e.g., "RBACMiddleware:finance.view"
        $user = Session::get('user');

        if (!$user) {
            Response::redirect('/login');
            return false;
        }

        // Super Admin has access to everything
        if ($user['role_slug'] === 'super_admin') {
            return true;
        }

        // If no specific permission required (just auth), pass
        if (empty($params) && $this->requiredPermission === null) {
            return true;
        }

        $permission = $this->requiredPermission ?? '';
        if ($permission === '') {
            return true;
        }

        // Load user permissions (cached)
        $permissions = $this->getUserPermissions($user['role_id']);

        if ($this->hasPermission($permissions, $permission)) {
            return true;
        }

        Response::error(403);
        return false;
    }

    /**
     * Called by the Router to pass the permission from the middleware string.
     * We override this by using a factory pattern instead.
     */
    public function setPermission(string $permission): self
    {
        $this->requiredPermission = $permission;
        return $this;
    }

    private function getUserPermissions(int $roleId): array
    {
        $cacheKey = "role_permissions_{$roleId}";
        $cached = Cache::get($cacheKey);

        if ($cached !== null) {
            return $cached;
        }

        $db = \App\Core\Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT p.slug
            FROM role_permissions rp
            INNER JOIN permissions p ON p.id = rp.permission_id
            WHERE rp.role_id = :role_id
        ");
        $stmt->execute([':role_id' => $roleId]);
        $slugs = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        Cache::set($cacheKey, $slugs, 1800); // 30 minutes
        return $slugs;
    }

    /**
     * Check if the user has a specific permission.
     * Supports wildcard: if user has "users.*", they pass "users.create"
     */
    private function hasPermission(array $permissions, string $required): bool
    {
        // Direct match
        if (in_array($required, $permissions, true)) {
            return true;
        }

        // Wildcard match: "module.*" covers "module.action"
        $parts = explode('.', $required, 2);
        if (count($parts) === 2) {
            $wildcard = "{$parts[0]}.*";
            if (in_array($wildcard, $permissions, true)) {
                return true;
            }
        }

        return false;
    }
}
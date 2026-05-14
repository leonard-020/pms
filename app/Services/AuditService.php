<?php

namespace App\Services;

use App\Models\AuditLog;

class AuditService
{
    private AuditLog $auditLog;

    public function __construct()
    {
        $this->auditLog = new AuditLog();
    }

    public function log(
        string $action,
        string $module,
        string $description,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        $userId = \App\Core\Session::get('user.id') ?? 0;
        $this->auditLog->log(
            $userId,
            $action,
            $module,
            $description,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null,
            $oldValues,
            $newValues
        );
    }
}
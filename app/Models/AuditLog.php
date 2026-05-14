<?php

namespace App\Models;

use App\Core\Model;

class AuditLog extends Model
{
    protected string $table = 'audit_logs';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'user_id', 'action', 'module', 'description', 'ip_address', 'user_agent', 'old_values', 'new_values',
    ];
    protected bool $softDeletes = false;

    /**
     * Audit logs are NEVER deleted. Override delete to prevent.
     */
    public function delete(int $id): bool
    {
        return false; // Immutable — no deletion allowed
    }

    public function softDelete(int $id): bool
    {
        return false;
    }

    public function log(
        int $userId,
        string $action,
        string $module,
        string $description,
        ?string $ip = null,
        ?string $userAgent = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): int {
        return $this->create([
            'user_id'     => $userId,
            'action'      => $action,
            'module'      => $module,
            'description' => $description,
            'ip_address'  => $ip,
            'user_agent'  => $userAgent,
            'old_values'  => $oldValues ? json_encode($oldValues) : null,
            'new_values'  => $newValues ? json_encode($newValues) : null,
        ]);
    }

    public function paginateWithUser(
        int $page = 1,
        int $perPage = 25,
        array $filters = [],
        string $search = ''
    ): array {
        $offset = ($page - 1) * $perPage;
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['action'])) {
            $where[] = "al.action = :action";
            $params[':action'] = $filters['action'];
        }
        if (!empty($filters['module'])) {
            $where[] = "al.module = :module";
            $params[':module'] = $filters['module'];
        }
        if (!empty($filters['date_from'])) {
            $where[] = "al.created_at >= :date_from";
            $params[':date_from'] = $filters['date_from'] . ' 00:00:00';
        }
        if (!empty($filters['date_to'])) {
            $where[] = "al.created_at <= :date_to";
            $params[':date_to'] = $filters['date_to'] . ' 23:59:59';
        }

        if ($search !== '') {
            $where[] = "(al.description LIKE :search OR u.email LIKE :search2)";
            $params[':search'] = "%{$search}%";
            $params[':search2'] = "%{$search}%";
        }

        $whereClause = 'WHERE ' . implode(' AND ', $where);

        $total = (int) $this->query(
            "SELECT COUNT(*) FROM audit_logs al LEFT JOIN users u ON u.id = al.user_id {$whereClause}",
            $params
        )->fetchColumn();

        $stmt = $this->db->prepare(
            "SELECT al.*, u.email as user_email
             FROM audit_logs al
             LEFT JOIN users u ON u.id = al.user_id
             {$whereClause}
             ORDER BY al.created_at DESC
             LIMIT :limit OFFSET :offset"
        );
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data'      => $stmt->fetchAll(),
            'total'     => $total,
            'page'      => $page,
            'per_page'  => $perPage,
            'last_page' => (int) ceil($total / $perPage) ?: 1,
        ];
    }
}
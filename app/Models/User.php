<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected string $table = 'users';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'email', 'password', 'role_id', 'status',
        'last_login_at', 'last_login_ip',
    ];
    protected bool $softDeletes = true;

    public function findWithRole(int $id): ?array
    {
        $sql = "SELECT u.*, r.name as role_name, r.slug as role_slug
                FROM users u
                INNER JOIN roles r ON r.id = u.role_id
                WHERE u.id = :id AND u.deleted_at IS NULL
                LIMIT 1";
        $stmt = $this->query($sql, [':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT u.*, r.name as role_name, r.slug as role_slug
                FROM users u
                INNER JOIN roles r ON r.id = u.role_id
                WHERE u.email = :email AND u.deleted_at IS NULL
                LIMIT 1";
        $stmt = $this->query($sql, [':email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function paginateWithRole(
        int $page = 1,
        int $perPage = 15,
        string $search = ''
    ): array {
        $offset = ($page - 1) * $perPage;
        $where = ["u.deleted_at IS NULL"];
        $params = [];

        if ($search !== '') {
            $where[] = "(u.email LIKE :search OR r.name LIKE :search2)";
            $params[':search'] = "%{$search}%";
            $params[':search2'] = "%{$search}%";
        }

        $whereClause = 'WHERE ' . implode(' AND ', $where);

        $countSql = "SELECT COUNT(*) FROM users u INNER JOIN roles r ON r.id = u.role_id {$whereClause}";
        $total = (int) $this->query($countSql, $params)->fetchColumn();

        $dataSql = "SELECT u.id, u.email, u.status, u.last_login_at, u.created_at,
                           r.name as role_name, r.slug as role_slug
                    FROM users u
                    INNER JOIN roles r ON r.id = u.role_id
                    {$whereClause}
                    ORDER BY u.id DESC
                    LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($dataSql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();

        return [
            'data'      => $items,
            'total'     => $total,
            'page'      => $page,
            'per_page'  => $perPage,
            'last_page' => (int) ceil($total / $perPage) ?: 1,
        ];
    }

    public function updateLogin(int $id, string $ip): void
    {
        $sql = "UPDATE users SET last_login_at = NOW(), last_login_ip = :ip WHERE id = :id";
        $this->query($sql, [':ip' => $ip, ':id' => $id]);
    }
}
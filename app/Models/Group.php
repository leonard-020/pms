<?php

namespace App\Models;

use App\Core\Model;

class Group extends Model
{
    protected string $table = 'groups';
    protected string $primaryKey = 'id';
    protected array $fillable = ['name', 'description', 'leader_id', 'status'];
    protected bool $softDeletes = false;

    public function paginateWithMemberCount(
        int $page = 1,
        int $perPage = 15,
        string $search = ''
    ): array {
        $offset = ($page - 1) * $perPage;
        $where = ["g.status = 'active'"];
        $params = [];

        if ($search !== '') {
            $where[] = "(g.name LIKE :search OR g.description LIKE :search2)";
            $params[':search'] = "%{$search}%";
            $params[':search2'] = "%{$search}%";
        }

        $whereClause = 'WHERE ' . implode(' AND ', $where);

        $total = (int) $this->query(
            "SELECT COUNT(*) FROM groups g {$whereClause}", $params
        )->fetchColumn();

        $stmt = $this->db->prepare(
            "SELECT g.*, COUNT(gm.member_id) as member_count,
                    CONCAT(m.first_name, ' ', m.last_name) as leader_name
             FROM groups g
             LEFT JOIN group_members gm ON gm.group_id = g.id
             LEFT JOIN members m ON m.id = g.leader_id
             {$whereClause}
             GROUP BY g.id
             ORDER BY g.name ASC
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
<?php

namespace App\Models;

use App\Core\Model;

class Sacrament extends Model
{
    protected string $table = 'sacraments';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'member_id', 'type', 'date', 'place', 'minister', 'witness_name', 'notes', 'created_by',
    ];
    protected bool $softDeletes = false;

    public function paginateWithMember(
        int $page = 1,
        int $perPage = 15,
        array $filters = [],
        string $search = ''
    ): array {
        $offset = ($page - 1) * $perPage;
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['type'])) {
            $where[] = "s.type = :type";
            $params[':type'] = $filters['type'];
        }

        if ($search !== '') {
            $where[] = "(m.first_name LIKE :search OR m.last_name LIKE :search2 OR m.member_number LIKE :search3)";
            $params[':search'] = "%{$search}%";
            $params[':search2'] = "%{$search}%";
            $params[':search3'] = "%{$search}%";
        }

        $whereClause = 'WHERE ' . implode(' AND ', $where);

        $countSql = "SELECT COUNT(*) FROM sacraments s
                     INNER JOIN members m ON m.id = s.member_id
                     {$whereClause}";
        $total = (int) $this->query($countSql, $params)->fetchColumn();

        $dataSql = "SELECT s.*, m.first_name, m.last_name, m.member_number
                    FROM sacraments s
                    INNER JOIN members m ON m.id = s.member_id
                    {$whereClause}
                    ORDER BY s.date DESC, s.id DESC
                    LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($dataSql);
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
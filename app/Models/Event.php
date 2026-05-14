<?php

namespace App\Models;

use App\Core\Model;

class Event extends Model
{
    protected string $table = 'events';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'title', 'description', 'event_date', 'start_time', 'end_time', 'location', 'status', 'created_by',
    ];
    protected bool $softDeletes = false;

    public function paginateWithCreator(
        int $page = 1,
        int $perPage = 15,
        string $search = ''
    ): array {
        $offset = ($page - 1) * $perPage;
        $where = ["1=1"];
        $params = [];

        if ($search !== '') {
            $where[] = "(e.title LIKE :search OR e.location LIKE :search2)";
            $params[':search'] = "%{$search}%";
            $params[':search2'] = "%{$search}%";
        }

        $whereClause = 'WHERE ' . implode(' AND ', $where);

        $total = (int) $this->query(
            "SELECT COUNT(*) FROM events e {$whereClause}", $params
        )->fetchColumn();

        $stmt = $this->db->prepare(
            "SELECT e.*, u.email as creator_email
             FROM events e
             LEFT JOIN users u ON u.id = e.created_by
             {$whereClause}
             ORDER BY e.event_date DESC, e.id DESC
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

    public function getUpcoming(int $limit = 5): array
    {
        return $this->query(
            "SELECT * FROM events WHERE status = 'upcoming' AND event_date >= CURDATE()
             ORDER BY event_date ASC, start_time ASC LIMIT :limit",
            [':limit' => $limit]
        )->fetchAll();
    }
}
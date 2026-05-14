<?php

namespace App\Models;

use App\Core\Model;

class FinanceTransaction extends Model
{
    protected string $table = 'finance_transactions';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'type', 'category', 'amount', 'description', 'transaction_date',
        'status', 'recorded_by', 'approved_by', 'approved_at',
        'rejection_note', 'notes',
    ];
    protected bool $softDeletes = false;

    public function paginateWithDetails(
        int $page = 1,
        int $perPage = 15,
        array $filters = [],
        string $search = ''
    ): array {
        $offset = ($page - 1) * $perPage;
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['type'])) {
            $where[] = "ft.type = :type";
            $params[':type'] = $filters['type'];
        }
        if (!empty($filters['status'])) {
            $where[] = "ft.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['category'])) {
            $where[] = "ft.category = :category";
            $params[':category'] = $filters['category'];
        }
        if (!empty($filters['date_from'])) {
            $where[] = "ft.transaction_date >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[] = "ft.transaction_date <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        if ($search !== '') {
            $where[] = "(ft.description LIKE :search OR ft.category LIKE :search2)";
            $params[':search'] = "%{$search}%";
            $params[':search2'] = "%{$search}%";
        }

        $whereClause = 'WHERE ' . implode(' AND ', $where);

        $countSql = "SELECT COUNT(*) FROM finance_transactions ft {$whereClause}";
        $total = (int) $this->query($countSql, $params)->fetchColumn();

        $dataSql = "SELECT ft.*,
                           recorder.email as recorder_email,
                           approver.email as approver_email
                    FROM finance_transactions ft
                    LEFT JOIN users recorder ON recorder.id = ft.recorded_by
                    LEFT JOIN users approver ON approver.id = ft.approved_by
                    {$whereClause}
                    ORDER BY ft.transaction_date DESC, ft.id DESC
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

    public function getSummary(array $filters = []): array
    {
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['date_from'])) {
            $where[] = "transaction_date >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[] = "transaction_date <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        $whereClause = 'WHERE ' . implode(' AND ', $where);

        $income = (float) $this->query(
            "SELECT COALESCE(SUM(amount), 0) FROM finance_transactions {$whereClause} AND type = 'income' AND status = 'approved'",
            $params
        )->fetchColumn();

        $expense = (float) $this->query(
            "SELECT COALESCE(SUM(amount), 0) FROM finance_transactions {$whereClause} AND type = 'expense' AND status = 'approved'",
            $params
        )->fetchColumn();

        $pending = (int) $this->query(
            "SELECT COUNT(*) FROM finance_transactions {$whereClause} AND status = 'pending'",
            $params
        )->fetchColumn();

        return [
            'total_income'  => $income,
            'total_expense' => $expense,
            'net'           => $income - $expense,
            'pending_count' => $pending,
        ];
    }

    public function getCategoryBreakdown(string $type, array $filters = []): array
    {
        $where = ["type = :type", "status = 'approved'"];
        $params = [':type' => $type];

        if (!empty($filters['date_from'])) {
            $where[] = "transaction_date >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[] = "transaction_date <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        $whereClause = 'WHERE ' . implode(' AND ', $where);

        $sql = "SELECT category, COUNT(*) as count, SUM(amount) as total
                FROM finance_transactions {$whereClause}
                GROUP BY category
                ORDER BY total DESC";

        return $this->query($sql, $params)->fetchAll();
    }
}
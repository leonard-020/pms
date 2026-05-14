<?php

namespace App\Core;

use PDO;
use PDOStatement;

abstract class Model
{
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected bool $softDeletes = false;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // -------------------------------------------------------
    // Find operations
    // -------------------------------------------------------

    public function find(int $id, bool $withTrashed = false): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";

        if ($this->softDeletes && !$withTrashed) {
            $sql .= " AND deleted_at IS NULL";
        }

        $sql .= " LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function findBy(string $column, mixed $value, bool $withTrashed = false): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = :val";

        if ($this->softDeletes && !$withTrashed) {
            $sql .= " AND deleted_at IS NULL";
        }

        $sql .= " LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':val' => $value]);
        return $stmt->fetch() ?: null;
    }

    public function findAll(bool $withTrashed = false): array
    {
        $sql = "SELECT * FROM {$this->table}";

        if ($this->softDeletes && !$withTrashed) {
            $sql .= " WHERE deleted_at IS NULL";
        }

        return $this->db->query($sql)->fetchAll();
    }

    // -------------------------------------------------------
    // Paginated query with optional filters
    // -------------------------------------------------------

    public function paginate(
        int $page = 1,
        int $perPage = 15,
        array $filters = [],
        string $search = '',
        string $searchColumns = '',
        string $orderBy = 'id',
        string $orderDir = 'DESC'
    ): array {
        $offset = ($page - 1) * $perPage;
        $where = [];
        $params = [];

        // Soft delete filter
        if ($this->softDeletes) {
            $where[] = "deleted_at IS NULL";
        }

        // Custom filters: ['status' => 'active', 'type' => 'income']
        foreach ($filters as $col => $val) {
            if ($val !== '' && $val !== null) {
                $where[] = "`{$col}` = :filter_{$col}";
                $params[":filter_{$col}"] = $val;
            }
        }

        // Search across columns
        if ($search !== '' && $searchColumns !== '') {
            $searchConditions = [];
            $cols = explode(',', $searchColumns);
            foreach ($cols as $col) {
                $col = trim($col);
                $searchConditions[] = "`{$col}` LIKE :search";
            }
            $where[] = '(' . implode(' OR ', $searchConditions) . ')';
            $params[':search'] = "%{$search}%";
        }

        $whereClause = empty($where) ? '' : 'WHERE ' . implode(' AND ', $where);

        // Whitelist order direction
        $orderDir = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';

        // Count total
        $countSql = "SELECT COUNT(*) FROM {$this->table} {$whereClause}";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        // Fetch page
        $dataSql = "SELECT * FROM {$this->table} {$whereClause}
                    ORDER BY `{$orderBy}` {$orderDir}
                    LIMIT :limit OFFSET :offset";
        $dataStmt = $this->db->prepare($dataSql);
        foreach ($params as $key => $val) {
            $dataStmt->bindValue($key, $val);
        }
        $dataStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $dataStmt->execute();
        $items = $dataStmt->fetchAll();

        return [
            'data'  => $items,
            'total' => $total,
            'page'  => $page,
            'per_page' => $perPage,
            'last_page' => (int) ceil($total / $perPage) ?: 1,
            'from'  => $total > 0 ? $offset + 1 : 0,
            'to'    => min($offset + $perPage, $total),
        ];
    }

    // -------------------------------------------------------
    // Create / Update / Delete
    // -------------------------------------------------------

    public function create(array $data): int
    {
        $filtered = $this->onlyFillable($data);
        $columns = implode(', ', array_map(fn($col) => "`{$col}`", array_keys($filtered)));
        $placeholders = implode(', ', array_map(fn($col) => ":{$col}", array_keys($filtered)));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);

        foreach ($filtered as $col => $val) {
            $stmt->bindValue(":{$col}", $val);
        }

        $stmt->execute();
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $filtered = $this->onlyFillable($data);
        if (empty($filtered)) {
            return false;
        }

        $set = implode(', ', array_map(fn($col) => "`{$col}` = :{$col}", array_keys($filtered)));

        $sql = "UPDATE {$this->table} SET {$set} WHERE {$this->primaryKey} = :id";

        if ($this->softDeletes) {
            $sql .= " AND deleted_at IS NULL";
        }

        $stmt = $this->db->prepare($sql);

        foreach ($filtered as $col => $val) {
            $stmt->bindValue(":{$col}", $val);
        }
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }

    public function softDelete(int $id): bool
    {
        if (!$this->softDeletes) {
            return false;
        }

        $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE {$this->primaryKey} = :id AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }

    public function restore(int $id): bool
    {
        if (!$this->softDeletes) {
            return false;
        }

        $sql = "UPDATE {$this->table} SET deleted_at = NULL WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }

    public function count(array $filters = []): int
    {
        $where = [];
        $params = [];

        if ($this->softDeletes) {
            $where[] = "deleted_at IS NULL";
        }

        foreach ($filters as $col => $val) {
            $where[] = "`{$col}` = :{$col}";
            $params[":{$col}"] = $val;
        }

        $whereClause = empty($where) ? '' : 'WHERE ' . implode(' AND ', $where);
        $sql = "SELECT COUNT(*) FROM {$this->table} {$whereClause}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function sum(string $column, array $filters = []): float
    {
        $where = [];
        $params = [];

        if ($this->softDeletes) {
            $where[] = "deleted_at IS NULL";
        }

        foreach ($filters as $col => $val) {
            $where[] = "`{$col}` = :{$col}";
            $params[":{$col}"] = $val;
        }

        $whereClause = empty($where) ? '' : 'WHERE ' . implode(' AND ', $where);
        $sql = "SELECT COALESCE(SUM(`{$column}`), 0) FROM {$this->table} {$whereClause}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (float) $stmt->fetchColumn();
    }

    // -------------------------------------------------------
    // Raw query (for complex joins)
    // -------------------------------------------------------

    protected function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    private function onlyFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }
        return array_intersect_key($data, array_flip($this->fillable));
    }

    public function beginTransaction(): void
    {
        Database::getInstance()->beginTransaction();
    }

    public function commit(): void
    {
        Database::getInstance()->commit();
    }

    public function rollBack(): void
    {
        Database::getInstance()->rollBack();
    }
}
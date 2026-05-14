<?php

namespace App\Models;

use App\Core\Model;

class Member extends Model
{
    protected string $table = 'members';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'user_id', 'member_number', 'first_name', 'last_name', 'middle_name',
        'date_of_birth', 'gender', 'phone', 'address', 'city', 'state',
        'zip_code', 'country', 'photo', 'occupation', 'status', 'created_by',
    ];
    protected bool $softDeletes = true;

    public function generateMemberNumber(): string
    {
        $prefix = 'PMS';
        $year = date('Y');

        $sql = "SELECT COUNT(*) + 1 FROM members WHERE YEAR(created_at) = :year";
        $count = (int) $this->query($sql, [':year' => $year])->fetchColumn();

        return sprintf('%s-%s-%05d', $prefix, $year, $count);
    }

    public function findByUserId(int $userId): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :uid AND deleted_at IS NULL LIMIT 1";
        $stmt = $this->query($sql, [':uid' => $userId]);
        return $stmt->fetch() ?: null;
    }

    public function getSacraments(int $memberId): array
    {
        $sql = "SELECT * FROM sacraments WHERE member_id = :mid ORDER BY date DESC";
        return $this->query($sql, [':mid' => $memberId])->fetchAll();
    }

    public function getGroups(int $memberId): array
    {
        $sql = "SELECT g.*, gm.role as member_role, gm.joined_at
                FROM groups g
                INNER JOIN group_members gm ON gm.group_id = g.id
                WHERE gm.member_id = :mid AND g.status = 'active'
                ORDER BY g.name";
        return $this->query($sql, [':mid' => $memberId])->fetchAll();
    }
}
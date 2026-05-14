<?php

namespace App\Services;

use App\Core\Validator;
use App\Core\CSRF;
use App\Models\Member;
use App\Models\User;

class MemberService
{
    private Member $memberModel;
    private AuditService $audit;

    public function __construct()
    {
        $this->memberModel = new Member();
        $this->audit = new AuditService();
    }

    public function create(array $data): array
    {
        if (!CSRF::check()) {
            return ['success' => false, 'errors' => ['_token' => ['Invalid security token.']]];
        }

        $validator = new Validator($data, [
            'first_name' => 'required|alpha|min:2|max:100',
            'last_name'  => 'required|alpha|min:2|max:100',
            'middle_name'=> 'nullable|alpha|max:100',
            'date_of_birth' => 'nullable|date',
            'gender'     => 'nullable|in:male,female',
            'phone'      => 'nullable|phone',
            'address'    => 'nullable|max:255',
            'city'       => 'nullable|max:100',
            'state'      => 'nullable|max:100',
            'zip_code'   => 'nullable|max:20',
            'occupation' => 'nullable|max:150',
        ]);

        if (!$validator->validate()) {
            return ['success' => false, 'errors' => $validator->errors()];
        }

        $userId = \App\Core\Session::get('user.id');
        $data['member_number'] = $this->memberModel->generateMemberNumber();
        $data['created_by'] = $userId;
        $data['status'] = 'active';

        $id = $this->memberModel->create($data);

        $this->audit->log('create', 'members', "Created member {$data['member_number']} - {$data['first_name']} {$data['last_name']}");

        return ['success' => true, 'id' => $id, 'member_number' => $data['member_number']];
    }

    public function update(int $id, array $data): array
    {
        if (!CSRF::check()) {
            return ['success' => false, 'errors' => ['_token' => ['Invalid security token.']]];
        }

        $validator = new Validator($data, [
            'first_name' => 'required|alpha|min:2|max:100',
            'last_name'  => 'required|alpha|min:2|max:100',
            'middle_name'=> 'nullable|alpha|max:100',
            'date_of_birth' => 'nullable|date',
            'gender'     => 'nullable|in:male,female',
            'phone'      => 'nullable|phone',
            'address'    => 'nullable|max:255',
            'city'       => 'nullable|max:100',
            'state'      => 'nullable|max:100',
            'zip_code'   => 'nullable|max:20',
            'occupation' => 'nullable|max:150',
        ]);

        if (!$validator->validate()) {
            return ['success' => false, 'errors' => $validator->errors()];
        }

        $old = $this->memberModel->find($id);
        if (!$old) {
            return ['success' => false, 'errors' => ['id' => ['Member not found.']]];
        }

        $this->memberModel->update($id, $data);

        $this->audit->log('update', 'members', "Updated member {$old['member_number']}", $old, $data);

        return ['success' => true];
    }
}
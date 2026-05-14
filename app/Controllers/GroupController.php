<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\CSRF;
use App\Core\Validator;
use App\Models\Group;
use App\Services\AuditService;

class GroupController extends Controller
{
    public function index(): void
    {
        $page   = (int) ($this->request->query('page', 1) ?: 1);
        $search = $this->request->query('search', '');

        $model  = new Group();
        $groups = $model->paginateWithMemberCount($page, 15, $search);

        $this->layout('main', 'groups/index', [
            'title'  => 'Ministries & Groups',
            'groups' => $groups,
            'search' => htmlspecialchars($search, ENT_QUOTES, 'UTF-8'),
            'success'=> Session::flash('success'),
        ]);
    }

    public function create(): void
    {
        $db      = \App\Core\Database::getInstance()->getConnection();
        $members = $db->query(
            "SELECT id, member_number, first_name, last_name
             FROM members WHERE deleted_at IS NULL AND status = 'active'
             ORDER BY last_name, first_name"
        )->fetchAll();

        $this->layout('main', 'groups/create', [
            'title'   => 'Create Group',
            '_token'  => CSRF::field(),
            'members' => $members,
            'old'     => Session::flash('old') ?: [],
            'errors'  => Session::flash('errors') ?: [],
        ]);
    }

    public function store(): void
    {
        if (!CSRF::check()) {
            Session::flash('error', 'Invalid security token.');
            $this->back();
            return;
        }

        $validator = new Validator($this->request->all(), [
            'name'        => 'required|max:150',
            'description' => 'nullable|max:2000',
            'leader_id'   => 'nullable|numeric',
        ]);

        if (!$validator->validate()) {
            Session::flash('errors', $validator->errors());
            Session::flash('old', $this->request->all());
            $this->redirect('/groups/create');
            return;
        }

        $data = $this->request->all();
        $data['status'] = 'active';

        $model = new Group();
        $id    = $model->create($data);

        (new AuditService())->log('create', 'groups', "Created group: {$data['name']}");

        Session::flash('success', 'Group created successfully.');
        $this->redirect('/groups');
    }

    public function show(int $id): void
    {
        $model = new Group();
        $group = $model->find($id);

        if (!$group) {
            Session::flash('error', 'Group not found.');
            $this->redirect('/groups');
            return;
        }

        $db = \App\Core\Database::getInstance()->getConnection();
        $members = $db->prepare(
            "SELECT m.*, gm.role as member_role, gm.joined_at
             FROM group_members gm
             INNER JOIN members m ON m.id = gm.member_id
             WHERE gm.group_id = :gid AND m.deleted_at IS NULL
             ORDER BY m.last_name, m.first_name"
        );
        $members->execute([':gid' => $id]);

        $this->layout('main', 'groups/show', [
            'title'   => $group['name'],
            'group'   => $group,
            'members' => $members->fetchAll(),
        ]);
    }
}
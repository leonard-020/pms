<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\CSRF;
use App\Models\Member;
use App\Services\MemberService;

class MemberController extends Controller
{
    private MemberService $memberService;

    public function __construct()
    {
        parent::__construct();
        $this->memberService = new MemberService();
    }

    public function index(): void
    {
        $page   = (int) ($this->request->query('page', 1) ?: 1);
        $search = $this->request->query('search', '');
        $status = $this->request->query('status', '');

        $model = new Member();
        $filters = [];
        if ($status !== '') {
            $filters['status'] = $status;
        }

        $result = $model->paginate($page, 15, $filters, $search, 'last_name,first_name');

        $this->layout('main', 'members/index', [
            'title'    => 'Parish Members',
            'members'  => $result,
            'search'   => htmlspecialchars($search, ENT_QUOTES, 'UTF-8'),
            'status'   => htmlspecialchars($status, ENT_QUOTES, 'UTF-8'),
            'success'  => Session::flash('success'),
            'error'    => Session::flash('error'),
        ]);
    }

    public function create(): void
    {
        $this->layout('main', 'members/create', [
            'title'    => 'Register New Member',
            '_token'   => CSRF::field(),
            'old'      => Session::flash('old') ?: [],
            'errors'   => Session::flash('errors') ?: [],
        ]);
    }

    public function store(): void
    {
        $result = $this->memberService->create($this->request->all());

        if (!$result['success']) {
            Session::flash('errors', $result['errors']);
            Session::flash('old', $this->request->all());
            $this->redirect('/members/create');
        }

        Session::flash('success', "Member {$result['member_number']} registered successfully.");
        $this->redirect('/members');
    }

    public function show(int $id): void
    {
        $model = new Member();
        $member = $model->find($id);

        if (!$member) {
            Session::flash('error', 'Member not found.');
            $this->redirect('/members');
        }

        $sacraments = $model->getSacraments($id);
        $groups = $model->getGroups($id);

        $this->layout('main', 'members/show', [
            'title'     => $member['first_name'] . ' ' . $member['last_name'],
            'member'    => $member,
            'sacraments'=> $sacraments,
            'groups'    => $groups,
        ]);
    }

    public function edit(int $id): void
    {
        $model = new Member();
        $member = $model->find($id);

        if (!$member) {
            Session::flash('error', 'Member not found.');
            $this->redirect('/members');
        }

        $this->layout('main', 'members/edit', [
            'title'  => 'Edit Member',
            'member' => $member,
            '_token' => CSRF::field(),
            'old'    => Session::flash('old') ?: $member,
            'errors' => Session::flash('errors') ?: [],
        ]);
    }

    public function update(int $id): void
    {
        $result = $this->memberService->update($id, $this->request->all());

        if (!$result['success']) {
            Session::flash('errors', $result['errors']);
            Session::flash('old', $this->request->all());
            $this->redirect("/members/{$id}/edit");
        }

        Session::flash('success', 'Member updated successfully.');
        $this->redirect("/members/{$id}");
    }
}
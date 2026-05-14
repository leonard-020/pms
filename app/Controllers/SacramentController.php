<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\CSRF;
use App\Core\Validator;
use App\Models\Sacrament;
use App\Services\AuditService;

class SacramentController extends Controller
{
    public function index(): void
    {
        $page   = (int) ($this->request->query('page', 1) ?: 1);
        $search = $this->request->query('search', '');
        $type   = $this->request->query('type', '');

        $model   = new Sacrament();
        $filters = [];
        if ($type !== '') {
            $filters['type'] = $type;
        }

        $sacraments = $model->paginateWithMember($page, 15, $filters, $search);

        $this->layout('main', 'sacraments/index', [
            'title'      => 'Sacraments',
            'sacraments' => $sacraments,
            'search'     => htmlspecialchars($search, ENT_QUOTES, 'UTF-8'),
            'type'       => htmlspecialchars($type, ENT_QUOTES, 'UTF-8'),
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

        $this->layout('main', 'sacraments/create', [
            'title'   => 'Record Sacrament',
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
            'member_id'   => 'required|numeric',
            'type'        => 'required|in:baptism,first_communion,confirmation,marriage,holy_orders,anointing_sick',
            'date'        => 'required|date',
            'place'       => 'nullable|max:255',
            'minister'    => 'nullable|max:150',
            'witness_name'=> 'nullable|max:150',
            'notes'       => 'nullable|max:2000',
        ]);

        if (!$validator->validate()) {
            Session::flash('errors', $validator->errors());
            Session::flash('old', $this->request->all());
            $this->redirect('/sacraments/create');
            return;
        }

        $data = $this->request->all();
        $data['created_by'] = Session::get('user.id');

        $model = new Sacrament();
        $model->create($data);

        (new AuditService())->log('create', 'sacraments',
            "Recorded {$data['type']} for member ID {$data['member_id']}");

        Session::flash('success', 'Sacrament recorded successfully.');
        $this->redirect('/sacraments');
    }
}
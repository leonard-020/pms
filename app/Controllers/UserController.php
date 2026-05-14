<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\CSRF;
use App\Models\User;
use App\Models\Role;
use App\Services\UserService;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct()
    {
        parent::__construct();
        $this->userService = new UserService();
    }

    public function index(): void
    {
        $page = (int) ($this->request->query('page', 1) ?: 1);
        $search = $this->request->query('search', '');

        $model = new User();
        $users = $model->paginateWithRole($page, 15, $search);

        $this->layout('main', 'users/index', [
            'title'  => 'User Management',
            'users'  => $users,
            'search' => htmlspecialchars($search, ENT_QUOTES, 'UTF-8'),
            'success'=> Session::flash('success'),
            'error'  => Session::flash('error'),
        ]);
    }

    public function create(): void
    {
        $db = \App\Core\Database::getInstance()->getConnection();
        $roles = $db->query("SELECT * FROM roles ORDER BY name")->fetchAll();

        $this->layout('main', 'users/create', [
            'title'  => 'Create User',
            '_token' => CSRF::field(),
            'roles'  => $roles,
            'old'    => Session::flash('old') ?: [],
            'errors' => Session::flash('errors') ?: [],
        ]);
    }

    public function store(): void
    {
        $result = $this->userService->create($this->request->all());

        if (!$result['success']) {
            Session::flash('errors', $result['errors']);
            Session::flash('old', $this->request->all());
            $this->redirect('/users/create');
        }

        Session::flash('success', 'User created successfully.');
        $this->redirect('/users');
    }

    public function activate(int $id): void
    {
        $result = $this->userService->activate($id);
        Session::flash($result['success'] ? 'success' : 'error', $result['message']);
        $this->back();
    }

    public function deactivate(int $id): void
    {
        $result = $this->userService->deactivate($id);
        Session::flash($result['success'] ? 'success' : 'error', $result['message']);
        $this->back();
    }
}
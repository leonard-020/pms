<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\CSRF;
use App\Services\AuthService;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        parent::__construct();
        $this->authService = new AuthService();
    }

    public function showLogin(): void
    {
        $this->layout('auth', 'auth/login', [
            'title'  => 'Sign In',
            '_token' => CSRF::field(),
        ]);
    }

    public function login(): void
    {
        $result = $this->authService->login($this->request->all());

        if (!$result['success']) {
            Session::flash('errors', $result['errors']);
            Session::flash('old', $this->request->only(['email']));
            $this->redirect('/login');
        }

        Session::flash('success', 'Welcome back, ' . $result['user']['role_name'] . '!');
        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        $this->authService->logout();
        $this->redirect('/login');
    }
}
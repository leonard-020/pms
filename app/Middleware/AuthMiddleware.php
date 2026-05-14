<?php

namespace App\Middleware;

use App\Core\Session;
use App\Core\Response;

class AuthMiddleware
{
    public function handle(array $params = []): bool
    {
        if (!Session::has('user')) {
            Session::flash('error', 'You must be logged in to access this page.');
            Response::redirect('/login');
            return false;
        }

        // Check user status
        $user = Session::get('user');
        if ($user['status'] !== 'active') {
            Session::destroy();
            Session::flash('error', 'Your account has been deactivated. Contact the administrator.');
            Response::redirect('/login');
            return false;
        }

        return true;
    }
}
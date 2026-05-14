<?php

namespace App\Middleware;

use App\Core\Session;
use App\Core\Response;

class GuestMiddleware
{
    public function handle(array $params = []): bool
    {
        if (Session::has('user')) {
            Response::redirect('/dashboard');
            return false;
        }
        return true;
    }
}
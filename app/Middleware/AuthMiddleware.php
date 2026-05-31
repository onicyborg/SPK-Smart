<?php

declare(strict_types=1);

namespace App\Middleware;

use Core\Session;
use Core\Response;

class AuthMiddleware
{
    public function handle(): void
    {
        if (!Session::get('user_id')) {
            Response::redirect('/login');
        }
    }
}

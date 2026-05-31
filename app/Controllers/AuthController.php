<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Request;
use Core\Response;
use Core\Session;
use Core\Model;

class AuthController
{
    public function loginView(): void
    {
        require_once __DIR__ . '/../../views/auth/login.php';
    }

    public function authenticate(): void
    {
        $request = new Request();

        $login = trim($request->post('username', ''));
        $password = $request->post('password', '');

        if ($login === '' || $password === '') {
            Session::setFlash('error', 'Email/username dan password wajib diisi.');
            Response::redirect('/login');
        }

        $db = new Model();

        if (str_contains($login, '@')) {
            $user = $db->table('users')
                ->select('*')
                ->where('email', '=', $login)
                ->first();
        } else {
            $user = $db->table('users')
                ->select('*')
                ->where('username', '=', $login)
                ->first();
        }

        if (!$user || !password_verify($password, $user['password'])) {
            Session::setFlash('error', 'Email/username atau password salah.');
            Session::setFlash('old', ['username' => $login]);
            Response::redirect('/login');
        }

        Session::set('user_id', $user['id']);
        Session::set('username', $user['username']);
        Session::set('full_name', $user['full_name'] ?? null);
        Session::set('role', $user['role']);

        Response::redirect('/dashboard');
    }

    public function logout(): void
    {
        Session::destroy();
        Response::redirect('/login');
    }
}

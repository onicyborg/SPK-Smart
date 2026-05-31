<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Model;
use Core\Request;
use Core\Response;
use Core\Session;
use Core\Uuid;

class UserController
{
    public function index(): void
    {
        $db = new Model();
        $users = $db->table('users')
            ->select('*')
            ->orderBy('created_at', 'DESC')
            ->get();

        require_once __DIR__ . '/../../views/users/index.php';
    }

    public function store(): void
    {
        $this->requireAdmin();

        $request = new Request();
        $data = $this->validateUserInput($request);
        if ($data === null) {
            return;
        }

        $db = new Model();

        $existing = $db->table('users')
            ->where('username', '=', $data['username'])
            ->first();
        if ($existing) {
            Session::setFlash('error', 'Username sudah digunakan.');
            Session::setFlash('old', $request->all());
            Response::redirect('/users');
        }

        $existingEmail = $db->table('users')
            ->where('email', '=', $data['email'])
            ->first();
        if ($existingEmail) {
            Session::setFlash('error', 'Email sudah digunakan.');
            Session::setFlash('old', $request->all());
            Response::redirect('/users');
        }

        $data['id'] = Uuid::generate();
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $db->table('users')->insert($data);

        Session::setFlash('success', 'Pengguna berhasil ditambahkan.');
        Response::redirect('/users');
    }

    public function update(): void
    {
        $this->requireAdmin();

        $request = new Request();
        $id = trim($request->post('id', ''));

        if ($id === '' || !Uuid::isValid($id)) {
            Session::setFlash('error', 'ID pengguna tidak valid.');
            Response::redirect('/users');
        }

        $data = $this->validateUserInput($request, true);
        if ($data === null) {
            return;
        }

        $db = new Model();

        $existing = $db->table('users')
            ->where('username', '=', $data['username'])
            ->first();
        if ($existing && $existing['id'] !== $id) {
            Session::setFlash('error', 'Username sudah digunakan oleh pengguna lain.');
            Session::setFlash('old', $request->all());
            Response::redirect('/users');
        }

        $existingEmail = $db->table('users')
            ->where('email', '=', $data['email'])
            ->first();
        if ($existingEmail && $existingEmail['id'] !== $id) {
            Session::setFlash('error', 'Email sudah digunakan oleh pengguna lain.');
            Session::setFlash('old', $request->all());
            Response::redirect('/users');
        }

        $user = $db->table('users')->where('id', '=', $id)->first();
        if (!$user) {
            Session::setFlash('error', 'Pengguna tidak ditemukan.');
            Response::redirect('/users');
        }

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        $db->table('users')->where('id', '=', $id)->update($data);

        Session::setFlash('success', 'Pengguna berhasil diperbarui.');
        Response::redirect('/users');
    }

    public function destroy(): void
    {
        $this->requireAdmin();

        $request = new Request();
        $id = trim($request->post('id', ''));

        if ($id === '' || !Uuid::isValid($id)) {
            Session::setFlash('error', 'ID pengguna tidak valid.');
            Response::redirect('/users');
        }

        if ($id === Session::get('user_id')) {
            Session::setFlash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            Response::redirect('/users');
        }

        $db = new Model();
        $user = $db->table('users')->where('id', '=', $id)->first();
        if (!$user) {
            Session::setFlash('error', 'Pengguna tidak ditemukan.');
            Response::redirect('/users');
        }

        $db->table('users')->where('id', '=', $id)->delete();

        Session::setFlash('success', 'Pengguna berhasil dihapus.');
        Response::redirect('/users');
    }

    private function requireAdmin(): void
    {
        $role = Session::get('role');
        if ($role !== 'admin') {
            Session::setFlash('error', 'Akses ditolak. Hanya administrator yang dapat mengelola pengguna.');
            Response::redirect('/dashboard');
        }
    }

    private function validateUserInput(Request $request, bool $isUpdate = false): ?array
    {
        $username = trim($request->post('username', ''));
        $email    = trim($request->post('email', ''));
        $password = $request->post('password', '');
        $role     = trim($request->post('role', ''));
        $fullName = trim($request->post('full_name', ''));

        $errors = [];
        if ($username === '') {
            $errors[] = 'Username wajib diisi.';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email wajib diisi dengan format yang valid.';
        }
        if (!$isUpdate && $password === '') {
            $errors[] = 'Password wajib diisi.';
        }
        if ($role === '') {
            $errors[] = 'Role wajib dipilih.';
        } elseif (!in_array($role, ['admin', 'staff', 'pimpinan'], true)) {
            $errors[] = 'Role tidak valid.';
        }

        if (!empty($errors)) {
            Session::setFlash('error', implode(' ', $errors));
            Session::setFlash('old', $request->all());
            Response::redirect('/users');
        }

        $data = [
            'username'  => $username,
            'email'     => $email,
            'role'      => $role,
            'full_name' => $fullName ?: null,
        ];

        if ($password !== '') {
            $data['password'] = $password;
        }

        return $data;
    }
}


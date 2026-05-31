<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Model;
use Core\Request;
use Core\Response;
use Core\Session;

class ProfileController
{
    public function index(): void
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            Response::redirect('/login');
        }

        $db = new Model();
        $user = $db->table('users')->where('id', '=', $userId)->first();

        if (!$user) {
            Session::destroy();
            Session::setFlash('error', 'Data pengguna tidak ditemukan, silakan login ulang.');
            Response::redirect('/login');
        }

        require_once __DIR__ . '/../../views/auth/profile.php';
    }

    public function update(): void
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            Response::redirect('/login');
        }

        $db = new Model();
        $user = $db->table('users')->where('id', '=', $userId)->first();
        if (!$user) {
            Session::destroy();
            Session::setFlash('error', 'Data pengguna tidak ditemukan, silakan login ulang.');
            Response::redirect('/login');
        }

        $request = new Request();
        $fullName = trim($request->post('full_name', ''));
        $username = trim($request->post('username', ''));
        $email    = trim($request->post('email', ''));
        $password = $request->post('password', '');
        $passwordConfirmation = $request->post('password_confirmation', '');

        $errors = [];
        if ($username === '') {
            $errors[] = 'Username wajib diisi.';
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid.';
        }

        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            Session::setFlash('old', $request->all());
            Response::redirect('/profile');
        }

        $updateData = [
            'full_name' => $fullName ?: null,
            'username'  => $username,
            'email'     => $email,
        ];

        if ($password !== '') {
            if ($password !== $passwordConfirmation) {
                Session::setFlash('errors', ['Password baru dan konfirmasi password tidak cocok.']);
                Session::setFlash('old', $request->all());
                Response::redirect('/profile');
            }
            $updateData['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $existing = $db->table('users')
            ->where('username', '=', $username)
            ->first();
        if ($existing && $existing['id'] !== $userId) {
            Session::setFlash('errors', ['Username sudah digunakan oleh pengguna lain.']);
            Session::setFlash('old', $request->all());
            Response::redirect('/profile');
        }

        $existingEmail = $db->table('users')
            ->where('email', '=', $email)
            ->first();
        if ($existingEmail && $existingEmail['id'] !== $userId) {
            Session::setFlash('errors', ['Email sudah digunakan oleh pengguna lain.']);
            Session::setFlash('old', $request->all());
            Response::redirect('/profile');
        }

        $avatarPath = $user['avatar'] ?? null;
        $uploadDir = __DIR__ . '/../../public/uploads/avatars';

        $avatarRemove = $request->post('avatar_remove', '');
        if ($avatarRemove === '1') {
            if ($avatarPath && file_exists($uploadDir . '/' . $avatarPath)) {
                unlink($uploadDir . '/' . $avatarPath);
            }
            $updateData['avatar'] = null;
            $avatarPath = null;
        }

        if (!empty($_FILES['avatar']['name'])) {
            $file = $_FILES['avatar'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                Session::setFlash('errors', ['Terjadi kesalahan saat mengunggah avatar.']);
                Session::setFlash('old', $request->all());
                Response::redirect('/profile');
            }

            $maxSize = 2 * 1024 * 1024;
            if ($file['size'] > $maxSize) {
                Session::setFlash('errors', ['Ukuran file avatar maksimal 2 MB.']);
                Session::setFlash('old', $request->all());
                Response::redirect('/profile');
            }

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            if (!in_array($ext, $allowed, true)) {
                Session::setFlash('errors', ['Format avatar harus JPG, JPEG, PNG, atau WEBP.']);
                Session::setFlash('old', $request->all());
                Response::redirect('/profile');
            }

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $filename = uniqid() . '_' . basename($file['name']);
            $target = $uploadDir . '/' . $filename;

            if (move_uploaded_file($file['tmp_name'], $target)) {
                if ($avatarPath && file_exists($uploadDir . '/' . $avatarPath)) {
                    unlink($uploadDir . '/' . $avatarPath);
                }
                $updateData['avatar'] = $filename;
            } else {
                Session::setFlash('errors', ['Gagal menyimpan file avatar.']);
                Session::setFlash('old', $request->all());
                Response::redirect('/profile');
            }
        }

        $db->table('users')->where('id', '=', $userId)->update($updateData);

        Session::set('username', $username);
        Session::setFlash('success', 'Profil berhasil diperbarui.');
        Response::redirect('/profile');
    }
}

<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Model;
use Core\Request;
use Core\Response;
use Core\Session;
use Core\Uuid;

class CriteriaController
{
    public function index(): void
    {
        $db = new Model();
        $criteria = $db->table('criteria')
            ->select('*')
            ->orderBy('criteria_code', 'ASC')
            ->get();

        require_once __DIR__ . '/../../views/criteria/index.php';
    }

    public function store(): void
    {
        $request = new Request();
        $data = $this->validateCriteriaInput($request);
        if ($data === null) {
            return;
        }

        $db = new Model();
        $existing = $db->table('criteria')
            ->where('criteria_code', '=', $data['criteria_code'])
            ->first();

        if ($existing) {
            Session::setFlash('error', 'Kode kriteria sudah digunakan.');
            Session::setFlash('old', $request->all());
            Response::redirect('/criteria');
        }

        $userId = Session::get('user_id');
        if (!$userId || !Uuid::isValid($userId)) {
            Session::setFlash('error', 'Sesi tidak valid, silakan login ulang.');
            Response::redirect('/login');
        }

        $userExists = $db->table('users')->where('id', '=', $userId)->first();
        if (!$userExists) {
            Session::destroy();
            Session::setFlash('error', 'Data pengguna tidak ditemukan, silakan login ulang.');
            Response::redirect('/login');
        }

        $data['id'] = Uuid::generate();
        $data['created_by'] = $userId;
        $db->table('criteria')->insert($data);

        Session::setFlash('success', 'Kriteria berhasil ditambahkan.');
        Response::redirect('/criteria');
    }

    public function update(): void
    {
        $request = new Request();
        $id = trim($request->post('id', ''));

        if ($id === '' || !Uuid::isValid($id)) {
            Session::setFlash('error', 'ID kriteria tidak valid.');
            Response::redirect('/criteria');
        }

        $data = $this->validateCriteriaInput($request);
        if ($data === null) {
            return;
        }

        $db = new Model();
        $existing = $db->table('criteria')
            ->where('criteria_code', '=', $data['criteria_code'])
            ->first();

        if ($existing && $existing['id'] !== $id) {
            Session::setFlash('error', 'Kode kriteria sudah digunakan oleh kriteria lain.');
            Session::setFlash('old', $request->all());
            Response::redirect('/criteria');
        }

        unset($data['created_by']);
        $db->table('criteria')->where('id', '=', $id)->update($data);

        Session::setFlash('success', 'Kriteria berhasil diperbarui.');
        Response::redirect('/criteria');
    }

    public function destroy(): void
    {
        $request = new Request();
        $id = trim($request->post('id', ''));

        if ($id === '' || !Uuid::isValid($id)) {
            Session::setFlash('error', 'ID kriteria tidak valid.');
            Response::redirect('/criteria');
        }

        $db = new Model();
        $db->table('criteria')->where('id', '=', $id)->delete();

        Session::setFlash('success', 'Kriteria berhasil dihapus.');
        Response::redirect('/criteria');
    }

    private function validateCriteriaInput(Request $request): ?array
    {
        $criteriaCode = trim($request->post('criteria_code', ''));
        $criteriaName = trim($request->post('criteria_name', ''));
        $description  = trim($request->post('description', ''));
        $type         = trim($request->post('type', ''));

        $errors = [];
        if ($criteriaCode === '') {
            $errors[] = 'Kode kriteria wajib diisi.';
        }
        if ($criteriaName === '') {
            $errors[] = 'Nama kriteria wajib diisi.';
        }
        if ($type === '') {
            $errors[] = 'Tipe kriteria wajib dipilih.';
        } elseif (!in_array($type, ['cost', 'benefit'], true)) {
            $errors[] = 'Tipe kriteria tidak valid.';
        }

        if (!empty($errors)) {
            Session::setFlash('error', implode(' ', $errors));
            Session::setFlash('old', $request->all());
            Response::redirect('/criteria');
        }

        return [
            'criteria_code' => $criteriaCode,
            'criteria_name' => $criteriaName,
            'description'   => $description ?: null,
            'type'          => $type,
        ];
    }
}

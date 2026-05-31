<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Model;
use Core\Request;
use Core\Response;
use Core\Session;
use Core\Uuid;

class SupplierController
{
    public function index(): void
    {
        $db = new Model();
        $suppliers = $db->table('suppliers')
            ->select('*')
            ->orderBy('company_name', 'ASC')
            ->get();

        require_once __DIR__ . '/../../views/suppliers/index.php';
    }

    public function store(): void
    {
        $request = new Request();
        $data = $this->validateSupplierInput($request);
        if ($data === null) {
            return;
        }

        $db = new Model();
        $existing = $db->table('suppliers')
            ->where('supplier_code', '=', $data['supplier_code'])
            ->first();

        if ($existing) {
            Session::setFlash('error', 'Kode supplier sudah digunakan.');
            Session::setFlash('old', $request->all());
            Response::redirect('/suppliers');
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
        $db->table('suppliers')->insert($data);

        Session::setFlash('success', 'Supplier berhasil ditambahkan.');
        Response::redirect('/suppliers');
    }

    public function update(): void
    {
        $request = new Request();
        $id = trim($request->post('id', ''));

        if ($id === '' || !\Core\Uuid::isValid($id)) {
            Session::setFlash('error', 'ID supplier tidak valid.');
            Response::redirect('/suppliers');
        }

        $data = $this->validateSupplierInput($request);
        if ($data === null) {
            return;
        }

        $db = new Model();
        $existing = $db->table('suppliers')
            ->where('supplier_code', '=', $data['supplier_code'])
            ->first();

        if ($existing && $existing['id'] !== $id) {
            Session::setFlash('error', 'Kode supplier sudah digunakan oleh supplier lain.');
            Session::setFlash('old', $request->all());
            Response::redirect('/suppliers');
        }

        unset($data['created_by']);
        $db->table('suppliers')->where('id', '=', $id)->update($data);

        Session::setFlash('success', 'Supplier berhasil diperbarui.');
        Response::redirect('/suppliers');
    }

    public function destroy(): void
    {
        $request = new Request();
        $id = trim($request->post('id', ''));

        if ($id === '' || !\Core\Uuid::isValid($id)) {
            Session::setFlash('error', 'ID supplier tidak valid.');
            Response::redirect('/suppliers');
        }

        $db = new Model();
        $db->table('suppliers')->where('id', '=', $id)->delete();

        Session::setFlash('success', 'Supplier berhasil dihapus.');
        Response::redirect('/suppliers');
    }

    private function validateSupplierInput(Request $request): ?array
    {
        $supplierCode = trim($request->post('supplier_code', ''));
        $companyName  = trim($request->post('company_name', ''));
        $picName      = trim($request->post('pic_name', ''));
        $picEmail     = trim($request->post('pic_email', ''));
        $picPhone     = trim($request->post('pic_phone', ''));
        $taxId        = trim($request->post('tax_id', ''));
        $fullAddress  = trim($request->post('full_address', ''));
        $tireCategory = trim($request->post('tire_category', ''));
        $joinedDate   = trim($request->post('joined_date', ''));

        $errors = [];
        if ($supplierCode === '') $errors[] = 'Kode supplier wajib diisi.';
        if ($companyName === '') $errors[] = 'Nama perusahaan wajib diisi.';
        if ($picName === '') $errors[] = 'Nama PIC wajib diisi.';
        if ($picEmail === '') {
            $errors[] = 'Email PIC wajib diisi.';
        } elseif (!filter_var($picEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email PIC tidak valid.';
        }

        if (!empty($errors)) {
            Session::setFlash('error', implode(' ', $errors));
            Session::setFlash('old', $request->all());
            Response::redirect('/suppliers');
        }

        return [
            'supplier_code' => $supplierCode,
            'company_name'  => $companyName,
            'pic_name'      => $picName,
            'pic_email'     => $picEmail,
            'pic_phone'     => $picPhone ?: null,
            'tax_id'        => $taxId ?: null,
            'full_address'  => $fullAddress ?: null,
            'tire_category' => $tireCategory ?: null,
            'joined_date'   => $joinedDate ?: null,
        ];
    }
}

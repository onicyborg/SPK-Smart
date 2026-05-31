# AGENTS.md — Pedoman Umum Pengembangan Native PHP MVC Framework
# Proyek: SPK Pemilihan Supplier Ban Bandara

Dokumen ini adalah **aturan wajib** (system prompt) untuk seluruh AI agent dan developer manusia yang bekerja pada codebase **Native PHP MVC Semi-Framework** (dibangun dari scratch, tanpa Composer, tanpa third-party package).

Target utama:
- Menjaga **konsistensi UI/UX** (Bootstrap + Metronic) di seluruh modul.
- Menjaga konsistensi **pola CRUD berbasis Modal** (bukan halaman create/edit terpisah) untuk master data.
- Menjaga konsistensi **pola DataTables** untuk listing.
- Menjaga konsistensi **pola Controller** (method `index`, `store`, `update`, `destroy`), validasi manual via `Core\Request`, serta **flash message** / JSON response.
- Menjaga konsistensi **kontrol akses** berbasis role statis via `Core\Session`.

---

## Daftar Isi

- [0) Ringkasan Tech Stack & UI Kit](#0-ringkasan-tech-stack--ui-kit)
- [1) Pola Frontend & Implementasi UI](#1-pola-frontend--implementasi-ui)
  - [1.1 Struktur View, Layout Utama, dan Include](#11-struktur-view-layout-utama-dan-include)
  - [1.2 Pola CRUD di UI: Modal Create/Edit + Konfirmasi Delete](#12-pola-crud-di-ui-modal-createedit--konfirmasi-delete)
  - [1.3 Pola Data Display: DataTables](#13-pola-data-display-datatables)
  - [1.4 Komponen UI Kustom / Kompleks](#14-komponen-ui-kustom--kompleks)
  - [1.5 Pengecualian Pola CRUD Modal (Mass/Bulk Data Entry)](#15-pengecualian-pola-crud-modal-massbulk-data-entry)
- [2) Penanganan JavaScript & AJAX](#2-penanganan-javascript--ajax)
  - [2.5 Tips JSON pada Atribut HTML](#25-tips-json-pada-atribut-html)
- [3) Notifikasi UI](#3-notifikasi-ui)
- [4) Kontrol Akses](#4-kontrol-akses)
- [5) Standar System Logs](#5-standar-system-logs)
- [6) Konvensi Model & Routing](#6-konvensi-model--routing)
- [7) Template Wajib untuk Modul CRUD Baru (Checklist)](#7-template-wajib-untuk-modul-crud-baru-checklist)
- [8) Larangan / Anti-Pattern](#8-larangan--anti-pattern)
- [9) Standarisasi Penggunaan Metronic](#9-standarisasi-penggunaan-metronic)

---

# 0) Ringkasan Tech Stack & UI Kit

- **Backend**
  - Native PHP `^8.1`, **tanpa framework, tanpa Composer, tanpa third-party package**.
  - Arsitektur MVC semi-framework buatan sendiri.
  - Custom Autoloader:
    - Namespace `App\` → direktori `/app`
    - Namespace `Core\` → direktori `/core`

- **Frontend**
  - Tidak menggunakan Vite, Laravel Mix, atau build tool apapun.
  - Asset dimuat langsung dari folder `public/assets` menggunakan variabel `ASSET_URL` dari `.env`.
  - AJAX menggunakan native **Fetch API** atau **jQuery AJAX**. Contoh utama di pedoman ini menggunakan **Fetch**.

- **UI Framework / Template**
  - Bootstrap 5 + Metronic (admin template).
  - Vendor umum:
    - DataTables
    - FullCalendar (opsional)
  - Ikon: Bootstrap Icons.

**Aturan:** setiap halaman baru wajib menggunakan pola `require_once` Top-Bottom untuk layout, dan meletakkan JS halaman dalam `<script>` tag di bagian bawah file view, sebelum `require_once footer.php`.

Tambahan aturan konsistensi proyek (wajib):
- Password default untuk user role `admin` saat dibuat: `Qwerty123*`.
- Seluruh URL file lampiran (storage publik) menggunakan path relatif `<?= $_ENV['ASSET_URL'] ?>/storage/...`.

---

# 1) POLA FRONTEND & IMPLEMENTASI UI

## 1.1 Struktur View, Layout Utama, dan Include

- **Layout utama:**
  - Header: `views/layouts/master.php` — memuat semua CSS global dan membuka tag body/wrapper.
  - Footer: `views/layouts/footer.php` — memuat semua JS global dan menutup tag body.
  - Sidebar dipanggil via `require_once` di dalam `master.php`.

- **Cara menyisipkan page title:** Set variabel `$pageTitle` sebelum include header.

**Snippet (layout master — bagian asset):**
```php
<!-- views/layouts/master.php -->
<link href="<?= $_ENV['ASSET_URL'] ?>/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" />
<link href="<?= $_ENV['ASSET_URL'] ?>/plugins/global/plugins.bundle.css" rel="stylesheet" />
<link href="<?= $_ENV['ASSET_URL'] ?>/css/style.bundle.css" rel="stylesheet" />

...
<div class="app-container container-fluid py-5">
    <!-- Konten halaman dimuat di sini via require_once view file -->
</div>
```

```php
<!-- views/layouts/footer.php -->
<script src="<?= $_ENV['ASSET_URL'] ?>/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="<?= $_ENV['ASSET_URL'] ?>/plugins/global/plugins.bundle.js"></script>
<script src="<?= $_ENV['ASSET_URL'] ?>/js/scripts.bundle.js"></script>
</body>
</html>
```

**Kerangka view halaman minimal:**
```php
<?php
$pageTitle = 'Judul Halaman';
require_once __DIR__ . '/../layouts/master.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
    <h3 class="fw-bold mb-0">Data ...</h3>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#resourceModal" id="btnAddResource">
        <i class="bi bi-plus-lg me-2"></i>Tambah ...
    </button>
</div>

<div class="card">
    <div class="card-body">
        ...
    </div>
</div>

<!-- Flash messages Toastr -->
<?php $flashSuccess = \Core\Session::getFlash('success'); ?>
<?php if ($flashSuccess): ?>
<script>
    (function(){
        var msg = <?= json_encode($flashSuccess) ?>;
        if (window.toastr && toastr.success) { toastr.success(msg); }
        else { console.log('SUCCESS:', msg); }
    })();
</script>
<?php endif; ?>

<!-- JS halaman -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // init JS halaman
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
```

**Aturan implementasi view:**
- **Gunakan header halaman yang konsisten**: judul di kiri, tombol aksi utama di kanan.
- **Gunakan komponen Metronic/Bootstrap**: `card`, `card-body`, `table-responsive`, `btn btn-primary`, dll.
- **Satu file = satu halaman**: satu pasang `require_once master.php` ... `require_once footer.php` per file view.

---

## 1.2 Pola CRUD di UI: **Modal Create/Edit + Modal Konfirmasi Delete**

Standar ini menggunakan **Modal** untuk create/edit, dan **modal konfirmasi** untuk delete. Untuk master data, hindari membuat file view `create.php` atau `edit.php` terpisah.

### 1.2.1 Kerangka Modal Create/Edit

**Template wajib (contoh resource generik):**
```php
<!-- views/admin/<resource>/index.php -->
<div class="modal fade" id="resourceModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="resourceForm" method="POST" action="/resource/store">
        <?= csrf_field() ?>
        <input type="hidden" name="_method" id="resourceFormMethod" value="POST">
        <input type="hidden" name="id" id="resource_id">

        <div class="modal-header">
          <h5 class="modal-title" id="resourceModalTitle">Tambah Data</h5>
          <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>

        <div class="modal-body">
          ...fields...
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary" id="btnSaveResource">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
```

**Aturan:**
- **Selalu ada** `<?= csrf_field() ?>` di dalam form.
- **Selalu ada** hidden `_method` untuk switch POST/PUT.
- Mode create/edit ditentukan via JavaScript:
  - `form.action` diarahkan ke `/resource/store` (create) atau `/resource/update/{id}` (edit).
  - `resourceFormMethod.value` diset ke `POST` atau `PUT`.
  - `modalTitle.textContent` disesuaikan.

### 1.2.2 Kerangka Modal Konfirmasi Delete

**Template wajib:**
```php
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="deleteForm" method="POST" action="">
        <?= csrf_field() ?>
        <input type="hidden" name="_method" value="DELETE">
        <div class="modal-header">
          <h5 class="modal-title">Hapus Data</h5>
          <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        <div class="modal-body">
          <p>Yakin ingin menghapus <strong id="delete_name">-</strong>?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Hapus</button>
        </div>
      </form>
    </div>
  </div>
</div>
```

**Aturan:**
- Tombol delete pada tabel wajib menyimpan `data-id` dan `data-name`.
- JS wajib mengubah `deleteForm.action` ke URL `/resource/destroy/{id}` saat modal dibuka.

---

## 1.3 Pola Data Display: DataTables

### 1.3.1 Konfigurasi DataTables

Standar default: gunakan **client-side DataTables** untuk data yang sudah dirender oleh PHP view. Bila data sangat besar, boleh beralih ke server-side, tapi pastikan konsisten di seluruh modul.

**Markup tabel (standar Metronic):**
```html
<div class="table-responsive">
    <table id="resource_table" class="table align-middle table-row-dashed fs-6 gy-5">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>Kolom 1</th>
                <th>Kolom 2</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            ...
        </tbody>
    </table>
</div>
```

**Snippet (init DataTable):**
```js
document.addEventListener('DOMContentLoaded', function () {
  if (window.jQuery && jQuery.fn && jQuery.fn.DataTable) {
    const dt = jQuery('#resource_table').DataTable({
      pageLength: 10,
      ordering: true,
    });

    // Optional: external search
    const $search = jQuery('#resource_search');
    $search.on('keyup change', function () {
      dt.search(this.value || '').draw();
    });
  }
});
```

**Aturan:**
- Inisialisasi DataTables diletakkan di `<script>` tag bagian bawah file view, sebelum `require_once footer.php`.
- Table markup **wajib** mengikuti class Metronic di atas.

### 1.3.2 Reload DataTables Setelah CRUD

Standar default: CRUD master data dilakukan via submit form normal (bukan AJAX). Setelah submit, controller melakukan redirect dan tabel tampil ulang dengan data terbaru.

---

### 1.3.3 Event Aksi pada Tabel (Wajib Delegated untuk DataTables)

Karena DataTables melakukan re-render DOM saat pagination/sort/search, binding event langsung ke tombol dengan `querySelectorAll(...).addEventListener(...)` akan tidak bekerja di halaman 2 dst. Gunakan **delegated event** pada elemen tabel (jQuery) agar tetap berfungsi.

**Snippet (jQuery, disarankan):**
```js
// Pastikan inisialisasi DataTables lebih dulu
const dt = jQuery('#resource_table').DataTable({ pageLength: 10, ordering: true });

// Delegated handler untuk tombol Edit
jQuery('#resource_table').on('click', '.btnEditResource', function(){
    const id   = this.dataset.id;
    const name = this.dataset.name;
    // ... isi form modal edit
    document.getElementById('resourceFormMethod').value = 'PUT';
    document.getElementById('resourceForm').action = '/resource/update/' + id;
    document.getElementById('resourceModalTitle').textContent = 'Edit Data';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('resourceModal')).show();
});

// Delegated handler untuk tombol Delete
jQuery('#resource_table').on('click', '.btnDeleteResource', function(){
    const id   = this.dataset.id;
    const name = this.dataset.name;
    document.getElementById('delete_name').textContent = name;
    document.getElementById('deleteForm').action = '/resource/destroy/' + id;
    bootstrap.Modal.getOrCreateInstance(document.getElementById('confirmDeleteModal')).show();
});
```

Fallback tanpa jQuery (bila tidak memakai DataTables): gunakan `addEventListener` biasa. Namun untuk halaman standar dengan DataTables, **wajib** pakai pola delegated di atas.

---

## 1.4 Komponen UI Kustom / Kompleks

### 1.4.1 Image Input (Metronic `data-kt-image-input`)

Digunakan untuk upload foto profil pengguna atau entitas lain yang relevan.

**Snippet (profile modal):**
```php
<div class="image-input image-input-circle" data-kt-image-input="true"
     style="background-image: url('<?= $_ENV['ASSET_URL'] ?>/media/svg/avatars/blank.svg')">
  <div id="profile_photo_wrapper" class="image-input-wrapper w-125px h-125px"></div>
  <label class="btn btn-icon btn-circle btn-active-color-primary w-30px h-30px bg-body shadow"
         data-kt-image-input-action="change">
    <i class="bi bi-pencil-fill fs-7"></i>
    <input type="file" name="photo" id="profile_photo" accept=".png, .jpg, .jpeg, .webp" />
  </label>
</div>
<div class="invalid-feedback d-block" data-field="photo"></div>
```

**Snippet (preview pakai FileReader):**
```js
const photoInput   = document.getElementById('profile_photo');
const photoWrapper = document.getElementById('profile_photo_wrapper');

photoInput?.addEventListener('change', function(){
    const f = photoInput.files && photoInput.files[0];
    if (!f) return;
    const reader = new FileReader();
    reader.onload = (e) => {
        if (photoWrapper) photoWrapper.style.backgroundImage = `url('${e.target.result}')`;
    };
    reader.readAsDataURL(f);
});
```

### 1.4.2 Select2 untuk Combobox

Pola `select2` untuk combobox di dalam modal.

**Snippet:**
```js
jQuery('#generic_select_id').select2({
    dropdownParent: jQuery('#genericModal'),
    width: '100%'
});
```

**Aturan:**
- Untuk select di dalam modal, **wajib** `dropdownParent: jQuery('#<modalId>')` agar dropdown tidak tertutup modal.
- Inisialisasi select2 di dalam event `shown.bs.modal` agar ukuran dropdown benar.

### 1.4.3 Date Input

Gunakan input native HTML5:
```html
<input type="date" name="date_from" class="form-control">
```

---

## 1.5 Pengecualian Pola CRUD Modal (Mass/Bulk Data Entry)

Aturan wajib menggunakan Modal untuk Create/Edit **hanya berlaku mutlak untuk halaman Master Data** (Data Supplier, Kriteria, Referensi, dll).

**PENGECUALIAN DIBERIKAN** untuk halaman Transaksi Massal atau Entry Data Bulk. Untuk UX yang lebih efisien, diizinkan menggunakan pendekatan **Inline Editable Grid** langsung di halaman utama (tanpa Modal) yang dibungkus dalam form massal, demi kecepatan input data banyak baris sekaligus.

---

# 2) PENANGANAN JAVASCRIPT & AJAX

## 2.1 Prinsip Umum

- JS per halaman diletakkan dalam `<script>` tag di bagian bawah file view, **sebelum** `require_once footer.php`.
- Untuk master data, submit form modal umumnya **non-AJAX** (HTTP form submit biasa), lalu controller melakukan `Core\Response::redirect(...)` dengan flash message.
- Untuk halaman profil atau endpoint tertentu, submit menggunakan **Fetch** + JSON response.

---

## 2.2 Pola Submit AJAX (Fetch) + Validasi

Gunakan pola ini untuk form yang butuh UX cepat (mis. profil, upload ringan, modal detail dengan update kecil).

### 2.2.1 Template Helper: clearValidation

**Snippet:**
```js
function clearValidation(scope) {
    (scope || document).querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    (scope || document).querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
}
```

### 2.2.2 Submit Form Profile (Fetch)

**Snippet:**
```js
const profileForm = document.getElementById('profileForm');
const CSRF_TOKEN  = document.querySelector('meta[name="csrf-token"]')?.content;

profileForm?.addEventListener('submit', function(e) {
    e.preventDefault();
    clearValidation(profileForm);

    const formData = new FormData(profileForm);
    formData.set('_method', 'PUT');

    fetch('/profile/update', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: formData
    })
    .then(async (resp) => {
        const data = await resp.json();
        if (!resp.ok) throw { status: resp.status, data };
        window.toastr?.success?.(data.message || 'Berhasil disimpan');
    })
    .catch(err => {
        const errs = err?.data?.errors || {};
        Object.keys(errs).forEach(field => {
            const input = profileForm.querySelector(`[name="${field}"]`);
            input && input.classList.add('is-invalid');
            const fb = profileForm.querySelector(`.invalid-feedback[data-field="${field}"]`);
            fb && (fb.textContent = errs[field][0]);
        });
        if (!Object.keys(errs).length) window.toastr?.error?.('Terjadi kesalahan.');
    });
});
```

**Aturan wajib untuk AJAX form:**
- Gunakan `fetch()` + `FormData`.
- Sertakan header `X-CSRF-TOKEN` dari meta tag yang di-render oleh `master.php`.
- Bila metode `PUT/DELETE`, set `_method` pada FormData.
- Handling error:
  - Baca `err.data.errors` (struktur `{ field: [msg] }`).
  - Tandai field dengan `.is-invalid`.
  - Render pesan ke elemen `.invalid-feedback[data-field="..."]`.
- Notifikasi: gunakan `window.toastr?.success?.(...)` / `window.toastr?.error?.(...)`.

---

## 2.3 Notifikasi UI (Toastr) dan Flash Message

Untuk halaman non-AJAX (redirect), pola notifikasi di view adalah:

**Snippet (flash success/error/errors):**
```php
<?php $flashSuccess = \Core\Session::getFlash('success'); ?>
<?php if ($flashSuccess): ?>
<script>
    (function(){
        var msg = <?= json_encode($flashSuccess) ?>;
        if (window.toastr && toastr.success) { toastr.success(msg); }
        else { console.log('SUCCESS:', msg); }
    })();
</script>
<?php endif; ?>

<?php $flashError = \Core\Session::getFlash('error'); ?>
<?php if ($flashError): ?>
<script>
    (function(){
        var msg = <?= json_encode($flashError) ?>;
        if (window.toastr && toastr.error) { toastr.error(msg); }
        else { console.error('ERROR:', msg); }
    })();
</script>
<?php endif; ?>

<?php $flashErrors = \Core\Session::getFlash('errors'); ?>
<?php if (!empty($flashErrors) && is_array($flashErrors)): ?>
<script>
    (function(){
        var errs = <?= json_encode($flashErrors) ?>;
        var msg = errs.join('\n');
        if (window.toastr && toastr.error) { toastr.error(msg); }
        else { console.error('ERRORS:', msg); }
    })();
</script>
<?php endif; ?>
```

**Aturan:**
- Bila controller menggunakan `Core\Session::setFlash('success', '...')` / `Core\Session::setFlash('error', '...')`, view **wajib** menyediakan blok Toastr di atas.
- Letakkan blok flash **sebelum** tag `<script>` inisialisasi halaman.

---

## 2.4 Modal Detail dengan Fetch (Read-only)

Pola fetch saat modal dibuka (`show.bs.modal`) untuk mengambil detail data.

**Snippet:**
```js
const detailModal = document.getElementById('detailModal');
const detailBody  = document.getElementById('detailModalBody');

detailModal?.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const id     = button.getAttribute('data-id');

    fetch('/resource/' + id, {
        headers: { 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
        detailBody.innerHTML = `
            <p><strong>Nama:</strong> ${data.name ?? '-'}</p>
            <p><strong>Deskripsi:</strong> ${(data.description ?? '').replace(/</g, '&lt;')}</p>
        `;
    })
    .catch(() => {
        detailBody.innerHTML = '<div class="text-danger">Gagal memuat detail.</div>';
    });
});
```

**Aturan:**
- Untuk modal detail/read-only, **boleh** fetch on-open dan render via template string.
- Selalu escape konten raw dengan `.replace(/</g, '&lt;')` untuk mencegah XSS.

---

## 2.5 Tips JSON pada Atribut HTML

Untuk menyisipkan data PHP kompleks ke atribut `data-*`, gunakan `json_encode()`.

**Aturan:**
- Gunakan `json_encode()` pada array PHP dan bungkus atribut dengan tanda kutip ganda HTML.
- Hindari menyisipkan JSON kompleks (dengan banyak tanda kutip) langsung di dalam atribut yang sudah dikutip ganda — tampung dulu di variabel PHP, baru echo.

**Snippet aman:**
```php
<?php
$items = array_map(function($it) {
    return [
        'id'    => $it['id'],
        'name'  => $it['name'],
        'price' => $it['price'],
    ];
}, $orderItems);
$itemsJson = htmlspecialchars(json_encode($items), ENT_QUOTES, 'UTF-8');
?>

<button class="btn btn-sm btn-info btnViewItems"
        data-items="<?= $itemsJson ?>">
    Lihat Item
</button>
```

**Catatan:**
- Selalu pakai `htmlspecialchars(..., ENT_QUOTES)` saat menyisipkan JSON ke dalam atribut HTML.
- Gunakan `JSON.parse(this.dataset.items)` di JavaScript untuk mengambil datanya.

---

# 3) KONVENSI CONTROLLER & VALIDASI

## 3.1 Struktur Controller

- Controller berada di `app/Controllers/`.
- Namespace: `App\Controllers\`.
- Pola method yang dipakai:
  - `index()` — render listing.
  - `store()` — create (proses dari POST).
  - `update(int|string $id)` — update (proses dari POST + `_method=PUT`).
  - `destroy(int|string $id)` — delete (proses dari POST + `_method=DELETE`).

**Snippet (controller resource generik):**
```php
<?php

namespace App\Controllers\Admin;

use Core\Request;
use Core\Response;
use Core\Session;
use Core\Model;

class ResourceController
{
    private Model $db;

    public function __construct()
    {
        $this->db = new Model('resources');
    }

    public function index(): void
    {
        $items = $this->db->select('*')->orderBy('created_at', 'DESC')->get();
        require_once __DIR__ . '/../../views/admin/resource/index.php';
    }

    public function store(): void
    {
        $request = new Request();

        $name = trim($request->post('name'));
        if (empty($name)) {
            Session::setFlash('errors', ['Nama wajib diisi.']);
            Response::redirect('/admin/resource');
            return;
        }

        $this->db->insert([
            'name'       => $name,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        Session::setFlash('success', 'Data berhasil ditambahkan.');
        Response::redirect('/admin/resource');
    }

    public function update(string $id): void
    {
        $request = new Request();

        $name = trim($request->post('name'));
        if (empty($name)) {
            Session::setFlash('errors', ['Nama wajib diisi.']);
            Response::redirect('/admin/resource');
            return;
        }

        $this->db->where('id', $id)->update([
            'name'       => $name,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        Session::setFlash('success', 'Data berhasil diperbarui.');
        Response::redirect('/admin/resource');
    }

    public function destroy(string $id): void
    {
        $this->db->where('id', $id)->delete();

        Session::setFlash('success', 'Data berhasil dihapus.');
        Response::redirect('/admin/resource');
    }
}
```

**Aturan:**
- Untuk master data, controller **mengembalikan redirect** + flash message via `Core\Session::setFlash()` dan `Core\Response::redirect()`.
- Data untuk view diset sebagai variabel PHP biasa sebelum `require_once` view.
- Tidak ada `compact()`, tidak ada `view()` helper ala Laravel — variable assignment langsung.

---

## 3.2 Validasi Input

### 3.2.1 Validasi Manual via Core\Request

Standar ini menggunakan validasi manual langsung di controller via `Core\Request`. **Tidak ada FormRequest class terpisah**.

**Aturan wajib:**
- Ambil data input dengan `$request->post('field')` atau `$request->get('field')`.
- Validasi secara eksplisit (cek kosong, panjang, format, dll.) dan return early dengan flash error bila gagal.
- Jangan memperkenalkan library validasi eksternal.

**Snippet validasi lengkap:**
```php
$request = new Request();

$name     = trim($request->post('name') ?? '');
$email    = trim($request->post('email') ?? '');
$roleId   = (int) $request->post('role_id');

$errors = [];

if (empty($name)) {
    $errors[] = 'Nama wajib diisi.';
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email tidak valid.';
}
if ($roleId <= 0) {
    $errors[] = 'Role wajib dipilih.';
}

if (!empty($errors)) {
    Session::setFlash('errors', $errors);
    Response::redirect('/admin/resource');
    return;
}
```

### 3.2.2 Validasi Bisnis Tambahan

Contoh validasi bisnis setelah sanitasi input:

```php
// Cek duplikasi nama supplier
$existing = $this->db->select('id')->where('name', $name)->first();
if ($existing) {
    Session::setFlash('errors', ['Nama supplier sudah terdaftar.']);
    Response::redirect('/admin/supplier');
    return;
}
```

---

## 3.3 Standar Response

### 3.3.1 Redirect + Flash Message (Non-AJAX)

```php
Session::setFlash('success', 'Data berhasil ditambahkan.');
Response::redirect('/admin/resource');
```

### 3.3.2 JSON Response (AJAX)

Digunakan pada endpoint yang dipanggil via Fetch.

**Snippet (sukses):**
```php
Response::json([
    'message' => 'Berhasil disimpan',
    'data'    => $data,
]);
```

**Snippet (error validasi):**
```php
Response::json([
    'message' => 'Validasi gagal',
    'errors'  => [
        'photo' => ['Ukuran file melebihi batas 2MB.'],
    ],
], 422);
```

**Aturan:**
- Response JSON **wajib** memiliki key `message`.
- Jika error field-level, **wajib** gunakan struktur `errors: { field: [msg] }` agar UI dapat menampilkan per-field.
- HTTP status code yang tepat wajib disertakan sebagai argumen kedua `Response::json()`.

---

# 4) KONTROL AKSES

Proyek ini menggunakan **kontrol akses statis berbasis role** via `Core\Session`. Tidak ada Gates, Policies, atau paket permission dinamis.

## 4.1 Skema Role

- Field `role` berada pada tabel `users` (contoh: `'admin'` | `'staff'`).
- Sidebar bersifat statis berdasarkan role — tidak ada `@can` dinamis.
- Redirect pasca-login diarahkan ke dashboard sesuai role.

## 4.2 Middleware

Middleware diterapkan via Router pada saat registrasi route.

**Registrasi route dengan middleware:**
```php
// routes/web.php
$router->get('/admin/dashboard', [DashboardController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->get('/admin/supplier',  [SupplierController::class,  'index'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->post('/admin/supplier', [SupplierController::class,  'store'], [AuthMiddleware::class, AdminMiddleware::class]);
```

**Implementasi AuthMiddleware:**
```php
<?php

namespace App\Middleware;

use Core\Session;
use Core\Response;

class AuthMiddleware
{
    public function handle(): void
    {
        if (!Session::get('user_id')) {
            Response::redirect('/login');
            exit;
        }
    }
}
```

**Implementasi AdminMiddleware:**
```php
<?php

namespace App\Middleware;

use Core\Session;
use Core\Response;

class AdminMiddleware
{
    public function handle(): void
    {
        if (Session::get('role') !== 'admin') {
            Response::redirect('/403');
            exit;
        }
    }
}
```

## 4.3 Pengecekan Role di Controller (Manual)

Bila perlu cek akses di dalam controller secara granular:

```php
if (\Core\Session::get('role') !== 'admin') {
    \Core\Response::redirect('/403');
    exit;
}
```

## 4.4 Pengecekan Role di View (Kondisional UI)

Untuk menampilkan/menyembunyikan tombol atau elemen berdasarkan role:

```php
<?php if (\Core\Session::get('role') === 'admin'): ?>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#resourceModal">
        <i class="bi bi-plus-lg me-2"></i>Tambah
    </button>
<?php endif; ?>
```

---

# 5) STANDAR SYSTEM LOGS

Seluruh aksi penting sistem dicatat ke tabel `system_logs`. Struktur kolom utama:
- `id` (UUID), `user_id` (nullable UUID)
- `action` (string) — contoh: `created`, `updated`, `deleted`, `login`, `logout`
- `table_name` (string), `record_id` (string UUID dari entitas)
- `old_values` (json), `new_values` (json)
- `method` (HTTP method), `url` (full URL), `request_payload` (json), `ip_address` (string)

## 5.1 Setup

- Pastikan tabel `system_logs` sudah dibuat via SQL migration manual.
- Buat helper class `App\Support\SystemLogger` untuk mempermudah pencatatan.

**Contoh helper:**
```php
<?php
// app/Support/SystemLogger.php
namespace App\Support;

use Core\Model;
use Core\Session;

class SystemLogger
{
    public static function record(
        string  $action,
        string  $tableName,
        string  $recordId,
        ?array  $old = null,
        ?array  $new = null
    ): void {
        $db = new Model('system_logs');
        $db->insert([
            'id'              => \Core\Uuid::generate(),
            'user_id'         => Session::get('user_id'),
            'action'          => $action,
            'table_name'      => $tableName,
            'record_id'       => $recordId,
            'old_values'      => $old ? json_encode($old) : null,
            'new_values'      => $new ? json_encode($new) : null,
            'method'          => $_SERVER['REQUEST_METHOD'] ?? null,
            'url'             => (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? ''),
            'request_payload' => json_encode($_POST ?: []),
            'ip_address'      => $_SERVER['REMOTE_ADDR'] ?? null,
            'created_at'      => date('Y-m-d H:i:s'),
        ]);
    }
}
```

## 5.2 Penggunaan (Standarisasi di Controller)

- **Create (store):** catat `action = created`, `old_values = null`, `new_values = data baru`.
```php
$id = $this->db->insert($payload);
SystemLogger::record('created', 'suppliers', (string)$id, null, $payload);
```

- **Update:** catat `action = updated`, simpan snapshot sebelum dan sesudah.
```php
$before = $this->db->select('*')->where('id', $id)->first();
$this->db->where('id', $id)->update($payload);
$after  = $this->db->select('*')->where('id', $id)->first();
SystemLogger::record('updated', 'suppliers', (string)$id, (array)$before, (array)$after);
```

- **Delete:** catat `action = deleted`, `old_values = snapshot sebelum delete`, `new_values = null`.
```php
$before = $this->db->select('*')->where('id', $id)->first();
$this->db->where('id', $id)->delete();
SystemLogger::record('deleted', 'suppliers', (string)$id, (array)$before, null);
```

**Catatan:**
- Gunakan nama `table_name` sesuai nama tabel database yang sebenarnya.
- Bersihkan field sensitif (password, token) dari `request_payload` sebelum dicatat.
- `record_id` menyimpan ID string dari entitas yang dimodifikasi.

---

# 6) KONVENSI MODEL & ROUTING

## 6.1 Konvensi Model (Query Builder)

Model berada di `app/Models/` dan **tidak menggunakan Eloquent**. Semua query dilakukan via `Core\Model` (custom Query Builder).

**Aturan:**
- Instansiasi `Core\Model` dengan nama tabel sebagai argumen.
- Tidak ada mass assignment `$fillable` — kolom ditulis eksplisit di array `insert()`/`update()`.
- Tidak ada UUID trait otomatis — generate UUID manual via `\Core\Uuid::generate()` bila tabel memakai UUID.

**Snippet (pola query umum):**
```php
$db = new \Core\Model('suppliers');

// SELECT semua
$suppliers = $db->select('*')->orderBy('name', 'ASC')->get();

// SELECT dengan kondisi
$supplier = $db->select('*')->where('id', $id)->first();

// INSERT
$db->insert([
    'id'         => \Core\Uuid::generate(),
    'name'       => $name,
    'created_at' => date('Y-m-d H:i:s'),
]);

// UPDATE
$db->where('id', $id)->update([
    'name'       => $name,
    'updated_at' => date('Y-m-d H:i:s'),
]);

// DELETE
$db->where('id', $id)->delete();

// JOIN
$results = $db
    ->select('suppliers.*, categories.name AS category_name')
    ->join('categories', 'suppliers.category_id', '=', 'categories.id')
    ->where('suppliers.is_active', 1)
    ->get();
```

## 6.2 Konvensi Routing

Route didaftarkan di `routes/web.php` menggunakan custom Router.

**Format registrasi:**
```php
$router->get('/path',         [ControllerClass::class, 'method'], [MiddlewareClass::class]);
$router->post('/path',        [ControllerClass::class, 'method'], [MiddlewareClass::class]);
$router->put('/path/{id}',    [ControllerClass::class, 'method'], [MiddlewareClass::class]);
$router->delete('/path/{id}', [ControllerClass::class, 'method'], [MiddlewareClass::class]);
```

**Contoh routing lengkap satu resource:**
```php
use App\Controllers\Admin\SupplierController;
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;

$mw = [AuthMiddleware::class, AdminMiddleware::class];

$router->get('/admin/supplier',          [SupplierController::class, 'index'],   $mw);
$router->post('/admin/supplier',         [SupplierController::class, 'store'],   $mw);
$router->post('/admin/supplier/{id}',    [SupplierController::class, 'update'],  $mw); // _method=PUT
$router->post('/admin/supplier/del/{id}',[SupplierController::class, 'destroy'], $mw); // _method=DELETE
```

**Aturan:**
- Grup route admin wajib menggunakan prefix `/admin/...` dan middleware `[AuthMiddleware::class, AdminMiddleware::class]`.
- Tidak ada `Route::resource()` — setiap endpoint didaftarkan eksplisit.
- Method override (`PUT`, `DELETE`) diterima via hidden input `_method` dan dihandle oleh Router.

---

# 7) TEMPLATE WAJIB UNTUK MODUL CRUD BARU (Checklist)

Gunakan checklist ini setiap kali menambah modul master data baru.

**View (`views/admin/<resource>/index.php`)**
- [ ] `require_once` ke `master.php` di bagian atas, set `$pageTitle` sebelumnya
- [ ] Header halaman konsisten (`h3` + tombol `Tambah` membuka modal)
- [ ] Table markup Metronic + `id="resource_table"`
- [ ] Modal Create/Edit:
  - `<form method="POST" action="...">`
  - `<?= csrf_field() ?>`
  - Hidden input `_method` id `resourceFormMethod`
  - Hidden input `id` id `resource_id`
- [ ] Modal Delete Confirm:
  - `<form id="deleteForm" method="POST" action="">`
  - `<?= csrf_field() ?>`
  - `<input type="hidden" name="_method" value="DELETE">`
- [ ] Blok flash Toastr untuk `Core\Session::getFlash('success')`, `getFlash('error')`, `getFlash('errors')`
- [ ] `<script>` tag di bagian bawah (sebelum `require_once footer.php`):
  - Init DataTable
  - Delegated handler tombol Edit (set `form.action`, `_method`, isi field, buka modal)
  - Delegated handler tombol Delete (set `deleteForm.action`, `delete_name`, buka modal)
- [ ] `require_once` ke `footer.php` di bagian paling bawah

**Controller (`app/Controllers/Admin/<Resource>Controller.php`)**
- [ ] Method: `index`, `store`, `update($id)`, `destroy($id)`
- [ ] Validasi manual via `Core\Request` dengan early return + `Session::setFlash('errors', [...])`
- [ ] Persist via `Core\Model` (insert/update/delete eksplisit)
- [ ] Response: `Session::setFlash('success', '...')` + `Response::redirect('/admin/<resource>')`

**Routes (`routes/web.php`)**
- [ ] Tambahkan pada grup middleware admin yang sesuai
- [ ] Daftarkan endpoint GET (index), POST (store), POST+`_method=PUT` (update), POST+`_method=DELETE` (destroy) secara eksplisit

**SystemLogger**
- [ ] Catat aksi `created`, `updated`, `deleted` di setiap method yang relevan

---

# 8) LARANGAN / ANTI-PATTERN

- **JANGAN** membuat file `create.php` / `edit.php` untuk master data jika pola modul sejenis memakai modal.
- **JANGAN** mengubah layout global (`master.php` / `footer.php`) tanpa alasan kuat; semua halaman harus tetap kompatibel.
- **JANGAN** memperkenalkan library notifikasi baru (mis. SweetAlert) untuk modul baru — gunakan `toastr` sebagaimana yang sudah dipakai.
- **JANGAN** menggunakan Eloquent, Laravel helper, atau Blade directive apapun — proyek ini adalah Native PHP murni.
- **JANGAN** menggunakan `echo asset(...)` atau `url(...)` ala Laravel — gunakan `<?= $_ENV['ASSET_URL'] ?>/...`.
- **JANGAN** menggunakan `session('key')` ala Laravel — gunakan `\Core\Session::getFlash('key')` atau `\Core\Session::get('key')`.
- **JANGAN** menggunakan `redirect()->route(...)` atau `response()->json(...)` — gunakan `\Core\Response::redirect(...)` dan `\Core\Response::json(...)`.
- **JANGAN** melakukan binding event langsung ke tombol di dalam DataTables tanpa delegated event — gunakan `jQuery('#table').on('click', '.btnClass', fn)`.

---

# 9) STANDARISASI PENGGUNAAN METRONIC

Untuk standarisasi setup dan cara penggunaan komponen Metronic yang dipakai di proyek ini (layout, DataTables, modal CRUD, toastr, image input, select2, FullCalendar), lihat:

- `docs/metronic.md`

Dokumen tersebut adalah referensi komponen UI yang telah disesuaikan dengan Native PHP (tanpa Blade).

---

# Status Dokumen

`AGENTS.md` ini adalah **rulebook aktif** proyek **SPK Pemilihan Supplier Ban Bandara**.

- Setiap penambahan modul baru wajib merujuk dokumen ini.
- Jika ada keputusan arsitektur baru (misal: switch ke server-side DataTables, penambahan role baru, perubahan struktur routing), **update dokumen ini terlebih dahulu** sebelum implementasi.
- Konsistensi di seluruh modul adalah tanggung jawab setiap developer dan AI agent yang bekerja pada proyek ini.
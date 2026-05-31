<?php
$pageTitle = 'Manajemen User';
require_once __DIR__ . '/../layouts/master.php';

$old = \Core\Session::getFlash('old') ?? [];
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
    <h3 class="fw-bold mb-0">Manajemen Pengguna</h3>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="bi bi-plus-lg me-2"></i>Tambah Pengguna
    </button>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table id="users_table" class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th>Username</th>
                        <th>Email</th>
                        <th>Nama Lengkap</th>
                        <th>Role</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?= htmlspecialchars($u['username']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= htmlspecialchars($u['full_name'] ?? '-') ?></td>
                            <td>
                                <?php if ($u['role'] === 'admin'): ?>
                                    <span class="badge badge-light-danger">Administrator</span>
                                <?php elseif ($u['role'] === 'staff'): ?>
                                    <span class="badge badge-light-primary">Staff</span>
                                <?php else: ?>
                                    <span class="badge badge-light-info">Pimpinan</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-icon btn-light btn-active-light-primary btnEditUser"
                                    data-id="<?= htmlspecialchars($u['id']) ?>"
                                    data-username="<?= htmlspecialchars($u['username']) ?>"
                                    data-email="<?= htmlspecialchars($u['email']) ?>"
                                    data-name="<?= htmlspecialchars($u['full_name'] ?? '') ?>"
                                    data-role="<?= htmlspecialchars($u['role']) ?>">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button type="button" class="btn btn-icon btn-light btn-active-light-danger btnDeleteUser"
                                    data-id="<?= htmlspecialchars($u['id']) ?>"
                                    data-username="<?= htmlspecialchars($u['username']) ?>">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Pengguna -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/users">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pengguna Baru</h5>
                    <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Tutup">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="full_name" class="form-control"
                                value="<?= htmlspecialchars($old['full_name'] ?? '') ?>"
                                placeholder="Contoh: Budi Santoso">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" required
                                value="<?= htmlspecialchars($old['username'] ?? '') ?>"
                                placeholder="Contoh: budi123">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required
                                value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                                placeholder="Contoh: budi@email.com">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required
                                placeholder="Minimal 6 karakter">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="" disabled <?= empty($old['role']) ? 'selected' : '' ?>>-- Pilih Role --</option>
                                <option value="admin" <?= ($old['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrator</option>
                                <option value="staff" <?= ($old['role'] ?? '') === 'staff' ? 'selected' : '' ?>>Staff</option>
                                <option value="pimpinan" <?= ($old['role'] ?? '') === 'pimpinan' ? 'selected' : '' ?>>Pimpinan</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Pengguna -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/users/update" id="editUserForm">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Pengguna</h5>
                    <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Tutup">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="full_name" id="edit_full_name" class="form-control"
                                placeholder="Contoh: Budi Santoso">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" id="edit_username" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" id="edit_password" class="form-control"
                                placeholder="Biarkan kosong jika tidak ingin mengubah password">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select name="role" id="edit_role" class="form-select" required>
                                <option value="admin">Administrator</option>
                                <option value="staff">Staff</option>
                                <option value="pimpinan">Pimpinan</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/users/delete" id="deleteUserForm">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="delete_id">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Tutup">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus pengguna <strong id="delete_username">-</strong>?</p>
                    <p class="text-danger small">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.jQuery && jQuery.fn && jQuery.fn.DataTable) {
        jQuery('#users_table').DataTable({
            pageLength: 10,
            ordering: true,
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ entri',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ entri',
                infoEmpty: 'Tidak ada data',
                paginate: {
                    first: 'Pertama',
                    last: 'Terakhir',
                    next: 'Selanjutnya',
                    previous: 'Sebelumnya'
                }
            }
        });
    }

    const editModal = document.getElementById('editUserModal');
    const deleteModal = document.getElementById('deleteUserModal');

    if (editModal) {
        jQuery('#users_table').on('click', '.btnEditUser', function () {
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_username').value = this.dataset.username;
            document.getElementById('edit_email').value = this.dataset.email;
            document.getElementById('edit_full_name').value = this.dataset.name;
            document.getElementById('edit_role').value = this.dataset.role;
            document.getElementById('edit_password').value = '';
            bootstrap.Modal.getOrCreateInstance(editModal).show();
        });
    }

    if (deleteModal) {
        jQuery('#users_table').on('click', '.btnDeleteUser', function () {
            document.getElementById('delete_id').value = this.dataset.id;
            document.getElementById('delete_username').textContent = this.dataset.username;
            bootstrap.Modal.getOrCreateInstance(deleteModal).show();
        });
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

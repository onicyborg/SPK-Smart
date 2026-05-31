<?php
$pageTitle = 'Data Supplier';
require_once __DIR__ . '/../layouts/master.php';

$old = \Core\Session::getFlash('old') ?? [];
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
    <h3 class="fw-bold mb-0">Data Supplier Ban Bandara</h3>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
        <i class="bi bi-plus-lg me-2"></i>Tambah Supplier
    </button>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table id="supplier_table" class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th>Kode</th>
                        <th>Nama Perusahaan</th>
                        <th>PIC</th>
                        <th>Email PIC</th>
                        <th>Telepon PIC</th>
                        <th>Kategori Ban</th>
                        <th>Tanggal Bergabung</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($suppliers as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['supplier_code']) ?></td>
                            <td><?= htmlspecialchars($s['company_name']) ?></td>
                            <td><?= htmlspecialchars($s['pic_name']) ?></td>
                            <td><?= htmlspecialchars($s['pic_email']) ?></td>
                            <td><?= htmlspecialchars($s['pic_phone'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($s['tire_category'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($s['joined_date'] ?? '-') ?></td>
                            <td class="text-end">
                                <button type="button" class="btn btn-icon btn-light btn-active-light-primary btnEditSupplier"
                                    data-id="<?= htmlspecialchars($s['id']) ?>"
                                    data-code="<?= htmlspecialchars($s['supplier_code']) ?>"
                                    data-company="<?= htmlspecialchars($s['company_name']) ?>"
                                    data-pic="<?= htmlspecialchars($s['pic_name']) ?>"
                                    data-email="<?= htmlspecialchars($s['pic_email']) ?>"
                                    data-phone="<?= htmlspecialchars($s['pic_phone'] ?? '') ?>"
                                    data-tax="<?= htmlspecialchars($s['tax_id'] ?? '') ?>"
                                    data-address="<?= htmlspecialchars($s['full_address'] ?? '') ?>"
                                    data-category="<?= htmlspecialchars($s['tire_category'] ?? '') ?>"
                                    data-joined="<?= htmlspecialchars($s['joined_date'] ?? '') ?>">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button type="button" class="btn btn-icon btn-light btn-active-light-danger btnDeleteSupplier"
                                    data-id="<?= htmlspecialchars($s['id']) ?>"
                                    data-name="<?= htmlspecialchars($s['company_name']) ?>">
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

<!-- Modal Tambah Supplier -->
<div class="modal fade" id="addSupplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="/suppliers">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Supplier Baru</h5>
                    <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Tutup">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Kode Supplier <span class="text-danger">*</span></label>
                            <input type="text" name="supplier_code" class="form-control" required
                                value="<?= htmlspecialchars($old['supplier_code'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                            <input type="text" name="company_name" class="form-control" required
                                value="<?= htmlspecialchars($old['company_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama PIC <span class="text-danger">*</span></label>
                            <input type="text" name="pic_name" class="form-control" required
                                value="<?= htmlspecialchars($old['pic_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email PIC <span class="text-danger">*</span></label>
                            <input type="email" name="pic_email" class="form-control" required
                                value="<?= htmlspecialchars($old['pic_email'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telepon PIC</label>
                            <input type="text" name="pic_phone" class="form-control"
                                value="<?= htmlspecialchars($old['pic_phone'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NPWP / Tax ID</label>
                            <input type="text" name="tax_id" class="form-control"
                                value="<?= htmlspecialchars($old['tax_id'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kategori Ban</label>
                            <input type="text" name="tire_category" class="form-control"
                                value="<?= htmlspecialchars($old['tire_category'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Bergabung</label>
                            <input type="date" name="joined_date" class="form-control"
                                value="<?= htmlspecialchars($old['joined_date'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea name="full_address" class="form-control" rows="2"><?= htmlspecialchars($old['full_address'] ?? '') ?></textarea>
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

<!-- Modal Edit Supplier -->
<div class="modal fade" id="editSupplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="/suppliers/update" id="editSupplierForm">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Supplier</h5>
                    <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Tutup">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Kode Supplier <span class="text-danger">*</span></label>
                            <input type="text" name="supplier_code" id="edit_supplier_code" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                            <input type="text" name="company_name" id="edit_company_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama PIC <span class="text-danger">*</span></label>
                            <input type="text" name="pic_name" id="edit_pic_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email PIC <span class="text-danger">*</span></label>
                            <input type="email" name="pic_email" id="edit_pic_email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telepon PIC</label>
                            <input type="text" name="pic_phone" id="edit_pic_phone" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NPWP / Tax ID</label>
                            <input type="text" name="tax_id" id="edit_tax_id" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kategori Ban</label>
                            <input type="text" name="tire_category" id="edit_tire_category" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Bergabung</label>
                            <input type="date" name="joined_date" id="edit_joined_date" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea name="full_address" id="edit_full_address" class="form-control" rows="2"></textarea>
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

<!-- Modal Hapus Supplier -->
<div class="modal fade" id="deleteSupplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/suppliers/delete" id="deleteSupplierForm">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="delete_id">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Tutup">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus supplier <strong id="delete_name">-</strong>?</p>
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
        jQuery('#supplier_table').DataTable({
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

    const editModal = document.getElementById('editSupplierModal');
    const deleteModal = document.getElementById('deleteSupplierModal');

    if (editModal) {
        jQuery('#supplier_table').on('click', '.btnEditSupplier', function () {
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_supplier_code').value = this.dataset.code;
            document.getElementById('edit_company_name').value = this.dataset.company;
            document.getElementById('edit_pic_name').value = this.dataset.pic;
            document.getElementById('edit_pic_email').value = this.dataset.email;
            document.getElementById('edit_pic_phone').value = this.dataset.phone;
            document.getElementById('edit_tax_id').value = this.dataset.tax;
            document.getElementById('edit_full_address').value = this.dataset.address;
            document.getElementById('edit_tire_category').value = this.dataset.category;
            document.getElementById('edit_joined_date').value = this.dataset.joined;
            bootstrap.Modal.getOrCreateInstance(editModal).show();
        });
    }

    if (deleteModal) {
        jQuery('#supplier_table').on('click', '.btnDeleteSupplier', function () {
            document.getElementById('delete_id').value = this.dataset.id;
            document.getElementById('delete_name').textContent = this.dataset.name;
            bootstrap.Modal.getOrCreateInstance(deleteModal).show();
        });
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<?php
$pageTitle = 'Data Kriteria';
require_once __DIR__ . '/../layouts/master.php';

$old = \Core\Session::getFlash('old') ?? [];
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
    <h3 class="fw-bold mb-0">Data Kriteria (SMART)</h3>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCriteriaModal">
        <i class="bi bi-plus-lg me-2"></i>Tambah Kriteria
    </button>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table id="criteria_table" class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th>Kode</th>
                        <th>Nama Kriteria</th>
                        <th>Deskripsi</th>
                        <th>Tipe</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($criteria as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['criteria_code']) ?></td>
                            <td><?= htmlspecialchars($c['criteria_name']) ?></td>
                            <td><?= htmlspecialchars($c['description'] ?? '-') ?></td>
                            <td>
                                <?php if ($c['type'] === 'cost'): ?>
                                    <span class="badge badge-light-danger">Cost</span>
                                <?php else: ?>
                                    <span class="badge badge-light-success">Benefit</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-icon btn-light btn-active-light-primary btnEditCriteria"
                                    data-id="<?= htmlspecialchars($c['id']) ?>"
                                    data-code="<?= htmlspecialchars($c['criteria_code']) ?>"
                                    data-name="<?= htmlspecialchars($c['criteria_name']) ?>"
                                    data-desc="<?= htmlspecialchars($c['description'] ?? '') ?>"
                                    data-type="<?= htmlspecialchars($c['type']) ?>">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button type="button" class="btn btn-icon btn-light btn-active-light-danger btnDeleteCriteria"
                                    data-id="<?= htmlspecialchars($c['id']) ?>"
                                    data-name="<?= htmlspecialchars($c['criteria_name']) ?>">
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

<!-- Modal Tambah Kriteria -->
<div class="modal fade" id="addCriteriaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/criteria">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kriteria Baru</h5>
                    <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Tutup">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Kode Kriteria <span class="text-danger">*</span></label>
                            <input type="text" name="criteria_code" class="form-control" required
                                value="<?= htmlspecialchars($old['criteria_code'] ?? '') ?>"
                                placeholder="Contoh: C01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Kriteria <span class="text-danger">*</span></label>
                            <input type="text" name="criteria_name" class="form-control" required
                                value="<?= htmlspecialchars($old['criteria_name'] ?? '') ?>"
                                placeholder="Contoh: Harga">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="2"
                                placeholder="Penjelasan singkat tentang kriteria ini"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Tipe Kriteria <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="" disabled <?= empty($old['type']) ? 'selected' : '' ?>>-- Pilih Tipe --</option>
                                <option value="cost" <?= ($old['type'] ?? '') === 'cost' ? 'selected' : '' ?>>Cost (Semakin kecil semakin baik)</option>
                                <option value="benefit" <?= ($old['type'] ?? '') === 'benefit' ? 'selected' : '' ?>>Benefit (Semakin besar semakin baik)</option>
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

<!-- Modal Edit Kriteria -->
<div class="modal fade" id="editCriteriaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/criteria/update" id="editCriteriaForm">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Kriteria</h5>
                    <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Tutup">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Kode Kriteria <span class="text-danger">*</span></label>
                            <input type="text" name="criteria_code" id="edit_criteria_code" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Kriteria <span class="text-danger">*</span></label>
                            <input type="text" name="criteria_name" id="edit_criteria_name" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Tipe Kriteria <span class="text-danger">*</span></label>
                            <select name="type" id="edit_type" class="form-select" required>
                                <option value="cost">Cost (Semakin kecil semakin baik)</option>
                                <option value="benefit">Benefit (Semakin besar semakin baik)</option>
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

<!-- Modal Hapus Kriteria -->
<div class="modal fade" id="deleteCriteriaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/criteria/delete" id="deleteCriteriaForm">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="delete_id">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Tutup">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus kriteria <strong id="delete_name">-</strong>?</p>
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
        jQuery('#criteria_table').DataTable({
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

    const editModal = document.getElementById('editCriteriaModal');
    const deleteModal = document.getElementById('deleteCriteriaModal');

    if (editModal) {
        jQuery('#criteria_table').on('click', '.btnEditCriteria', function () {
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_criteria_code').value = this.dataset.code;
            document.getElementById('edit_criteria_name').value = this.dataset.name;
            document.getElementById('edit_description').value = this.dataset.desc;
            document.getElementById('edit_type').value = this.dataset.type;
            bootstrap.Modal.getOrCreateInstance(editModal).show();
        });
    }

    if (deleteModal) {
        jQuery('#criteria_table').on('click', '.btnDeleteCriteria', function () {
            document.getElementById('delete_id').value = this.dataset.id;
            document.getElementById('delete_name').textContent = this.dataset.name;
            bootstrap.Modal.getOrCreateInstance(deleteModal).show();
        });
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

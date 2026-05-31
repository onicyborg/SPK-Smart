<?php
$pageTitle = 'Sesi Penilaian';
require_once __DIR__ . '/../layouts/master.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
    <h3 class="fw-bold mb-0">Daftar Sesi Penilaian</h3>
    <a href="/sessions/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Buat Sesi Baru
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table id="sessions_table" class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th>Kode</th>
                        <th>Nama Sesi</th>
                        <th>Periode</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sessions as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['session_code']) ?></td>
                            <td>
                                <div class="fw-semibold"><?= htmlspecialchars($s['title']) ?></div>
                                <?php if (!empty($s['description'])): ?>
                                    <div class="text-muted small"><?= htmlspecialchars($s['description']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= isset($s['start_date']) ? date('d M Y', strtotime($s['start_date'])) : '-' ?>
                                &mdash;
                                <?= isset($s['end_date']) ? date('d M Y', strtotime($s['end_date'])) : '-' ?>
                            </td>
                            <td>
                                <?php if ($s['status'] === 'draft'): ?>
                                    <span class="badge badge-light-warning">Draft</span>
                                <?php elseif ($s['status'] === 'in_progress'): ?>
                                    <span class="badge badge-light-primary">Berlangsung</span>
                                <?php else: ?>
                                    <span class="badge badge-light-success">Selesai</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="/sessions/show?id=<?= htmlspecialchars($s['id']) ?>" class="btn btn-icon btn-light btn-active-light-primary" title="Input Nilai">
                                    <i class="bi bi-input-cursor-text"></i>
                                </a>
                                <?php if ($s['status'] !== 'draft'): ?>
                                    <a href="/sessions/result?id=<?= htmlspecialchars($s['id']) ?>" class="btn btn-icon btn-light btn-active-light-success" title="Lihat Hasil">
                                        <i class="bi bi-bar-chart-line-fill"></i>
                                    </a>
                                <?php endif; ?>
                                <button type="button" class="btn btn-icon btn-light btn-active-light-danger btnDeleteSession"
                                    data-id="<?= htmlspecialchars($s['id']) ?>"
                                    data-title="<?= htmlspecialchars($s['title']) ?>">
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

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteSessionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/sessions/delete" id="deleteSessionForm">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="delete_session_id">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Tutup">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus sesi <strong id="delete_session_title">-</strong>?</p>
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
        jQuery('#sessions_table').DataTable({
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

    jQuery('#sessions_table').on('click', '.btnDeleteSession', function () {
        document.getElementById('delete_session_id').value = this.dataset.id;
        document.getElementById('delete_session_title').textContent = this.dataset.title;
        bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteSessionModal')).show();
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

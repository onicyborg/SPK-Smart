<?php
$pageTitle = 'Input Nilai Evaluasi';
require_once __DIR__ . '/../layouts/master.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
    <div>
        <h3 class="fw-bold mb-0">Input Nilai Evaluasi</h3>
        <span class="text-muted">Sesi: <?= htmlspecialchars($session['title'] ?? '-') ?></span>
    </div>
    <a href="/sessions" class="btn btn-light">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<?php $flashSuccess = \Core\Session::getFlash('success'); ?>
<?php if ($flashSuccess): ?>
    <script>
        (function () {
            var msg = <?= json_encode($flashSuccess) ?>;
            if (window.toastr && toastr.success) { toastr.success(msg); }
            else { console.log('SUCCESS:', msg); }
        })();
    </script>
<?php endif; ?>

<?php $flashError = \Core\Session::getFlash('error'); ?>
<?php if ($flashError): ?>
    <script>
        (function () {
            var msg = <?= json_encode($flashError) ?>;
            if (window.toastr && toastr.error) { toastr.error(msg); }
            else { console.log('ERROR:', msg); }
        })();
    </script>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-header d-flex flex-column">
        <h5 class="card-title mb-1">Matriks Keputusan</h5>
        <span class="text-muted fs-7">Isi nilai mentah untuk setiap supplier pada setiap kriteria.</span>
    </div>
    <div class="card-body">
        <form method="POST" action="/sessions/save-matrix" id="matrixForm">
            <?= csrf_field() ?>
            <input type="hidden" name="session_id" value="<?= htmlspecialchars($session['id']) ?>">

            <div class="table-responsive">
                <table class="table table-bordered align-middle table-row-dashed fs-6 gy-2">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0 bg-light">
                            <th class="min-w-120px">Supplier</th>
                            <?php foreach ($criteria as $c): ?>
                                <th class="min-w-100px text-center">
                                    <?= htmlspecialchars($c['criteria_code']) ?>
                                    <div class="text-muted small fw-normal"><?= htmlspecialchars($c['criteria_name']) ?></div>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($suppliers as $s): ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?= htmlspecialchars($s['company_name']) ?></div>
                                    <div class="text-muted small"><?= htmlspecialchars($s['supplier_code']) ?></div>
                                </td>
                                <?php foreach ($criteria as $c): ?>
                                    <td>
                                        <input type="number" step="0.01"
                                            name="matrix[<?= htmlspecialchars($s['supplier_id']) ?>][<?= htmlspecialchars($c['criteria_id']) ?>]"
                                            class="form-control form-control-sm text-center w-100px mx-auto"
                                            value="<?= htmlspecialchars($matrix[$s['supplier_id']][$c['criteria_id']] ?? '') ?>"
                                            placeholder="0.00">
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between mt-5 pt-5 border-top">
                <a href="/sessions" class="btn btn-light btn-active-light-primary">
                    <i class="bi bi-arrow-left me-2"></i>Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    Simpan & Hitung SMART<i class="bi bi-calculator ms-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

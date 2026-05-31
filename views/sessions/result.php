<?php
$pageTitle = 'Hasil Perhitungan SMART';
require_once __DIR__ . '/../layouts/master.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
    <div>
        <h3 class="fw-bold mb-0">Hasil Perhitungan SMART</h3>
        <span class="text-muted">Sesi: <?= htmlspecialchars($session['title'] ?? '-') ?></span>
    </div>
    <div class="d-flex gap-2">
        <?php if ($session['status'] !== 'completed'): ?>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#completeSessionModal">
                <i class="bi bi-check-circle me-2"></i>Selesaikan Sesi
            </button>
        <?php endif; ?>
        <a href="/sessions/show?id=<?= htmlspecialchars($session['id']) ?>" class="btn btn-light">
            <i class="bi bi-pencil-square me-2"></i>Ubah Nilai
        </a>
        <a href="/sessions" class="btn btn-light btn-active-light-primary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>
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

<!-- Step 1: Matriks Nilai Mentah -->
<div class="card card-flush mb-5 shadow-sm">
    <div class="card-header">
        <div class="card-title flex-column align-items-start">
            <h3 class="card-title"><span class="badge badge-light-primary me-2">1</span>Matriks Nilai Mentah</h3>
            <span class="text-muted fs-7 fw-semibold">Nilai asli yang diinput untuk setiap supplier dan kriteria.</span>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle fs-6 gy-2">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0 bg-light">
                        <th class="min-w-150px">Supplier</th>
                        <?php foreach ($criteria as $c): ?>
                            <th class="text-center">
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
                            </td>
                            <?php foreach ($criteria as $c): ?>
                                <td class="text-center">
                                    <?= number_format($rawMatrix[$s['supplier_id']][$c['criteria_id']], 2) ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Step 2: Normalisasi Bobot & Parameter Min/Max -->
<div class="card card-flush mb-5 shadow-sm">
    <div class="card-header">
        <div class="card-title flex-column align-items-start">
            <h3 class="card-title"><span class="badge badge-light-primary me-2">2</span>Normalisasi Bobot &amp; Parameter Min/Max</h3>
            <span class="text-muted fs-7 fw-semibold">Bobot dinormalisasi (w / &Sigma;w) dan nilai ekstrem tiap kriteria.</span>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle fs-6 gy-2">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0 bg-light">
                        <th>Kode</th>
                        <th>Nama Kriteria</th>
                        <th class="text-center">Tipe</th>
                        <th class="text-center">Bobot Awal</th>
                        <th class="text-center">Bobot Normalisasi</th>
                        <th class="text-center">Min</th>
                        <th class="text-center">Max</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($criteria as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['criteria_code']) ?></td>
                            <td><?= htmlspecialchars($c['criteria_name']) ?></td>
                            <td class="text-center">
                                <?php if ($c['type'] === 'cost'): ?>
                                    <span class="badge badge-light-danger">Cost</span>
                                <?php else: ?>
                                    <span class="badge badge-light-success">Benefit</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?= number_format((float) $c['weight'], 2) ?></td>
                            <td class="text-center fw-semibold"><?= number_format($normalizedWeights[$c['criteria_id']], 4) ?></td>
                            <td class="text-center"><?= number_format($minMax[$c['criteria_id']]['min'], 2) ?></td>
                            <td class="text-center"><?= number_format($minMax[$c['criteria_id']]['max'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-3 p-3 bg-light rounded border border-dashed">
            <strong>Rumus Normalisasi Bobot:</strong> <code>w<sub>j</sub><sup>norm</sup> = w<sub>j</sub> / &Sigma;w</code>
        </div>
    </div>
</div>

<!-- Step 3: Matriks Utility -->
<div class="card card-flush mb-5 shadow-sm">
    <div class="card-header">
        <div class="card-title flex-column align-items-start">
            <h3 class="card-title"><span class="badge badge-light-primary me-2">3</span>Matriks Utility</h3>
            <span class="text-muted fs-7 fw-semibold">Konversi nilai mentah ke skala 0&ndash;1 berdasarkan tipe kriteria.</span>
        </div>
    </div>
    <div class="card-body">
        <div class="mb-3 p-3 bg-light rounded border border-dashed">
            <strong>Rumus Benefit:</strong> <code>(x &minus; min) / (max &minus; min)</code> &nbsp;&middot;&nbsp;
            <strong>Rumus Cost:</strong> <code>(max &minus; x) / (max &minus; min)</code>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle fs-6 gy-2">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0 bg-light">
                        <th class="min-w-150px">Supplier</th>
                        <?php foreach ($criteria as $c): ?>
                            <th class="text-center">
                                <?= htmlspecialchars($c['criteria_code']) ?>
                                <div class="text-muted small fw-normal"><?= htmlspecialchars($c['type']) ?></div>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($suppliers as $s): ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?= htmlspecialchars($s['company_name']) ?></div>
                            </td>
                            <?php foreach ($criteria as $c): ?>
                                <td class="text-center">
                                    <?= number_format($utilityMatrix[$s['supplier_id']][$c['criteria_id']], 4) ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Step 4: Hasil Akhir & Peringkat -->
<div class="card card-flush mb-5 shadow-sm">
    <div class="card-header">
        <div class="card-title flex-column align-items-start">
            <h3 class="card-title"><span class="badge badge-light-primary me-2">4</span>Hasil Akhir &amp; Peringkat</h3>
            <span class="text-muted fs-7 fw-semibold">Total skor dihitung dari &Sigma;(Bobot Normalisasi &times; Utility).</span>
        </div>
    </div>
    <div class="card-body">
        <div class="mb-3 p-3 bg-light rounded border border-dashed">
            <strong>Rumus:</strong> <code>U<sub>i</sub> = &Sigma; (w<sub>j</sub><sup>norm</sup> &times; u<sub>ij</sub>)</code>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle fs-6 gy-2">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0 bg-light">
                        <th>Peringkat</th>
                        <th>Supplier</th>
                        <th class="text-center">Total Skor</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($finalScores as $sid => $score): ?>
                        <?php
                        $supplierName = '';
                        foreach ($suppliers as $s) {
                            if ($s['supplier_id'] === $sid) {
                                $supplierName = $s['company_name'];
                                break;
                            }
                        }
                        $rank = $rankings[$sid];
                        $isTop = $rank === 1;
                        ?>
                        <tr>
                            <td class="text-center">
                                <?php if ($isTop): ?>
                                    <span class="badge badge-light-success fs-6 px-3 py-2">#<?= $rank ?></span>
                                <?php else: ?>
                                    <span class="badge badge-light fs-6 px-3 py-2">#<?= $rank ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-semibold"><?= htmlspecialchars($supplierName) ?></div>
                            </td>
                            <td class="text-center fw-bold"><?= number_format($score, 4) ?></td>
                            <td class="text-center">
                                <?php if ($isTop): ?>
                                    <span class="badge badge-success">Rekomendasi Terbaik</span>
                                <?php else: ?>
                                    <span class="badge badge-light">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Selesaikan Sesi -->
<div class="modal fade" id="completeSessionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="/sessions/complete">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($session['id']) ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Penyelesaian</h5>
                    <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Tutup">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menyelesaikan sesi ini? Setelah diselesaikan, status akan berubah menjadi <strong>Selesai</strong> dan Anda tidak dapat lagi mengubah nilai evaluasi.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-2"></i>Ya, Selesaikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

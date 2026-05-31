<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../layouts/master.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
    <h3 class="fw-bold mb-0">Ringkasan Eksekutif</h3>
</div>

<!-- Baris 1: Widget Statistik -->
<div class="row g-5 g-xl-8 mb-5 mb-xl-8">
    <div class="col-sm-6 col-xl-3">
        <div class="card card-flush h-100 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="d-flex flex-column flex-grow-1">
                    <span class="text-muted fw-semibold fs-7 text-uppercase">Total Supplier</span>
                    <span class="fw-bold fs-2x text-gray-800"><?= $totalSuppliers ?></span>
                </div>
                <div class="rounded-circle bg-light-primary p-4">
                    <i class="bi bi-truck fs-1 text-primary"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-flush h-100 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="d-flex flex-column flex-grow-1">
                    <span class="text-muted fw-semibold fs-7 text-uppercase">Total Kriteria</span>
                    <span class="fw-bold fs-2x text-gray-800"><?= $totalCriteria ?></span>
                </div>
                <div class="rounded-circle bg-light-success p-4">
                    <i class="bi bi-list-check fs-1 text-success"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-flush h-100 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="d-flex flex-column flex-grow-1">
                    <span class="text-muted fw-semibold fs-7 text-uppercase">Total Sesi</span>
                    <span class="fw-bold fs-2x text-gray-800"><?= $totalSessions ?></span>
                </div>
                <div class="rounded-circle bg-light-warning p-4">
                    <i class="bi bi-calculator fs-1 text-warning"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-flush h-100 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="d-flex flex-column flex-grow-1">
                    <span class="text-muted fw-semibold fs-7 text-uppercase">Sesi Selesai</span>
                    <span class="fw-bold fs-2x text-gray-800"><?= $completedSessions ?></span>
                </div>
                <div class="rounded-circle bg-light-info p-4">
                    <i class="bi bi-check-circle fs-1 text-info"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Baris 2: Tabel & List -->
<div class="row g-5 g-xl-8">
    <!-- Kolom Kiri: Sesi Evaluasi Terbaru -->
    <div class="col-xl-8">
        <div class="card h-100 shadow-sm">
            <div class="card-header">
                <h3 class="card-title">Sesi Evaluasi Terbaru</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-3">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th>Nama Sesi</th>
                                <th>Periode</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentSessions)): ?>
                                <tr>
                                    <td colspan="3" class="text-muted text-center py-5">Belum ada sesi evaluasi.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentSessions as $s): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold"><?= htmlspecialchars($s['title']) ?></div>
                                            <div class="text-muted small"><?= htmlspecialchars($s['session_code']) ?></div>
                                        </td>
                                        <td>
                                            <?= isset($s['start_date']) ? date('d M Y', strtotime($s['start_date'])) : '-' ?>
                                            &mdash;
                                            <?= isset($s['end_date']) ? date('d M Y', strtotime($s['end_date'])) : '-' ?>
                                        </td>
                                        <td>
                                            <?php if ($s['status'] === 'draft'): ?>
                                                <span class="badge badge-light">Draft</span>
                                            <?php elseif ($s['status'] === 'in_progress'): ?>
                                                <span class="badge badge-light-primary">Berlangsung</span>
                                            <?php else: ?>
                                                <span class="badge badge-light-success">Selesai</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Supplier Baru -->
    <div class="col-xl-4">
        <div class="card h-100 shadow-sm">
            <div class="card-header">
                <h3 class="card-title">Supplier Baru</h3>
            </div>
            <div class="card-body">
                <?php if (empty($recentSuppliers)): ?>
                    <p class="text-muted text-center py-5">Belum ada data supplier.</p>
                <?php else: ?>
                    <?php foreach ($recentSuppliers as $sup): ?>
                        <div class="d-flex align-items-center mb-7">
                            <div class="symbol symbol-40px bg-light-primary rounded me-4">
                                <span class="symbol-label fs-5 fw-bold text-primary"><?= strtoupper(substr($sup['company_name'] ?? '?', 0, 1)) ?></span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold"><?= htmlspecialchars($sup['company_name']) ?></div>
                                <div class="text-muted fs-7"><?= htmlspecialchars($sup['supplier_code'] ?? '-') ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
<?php
$pageTitle = 'Buat Sesi Penilaian';
$errors = \Core\Session::getFlash('errors') ?? [];
$old = \Core\Session::getFlash('old') ?? [];
require_once __DIR__ . '/../layouts/master.php';
?>

<style>
    #session_stepper,
    #session_stepper .stepper-content,
    #session_stepper .stepper-body,
    #session_stepper .stepper-body.current,
    #session_stepper .stepper-body > .card {
        width: 100% !important;
    }
    #session_stepper .stepper-content > .stepper-body {
        display: none;
    }
    #session_stepper .stepper-content > .stepper-body.current {
        display: block;
    }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
    <div>
        <h3 class="fw-bold mb-0">Buat Sesi Penilaian Baru</h3>
        <span class="text-muted">Ikuti langkah-langkah di bawah ini untuk membuat sesi evaluasi supplier.</span>
    </div>
    <a href="/sessions" class="btn btn-light">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0 ps-3">
            <?php foreach ($errors as $msg): ?>
                <li><?= htmlspecialchars($msg) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form method="POST" action="/sessions" id="sessionWizardForm">
    <?= csrf_field() ?>

    <div class="card w-100 shadow-sm">
        <div class="card-body">
            <div class="stepper stepper-pills" id="session_stepper">

                <!-- Stepper Nav: tetap di tengah -->
                <div class="stepper-nav flex-center flex-wrap mb-5">
                    <div class="stepper-item mx-8 my-4 current" data-kt-stepper-element="nav">
                        <div class="stepper-wrapper d-flex align-items-center">
                            <div class="stepper-icon w-40px h-40px">
                                <i class="stepper-check fas fa-check fs-2"></i>
                                <span class="stepper-number">1</span>
                            </div>
                            <div class="stepper-label ps-3">
                                <h6 class="stepper-title">Info Sesi</h6>
                                <div class="stepper-desc">Nama dan periode</div>
                            </div>
                        </div>
                        <div class="stepper-line h-40px"></div>
                    </div>

                    <div class="stepper-item mx-8 my-4" data-kt-stepper-element="nav">
                        <div class="stepper-wrapper d-flex align-items-center">
                            <div class="stepper-icon w-40px h-40px">
                                <i class="stepper-check fas fa-check fs-2"></i>
                                <span class="stepper-number">2</span>
                            </div>
                            <div class="stepper-label ps-3">
                                <h6 class="stepper-title">Kriteria</h6>
                                <div class="stepper-desc">Pilih dan tentukan bobot</div>
                            </div>
                        </div>
                        <div class="stepper-line h-40px"></div>
                    </div>

                    <div class="stepper-item mx-8 my-4" data-kt-stepper-element="nav">
                        <div class="stepper-wrapper d-flex align-items-center">
                            <div class="stepper-icon w-40px h-40px">
                                <i class="stepper-check fas fa-check fs-2"></i>
                                <span class="stepper-number">3</span>
                            </div>
                            <div class="stepper-label ps-3">
                                <h6 class="stepper-title">Supplier</h6>
                                <div class="stepper-desc">Pilih supplier evaluasi</div>
                            </div>
                        </div>
                        <div class="stepper-line h-40px"></div>
                    </div>

                    <div class="stepper-item mx-8 my-4" data-kt-stepper-element="nav">
                        <div class="stepper-wrapper d-flex align-items-center">
                            <div class="stepper-icon w-40px h-40px">
                                <i class="stepper-check fas fa-check fs-2"></i>
                                <span class="stepper-number">4</span>
                            </div>
                            <div class="stepper-label ps-3">
                                <h6 class="stepper-title">Konfirmasi</h6>
                                <div class="stepper-desc">Simpan sesi</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stepper Content: lebar penuh tanpa pembatas kolom -->
                <div class="stepper-content">

                    <!-- Step 1: Info Sesi -->
                    <div class="stepper-body current" data-kt-stepper-element="content">
                        <div class="card w-100 shadow-sm">
                            <div class="card-body">
                                <div class="row g-5">
                                    <div class="col-12">
                                        <label class="form-label">Nama Sesi <span class="text-danger">*</span></label>
                                        <input type="text" name="title" class="form-control" required
                                            value="<?= htmlspecialchars($old['title'] ?? '') ?>"
                                            placeholder="Contoh: Evaluasi Supplier Q3 2025">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Keterangan</label>
                                        <textarea name="description" rows="3" class="form-control"
                                            placeholder="Deskripsi singkat mengenai tujuan sesi ini"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                        <input type="date" name="start_date" class="form-control" required
                                            value="<?= htmlspecialchars($old['start_date'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                        <input type="date" name="end_date" class="form-control" required
                                            value="<?= htmlspecialchars($old['end_date'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Pilih Kriteria -->
                    <div class="stepper-body" data-kt-stepper-element="content">
                        <div class="card w-100 shadow-sm">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle table-row-dashed fs-6 gy-2">
                                        <thead>
                                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                                <th style="width: 40px;">
                                                    <div class="form-check form-check-custom form-check-solid">
                                                        <input class="form-check-input" type="checkbox" id="checkAllCriteria">
                                                    </div>
                                                </th>
                                                <th>Kode</th>
                                                <th>Nama Kriteria</th>
                                                <th>Tipe</th>
                                                <th style="width: 110px;">Bobot (%)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($criteria as $c): ?>
                                                <tr>
                                                    <td>
                                                        <div class="form-check form-check-custom form-check-solid">
                                                            <input class="form-check-input criteria-checkbox" type="checkbox"
                                                                name="criteria_ids[]" value="<?= htmlspecialchars($c['id']) ?>"
                                                                id="crit_<?= htmlspecialchars($c['id']) ?>"
                                                                <?= in_array($c['id'], $old['criteria_ids'] ?? []) ? 'checked' : '' ?>>
                                                        </div>
                                                    </td>
                                                    <td><?= htmlspecialchars($c['criteria_code']) ?></td>
                                                    <td>
                                                        <label for="crit_<?= htmlspecialchars($c['id']) ?>" class="fw-semibold cursor-pointer">
                                                            <?= htmlspecialchars($c['criteria_name']) ?>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <?php if ($c['type'] === 'cost'): ?>
                                                            <span class="badge badge-light-danger">Cost</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-light-success">Benefit</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01" min="0" max="100"
                                                            name="weights[<?= htmlspecialchars($c['id']) ?>]"
                                                            class="form-control form-control-sm w-100px"
                                                            value="<?= htmlspecialchars($old['weights'][$c['id']] ?? '') ?>"
                                                            placeholder="0.00">
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div id="criteriaError" class="text-danger small mt-2 d-none">Total bobot kriteria harus sama dengan 100%.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Pilih Supplier -->
                    <div class="stepper-body" data-kt-stepper-element="content">
                        <div class="card w-100 shadow-sm">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle table-row-dashed fs-6 gy-2">
                                        <thead>
                                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                                <th style="width: 40px;">
                                                    <div class="form-check form-check-custom form-check-solid">
                                                        <input class="form-check-input" type="checkbox" id="checkAllSuppliers">
                                                    </div>
                                                </th>
                                                <th>Kode</th>
                                                <th>Nama Perusahaan</th>
                                                <th>PIC</th>
                                                <th>Kategori</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($suppliers as $s): ?>
                                                <tr>
                                                    <td>
                                                        <div class="form-check form-check-custom form-check-solid">
                                                            <input class="form-check-input supplier-checkbox" type="checkbox"
                                                                name="supplier_ids[]" value="<?= htmlspecialchars($s['id']) ?>"
                                                                id="sup_<?= htmlspecialchars($s['id']) ?>"
                                                                <?= in_array($s['id'], $old['supplier_ids'] ?? []) ? 'checked' : '' ?>>
                                                        </div>
                                                    </td>
                                                    <td><?= htmlspecialchars($s['supplier_code']) ?></td>
                                                    <td>
                                                        <label for="sup_<?= htmlspecialchars($s['id']) ?>" class="fw-semibold cursor-pointer">
                                                            <?= htmlspecialchars($s['company_name']) ?>
                                                        </label>
                                                    </td>
                                                    <td><?= htmlspecialchars($s['pic_name']) ?></td>
                                                    <td><?= htmlspecialchars($s['tire_category'] ?? '-') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Konfirmasi -->
                    <div class="stepper-body" data-kt-stepper-element="content">
                        <div class="card w-100 shadow-sm">
                            <div class="card-body">
                                <div class="alert alert-dismissible bg-light-primary border border-primary d-flex flex-column flex-sm-row p-5 mb-10">
                                    <div class="d-flex flex-column">
                                        <h4 class="mb-1 text-primary">Konfirmasi Pembuatan Sesi</h4>
                                        <span>Semua data telah disiapkan. Silakan pastikan kembali pilihan Anda. Klik tombol 'Simpan Sesi' di bawah untuk membuat sesi penilaian baru. <strong>Perhatian:</strong> Kriteria dan Supplier yang telah dipilih akan dikunci secara permanen untuk sesi ini.</span>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                                <h5 class="fw-bold mb-4">Ringkasan Sesi</h5>
                                <div class="row g-5">
                                    <div class="col-md-4">
                                        <h6 class="text-muted text-uppercase fs-7 fw-bold">Informasi Sesi</h6>
                                        <table class="table table-sm table-borderless w-auto">
                                            <tr><td class="text-muted">Nama</td><td class="fw-semibold" id="confirmTitle">-</td></tr>
                                            <tr><td class="text-muted">Periode</td><td class="fw-semibold" id="confirmPeriod">-</td></tr>
                                        </table>
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="text-muted text-uppercase fs-7 fw-bold">Kriteria Terpilih</h6>
                                        <ul id="confirmCriteria" class="list-unstyled mb-0"></ul>
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="text-muted text-uppercase fs-7 fw-bold">Supplier Terpilih</h6>
                                        <ul id="confirmSuppliers" class="list-unstyled mb-0"></ul>
                                    </div>
                                </div>
                                <div class="alert alert-warning mt-5">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    Setelah sesi disimpan, kriteria dan supplier yang dipilih akan <strong>terkunci</strong> dan tidak dapat diubah.
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="d-flex justify-content-between mt-10 pt-5 border-top">
                    <button type="button" class="btn btn-light btn-active-light-primary" data-kt-stepper-action="previous">
                        <i class="bi bi-arrow-left me-2"></i>Sebelumnya
                    </button>
                    <div>
                        <button type="button" class="btn btn-primary" data-kt-stepper-action="next">
                            Selanjutnya<i class="bi bi-arrow-right ms-2"></i>
                        </button>
                        <button type="submit" class="btn btn-primary d-none" data-kt-stepper-action="submit">
                            <span class="indicator-label">Simpan Sesi <i class="bi bi-save ms-2 fs-4"></i></span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const stepperEl = document.getElementById('session_stepper');
    if (!stepperEl) return;

    const items    = stepperEl.querySelectorAll('[data-kt-stepper-element="nav"]');
    const contents = stepperEl.querySelectorAll('[data-kt-stepper-element="content"]');
    const btnNext   = stepperEl.querySelector('[data-kt-stepper-action="next"]');
    const btnPrev   = stepperEl.querySelector('[data-kt-stepper-action="previous"]');
    const btnSubmit = stepperEl.querySelector('[data-kt-stepper-action="submit"]');
    let currentStep = 0;

    function showStep(index) {
        items.forEach((el, i) => {
            el.classList.toggle('current', i === index);
            el.classList.toggle('completed', i < index);
        });
        contents.forEach((el, i) => {
            el.classList.toggle('current', i === index);
        });
        currentStep = index;

        // Kontrol tombol Sebelumnya
        btnPrev.style.display = index === 0 ? 'none' : 'inline-block';

        // Kontrol tombol Selanjutnya vs Simpan Sesi
        if (index === items.length - 1) {
            btnNext.style.display   = 'none';
            btnSubmit.style.display = 'inline-block';
            // Hapus d-none jika masih ada dari state awal
            btnSubmit.classList.remove('d-none');
            refreshConfirmation();
        } else {
            btnNext.style.display   = 'inline-block';
            btnSubmit.style.display = 'none';
        }
    }

    function showValidationError(message) {
        document.getElementById('validationErrorMessage').textContent = message;
        bootstrap.Modal.getOrCreateInstance(document.getElementById('validationErrorModal')).show();
    }

    function validateStep(index) {
        if (index === 0) {
            const title = document.querySelector('input[name="title"]').value.trim();
            const start = document.querySelector('input[name="start_date"]').value;
            const end   = document.querySelector('input[name="end_date"]').value;
            if (!title)        { showValidationError('Nama sesi wajib diisi.'); return false; }
            if (!start || !end){ showValidationError('Tanggal mulai dan selesai wajib diisi.'); return false; }
        }
        if (index === 1) {
            const checked = document.querySelectorAll('.criteria-checkbox:checked');
            if (checked.length === 0) { showValidationError('Minimal pilih satu kriteria.'); return false; }
            let total = 0;
            checked.forEach(cb => {
                const w = document.querySelector('input[name="weights[' + cb.value + ']"]');
                total += parseFloat(w?.value || 0);
            });
            const err = document.getElementById('criteriaError');
            if (Math.abs(total - 100) > 0.01) {
                err.classList.remove('d-none');
                return false;
            }
            err.classList.add('d-none');
        }
        if (index === 2) {
            const checked = document.querySelectorAll('.supplier-checkbox:checked');
            if (checked.length === 0) { showValidationError('Minimal pilih satu supplier.'); return false; }
        }
        return true;
    }

    function refreshConfirmation() {
        const title = document.querySelector('input[name="title"]').value;
        const start = document.querySelector('input[name="start_date"]').value;
        const end   = document.querySelector('input[name="end_date"]').value;
        document.getElementById('confirmTitle').textContent  = title || '-';
        document.getElementById('confirmPeriod').textContent = (start && end) ? (start + ' s/d ' + end) : '-';

        const critList = document.getElementById('confirmCriteria');
        critList.innerHTML = '';
        document.querySelectorAll('.criteria-checkbox:checked').forEach(cb => {
            const row  = cb.closest('tr');
            const name = row.querySelector('td:nth-child(3)').textContent.trim();
            const w    = document.querySelector('input[name="weights[' + cb.value + ']"]').value;
            const li   = document.createElement('li');
            li.textContent = name + ' (Bobot: ' + w + '%)';
            critList.appendChild(li);
        });

        const supList = document.getElementById('confirmSuppliers');
        supList.innerHTML = '';
        document.querySelectorAll('.supplier-checkbox:checked').forEach(cb => {
            const row  = cb.closest('tr');
            const name = row.querySelector('td:nth-child(3)').textContent.trim();
            const li   = document.createElement('li');
            li.textContent = name;
            supList.appendChild(li);
        });
    }

    btnNext.addEventListener('click', function () {
        if (validateStep(currentStep) && currentStep < items.length - 1) {
            showStep(currentStep + 1);
        }
    });

    btnPrev.addEventListener('click', function () {
        if (currentStep > 0) showStep(currentStep - 1);
    });

    const checkAllCriteria = document.getElementById('checkAllCriteria');
    if (checkAllCriteria) {
        checkAllCriteria.addEventListener('change', function () {
            document.querySelectorAll('.criteria-checkbox').forEach(cb => cb.checked = this.checked);
        });
    }

    const checkAllSuppliers = document.getElementById('checkAllSuppliers');
    if (checkAllSuppliers) {
        checkAllSuppliers.addEventListener('change', function () {
            document.querySelectorAll('.supplier-checkbox').forEach(cb => cb.checked = this.checked);
        });
    }

    // Inisialisasi state awal tombol secara eksplisit sebelum showStep
    btnSubmit.style.display = 'none';
    btnSubmit.classList.remove('d-none');
    btnPrev.style.display = 'none';

    showStep(0);
});
</script>

<!-- Modal Validasi Error -->
<div class="modal fade" id="validationErrorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Validasi Gagal</h5>
                <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Tutup">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body">
                <p id="validationErrorMessage" class="mb-0">-</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Oke</button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
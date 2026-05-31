<?php
$pageTitle = 'Profil Saya';
$errors = \Core\Session::getFlash('errors') ?? [];
$old = \Core\Session::getFlash('old') ?? [];
require_once __DIR__ . '/../layouts/master.php';

$fallbackAvatar = $_ENV['ASSET_URL'] . '/assets/media/avatars/blank.png';
$avatarUrl = !empty($user['avatar'])
    ? '/uploads/avatars/' . $user['avatar']
    : $fallbackAvatar;
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
    <div>
        <h3 class="fw-bold mb-0">Profil Pengguna</h3>
        <span class="text-muted">Perbarui informasi dasar, foto, dan password akun Anda.</span>
    </div>
</div>

<?php $successMessage = \Core\Session::getFlash('success'); ?>
<?php if ($successMessage): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($successMessage) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

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

<div class="row g-5">
    <div class="col-lg-4">
        <div class="card card-flush h-100 shadow-sm">
            <div class="card-body text-center">
                <div class="image-input image-input-outline" data-kt-image-input="true"
                    style="background-image: url('<?= htmlspecialchars($fallbackAvatar) ?>')">
                    <div class="image-input-wrapper w-150px h-150px"
                        style="background-image: url('<?= htmlspecialchars($avatarUrl) ?>');"></div>
                    <label class="btn btn-icon btn-circle btn-active-color-primary w-35px h-35px bg-body shadow"
                        data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Ganti foto">
                        <i class="bi bi-pencil-fill fs-7"></i>
                        <input type="file" name="avatar" accept=".png, .jpg, .jpeg, .webp" form="profile_form" />
                        <input type="hidden" name="avatar_remove" />
                    </label>
                    <span class="btn btn-icon btn-circle btn-active-color-primary w-35px h-35px bg-body shadow"
                        data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Batalkan">
                        <i class="bi bi-x fs-2"></i>
                    </span>
                </div>
                <h4 class="fw-bold mt-4 mb-1"><?= htmlspecialchars($user['full_name'] ?? $user['username'] ?? '') ?></h4>
                <div class="text-muted mb-4 text-uppercase"><?= htmlspecialchars($user['role'] ?? '-') ?></div>
                <div class="border-top pt-4 text-start">
                    <div class="mb-3">
                        <span class="text-muted d-block">Email</span>
                        <span class="fw-semibold"><?= htmlspecialchars($user['email'] ?? '-') ?></span>
                    </div>
                    <div>
                        <span class="text-muted d-block">Terakhir diperbarui</span>
                        <span class="fw-semibold"><?= isset($user['updated_at']) ? date('d M Y H:i', strtotime($user['updated_at'])) : '-' ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card card-flush h-100 shadow-sm">
            <div class="card-header">
                <div class="card-title">
                    <h4 class="fw-bold mb-0">Informasi Akun</h4>
                </div>
            </div>
            <div class="card-body">
                <form id="profile_form" method="POST" action="/profile/update" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="row g-5">
                        <div class="col-12">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="full_name" value="<?= htmlspecialchars($old['full_name'] ?? $user['full_name'] ?? '') ?>" class="form-control" placeholder="Contoh: Budi Santoso">
                        </div>
                    </div>

                    <div class="row g-5 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" value="<?= htmlspecialchars($old['username'] ?? $user['username'] ?? '') ?>" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? $user['email'] ?? '') ?>" class="form-control">
                        </div>
                    </div>

                    <div class="row g-5 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-5">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
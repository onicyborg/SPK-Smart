<?php
$role       = \Core\Session::get('role') ?? 'staff'; // Default fallback
$currentUri = $_SERVER['REQUEST_URI'] ?? '';
?>

<div class="app-sidebar-menu overflow-hidden flex-column-fluid">
    <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
        <div id="kt_app_sidebar_menu_scroll" class="scroll-y my-5 mx-3" data-kt-scroll="true" data-kt-scroll-activate="true"
            data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
            data-kt-scroll-save-state="true">

            <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6" id="#kt_app_sidebar_menu"
                data-kt-menu="true" data-kt-menu-expand="false">

                <div class="menu-item">
                    <a class="menu-link <?= str_starts_with($currentUri, '/dashboard') ? 'active' : '' ?>" href="/dashboard">
                        <span class="menu-icon"><i class="bi bi-speedometer2 fs-2"></i></span>
                        <span class="menu-title">Dashboard</span>
                    </a>
                </div>

                <?php if (in_array($role, ['admin', 'staff'])): ?>
                    <div class="menu-content">
                        <div class="separator my-3"></div>
                        <span class="menu-heading text-uppercase text-muted fs-8">Master Data</span>
                    </div>

                    <div class="menu-item">
                        <a class="menu-link <?= str_starts_with($currentUri, '/suppliers') ? 'active' : '' ?>" href="/suppliers">
                            <span class="menu-icon"><i class="bi bi-truck fs-2"></i></span>
                            <span class="menu-title">Supplier Ban</span>
                        </a>
                    </div>

                    <div class="menu-item">
                        <a class="menu-link <?= str_starts_with($currentUri, '/criteria') ? 'active' : '' ?>" href="/criteria">
                            <span class="menu-icon"><i class="bi bi-list-check fs-2"></i></span>
                            <span class="menu-title">Kriteria (SMART)</span>
                        </a>
                    </div>
                <?php endif; ?>

                <?php if ($role === 'admin'): ?>
                    <div class="menu-item">
                        <a class="menu-link <?= str_starts_with($currentUri, '/users') ? 'active' : '' ?>" href="/users">
                            <span class="menu-icon"><i class="bi bi-people fs-2"></i></span>
                            <span class="menu-title">Manajemen User</span>
                        </a>
                    </div>
                <?php endif; ?>

                <div class="menu-content">
                    <div class="separator my-3"></div>
                    <span class="menu-heading text-uppercase text-muted fs-8">Evaluasi SPK</span>
                </div>

                <div class="menu-item">
                    <a class="menu-link <?= str_starts_with($currentUri, '/sessions') ? 'active' : '' ?>" href="/sessions">
                        <span class="menu-icon"><i class="bi bi-calculator fs-2"></i></span>
                        <span class="menu-title">Sesi Penilaian</span>
                    </a>
                </div>

                <div class="menu-content">
                    <div class="separator my-3"></div>
                    <span class="menu-heading text-uppercase text-muted fs-8">Pengaturan</span>
                </div>

                <div class="menu-item">
                    <a class="menu-link <?= str_starts_with($currentUri, '/profile') ? 'active' : '' ?>" href="/profile">
                        <span class="menu-icon"><i class="bi bi-person-badge fs-2"></i></span>
                        <span class="menu-title">Profil Saya</span>
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
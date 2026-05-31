<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <?php if (isset($pageTitle)): ?>
        <title>ProTrack - <?= htmlspecialchars($pageTitle) ?></title>
    <?php else: ?>
        <title>ProTrack - Project Tracking System</title>
    <?php endif; ?>

    <link rel="shortcut icon" href="<?= $_ENV['ASSET_URL'] ?>/assets/media/logos/favicon.ico" />

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

    <!-- Vendor Stylesheets (optional, bisa dipakai untuk datatables, calendar dll) -->
    <link href="<?= $_ENV['ASSET_URL'] ?>/assets/plugins/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
    <link href="<?= $_ENV['ASSET_URL'] ?>/assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />

    <!-- Global Stylesheets Bundle -->
    <link href="<?= $_ENV['ASSET_URL'] ?>/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="<?= $_ENV['ASSET_URL'] ?>/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <style>
        [data-bs-theme="light"] #kt_app_sidebar_logo .sidebar-title { color: #fff !important; }
        html, body { overflow-x: hidden; }
        .select2-container { width: 100%; }
        .select2-container--open { z-index: 1060; }
        .select2-dropdown { z-index: 1060; }
    </style>

    <?= $styles ?? '' ?>
</head>

<body id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true"
    data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true"
    data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true"
    data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="false" class="app-default">

    <script>
        var defaultThemeMode = "light";
        var themeMode;

        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else {
                if (localStorage.getItem("data-bs-theme") !== null) {
                    themeMode = localStorage.getItem("data-bs-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }

            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }

            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>

    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">

            <div id="kt_app_header" class="app-header bg-body border-bottom shadow-sm" data-kt-sticky="true"
                data-kt-sticky-activate="{default: true, lg: true}" data-kt-sticky-name="app-header-minimize"
                data-kt-sticky-offset="{default: '200px', lg: '0'}" data-kt-sticky-animation="false">
                <div class="app-container container-fluid d-flex align-items-stretch justify-content-between"
                    id="kt_app_header_container">

                    <div class="d-flex align-items-center d-lg-none ms-n3 me-1 me-md-2" title="Tampilkan menu">
                        <div class="btn btn-icon btn-active-color-primary w-35px h-35px" id="kt_app_sidebar_mobile_toggle">
                            <i class="bi bi-list fs-1"></i>
                        </div>
                    </div>

                    <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
                        <?php
                            $homeUrl = match (\Core\Session::get('role')) {
                                'admin' => '/admin/dashboard',
                                'pm'    => '/pm/dashboard',
                                default => '/login',
                            };
                        ?>
                        <a href="<?= htmlspecialchars($homeUrl) ?>" class="d-flex align-items-center">
                            <img alt="Logo" src="<?= $_ENV['ASSET_URL'] ?>/assets/media/logos/default-small.svg"
                                class="h-30px" />
                            <span class="ms-3 fw-bold fs-4 d-none d-sm-inline text-gray-800">
                                ProTrack
                            </span>
                        </a>
                    </div>

                    <div class="d-flex align-items-center flex-shrink-0">
                        <div class="me-3">
                            <button type="button" id="theme_toggle" class="btn btn-icon btn-light btn-active-light-primary w-35px h-35px" aria-label="Ubah tema">
                                <i id="theme_toggle_icon" class="bi"></i>
                            </button>
                        </div>
                        <?php if (\Core\Session::get('user_id')): ?>
                            <?php
                                $authName = \Core\Session::get('full_name') ?: (\Core\Session::get('username') ?: 'User');
                                $authRole = ucfirst(\Core\Session::get('role') ?? '-');
                            ?>
                            <div class="dropdown">
                                <button class="btn btn-light d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="symbol symbol-35px symbol-circle me-2">
                                        <span class="symbol-label bg-primary text-white fw-bold">
                                            <?= strtoupper(substr($authName, 0, 1)) ?>
                                        </span>
                                    </div>
                                    <div class="d-none d-md-flex flex-column text-start me-2">
                                        <span class="fw-bold text-gray-800 lh-1"><?= htmlspecialchars($authName) ?></span>
                                        <span class="text-muted fs-8 lh-1"><?= htmlspecialchars($authRole) ?></span>
                                    </div>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                    <li class="px-3 py-2">
                                        <div class="fw-bold"><?= htmlspecialchars($authName) ?></div>
                                        <div class="text-muted small"><?= htmlspecialchars($authRole) ?></div>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <div class="px-3 py-2">
                                            <a href="/profile/edit" class="btn btn-sm btn-primary w-100">
                                                <i class="bi bi-person-gear me-2"></i>Manage Account
                                            </a>
                                        </div>
                                    </li>
                                    <li>
                                        <form action="/logout" method="GET" class="px-3 py-2">
                                            <button type="submit" class="btn btn-sm btn-light w-100">
                                                <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

            <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
                <div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true"
                    data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}"
                    data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start"
                    data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">

                    <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
                        <a href="<?= htmlspecialchars($homeUrl) ?>" class="d-flex align-items-center">
                            <img alt="Logo" src="<?= $_ENV['ASSET_URL'] ?>/assets/media/logos/default-small.svg"
                                class="h-25px app-sidebar-logo-default" />
                            <span class="ms-3 fw-semibold text-gray-800 fs-6 sidebar-title">ProTrack</span>
                        </a>
                    </div>

                    <?php require_once __DIR__ . '/sidebar.php'; ?>
                </div>

                <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                    <div class="d-flex flex-column flex-column-fluid">
                        <div id="kt_app_content" class="app-content flex-column-fluid">
                            <div class="app-container container-fluid py-5">
                                
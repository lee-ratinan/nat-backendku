<?php $session = session(); $slug = ''; ?>
<!DOCTYPE html>
<?php /*
  HOW TO USE: 
  data-layout: fluid (default), boxed
  data-sidebar-theme: dark (default), colored, light
  data-sidebar-position: left (default), right
  data-sidebar-behavior: sticky (default), fixed, compact
*/ ?>
<html lang="en" data-bs-theme="dark" data-layout="fluid" data-sidebar-theme="dark" data-sidebar-position="left" data-sidebar-behavior="sticky">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Responsive Bootstrap 5 Admin &amp; Dashboard Template">
    <meta name="author" content="Bootlab">
    <title><?= lang('Errors.pageNotFound') ?> | <?= $session->app_name ?></title>
    <link href="<?= base_url('file/favicon.jpg') ?>" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+Thai&family=Oxanium:wght@700&family=Poppins:ital@0;1&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/DataTables/datatables.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/flag-icon/css/flag-icon.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/toastrjs/toastr.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('appstack/css/app.css') ?>" rel="stylesheet" />
    <style>
        h1,h2,h3,h4,h5,h6,th{font-family:"Oxanium","Noto Serif Thai",sans-serif;} .alert{padding:1rem;}  svg:not(:host).svg-inline--fa, svg:not(:root).svg-inline--fa {overflow: visible;box-sizing: content-box;margin: auto 0.25rem;}
        .sidebar-header .avatar-img, .sidebar-header .avatar-txt {width: 3rem !important;height: 3rem !important;}
        table.dataTable tbody tr>.dtfc-fixed-start, table.dataTable tbody tr>.dtfc-fixed-end {background-color:#202634;}
        table.dataTable tbody tr:nth-of-type(odd)>.dtfc-fixed-start, table.dataTable tbody tr:nth-of-type(odd)>.dtfc-fixed-end {background-color:#323c4c!important;}
        table.dataTable thead tr>.dtfc-fixed-start, table.dataTable thead tr>.dtfc-fixed-end, table.dataTable tfoot tr>.dtfc-fixed-start, table.dataTable tfoot tr>.dtfc-fixed-end {background-color:#292f43;}
        .bg-red {background-color: #740001 !important;} .bg-gold {background-color: #D3A625 !important;color:#000!important;} .bg-yellow {background-color: #FFD800 !important;color:#000!important;} .bg-black {background-color: #000000 !important;} .bg-blue {background-color: #0E1A40 !important;} .bg-bronze {background-color: #946B2D !important;} .bg-green {background-color: #1A472A !important;} .bg-silver {background-color: #5D5D5D !important;}
    </style>
</head>
<body>
<div class="wrapper">
    <nav id="sidebar" class="sidebar">
        <div class="sidebar-content js-simplebar">
            <!-- SIDEBAR NAVIGATION -->
            <ul class="sidebar-nav">
                <li class="sidebar-header p-0">
                    <?php
                    switch ($session->current_role) {
                        case 'journey':
                            echo '<img class="img-fluid" src="' . base_url('appstack/sidebar_journey.jpg') . '" class="rounded" alt="Journey">';
                            break;
                        case 'finance':
                            echo '<img class="img-fluid" src="' . base_url('appstack/sidebar_finance.jpg') . '" class="rounded" alt="Finance">';
                            break;
                        default:
                            echo '<img class="img-fluid" src="' . base_url('appstack/sidebar_main.jpg') . '" class="rounded" alt="Header">';
                            break;
                    }
                    ?>
                </li>
                <li class="sidebar-header my-3">
                    <div class="float-start me-3"><?= $session->avatar ?></div>
                    <h6><?= $session->display_name ?></h6>
                    <span><?= $session->user['employee_title'] ?></span>
                </li>
                <li class="sidebar-item <?= ('dashboard' == $slug ? 'active' : '' ) ?>"><a class="sidebar-link" href="<?= base_url($session->locale . '/office/dashboard') ?>"><i class="fa-solid fa-house-chimney fa-fw me-3"></i><span><?= lang('System.dashboard.page_title') ?></span></a></li>
            </ul>
        </div>
    </nav>
    <div class="main">
        <!-- HEADER NAV -->
        <nav class="navbar navbar-expand navbar-bg">
            <a class="sidebar-toggle"><i class="hamburger align-self-center"></i></a>
            <!-- SEARCH BAR -->
            <form class="d-none d-sm-inline-block">
                <div class="input-group input-group-navbar">
                    <input type="text" class="form-control" placeholder="Search projects…" aria-label="Search">
                    <button class="btn" type="button">
                        <i class="align-middle" data-lucide="search"></i>
                    </button>
                </div>
            </form>
            <div class="navbar-collapse collapse">
                <ul class="navbar-nav navbar-align">
                    <li class="nav-item dropdown d-none">
                        <a class="nav-icon dropdown-toggle" href="#" id="messagesDropdown" data-bs-toggle="dropdown">
                            <div class="position-relative">
                                <i class="align-middle text-body" data-lucide="message-circle"></i>
                                <span class="indicator">4</span>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end py-0" aria-labelledby="messagesDropdown">
                            <div class="dropdown-menu-header">
                                <div class="position-relative">
                                    4 New Messages
                                </div>
                            </div>
                            <div class="list-group">
                                <a href="#" class="list-group-item">
                                    <div class="row g-0 align-items-center">
                                        <div class="col-2">
                                            <img src="#" class="img-fluid rounded-circle" alt="Ashley Briggs" width="40" height="40">
                                        </div>
                                        <div class="col-10 ps-2">
                                            <div>Ashley Briggs</div>
                                            <div class="text-muted small mt-1">Nam pretium turpis et arcu. Duis arcu tortor.</div>
                                            <div class="text-muted small mt-1">15m ago</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="dropdown-menu-footer">
                                <a href="#" class="text-muted">Show all messages</a>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item dropdown d-none">
                        <a class="nav-icon dropdown-toggle" href="#" id="alertsDropdown" data-bs-toggle="dropdown">
                            <div class="position-relative">
                                <i class="align-middle text-body" data-lucide="bell-off"></i>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end py-0" aria-labelledby="alertsDropdown">
                            <div class="dropdown-menu-header">
                                4 New Notifications
                            </div>
                            <div class="list-group">
                                <a href="#" class="list-group-item">
                                    <div class="row g-0 align-items-center">
                                        <div class="col-2">
                                            <i class="text-danger" data-lucide="alert-circle"></i>
                                        </div>
                                        <div class="col-10">
                                            <div>Update completed</div>
                                            <div class="text-muted small mt-1">Restart server 12 to complete the update.</div>
                                            <div class="text-muted small mt-1">2h ago</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="dropdown-menu-footer">
                                <a href="#" class="text-muted">Show all notifications</a>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item nav-theme-toggle dropdown d-none">
                        <a class="nav-icon js-theme-toggle" href="#">
                            <div class="position-relative">
                                <i class="align-middle text-body nav-theme-toggle-light" data-lucide="sun"></i>
                                <i class="align-middle text-body nav-theme-toggle-dark" data-lucide="moon"></i>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-flag dropdown-toggle" href="#" id="languageDropdown" data-bs-toggle="dropdown"><i class="flag-icon flag-icon-us small"></i></a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                            <a class="dropdown-item" href="<?= base_url('en/office/dashboard') ?>">
                                <i class="flag-icon flag-icon-us"></i>
                                <span class="align-middle">English (US)</span>
                            </a>
                            <a class="dropdown-item" href="<?= base_url('th/office/dashboard') ?>">
                                <i class="flag-icon flag-icon-th"></i>
                                <span class="align-middle">ภาษาไทย</span>
                            </a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
                            <i class="align-middle" data-lucide="settings"></i>
                        </a>
                        <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
                            <?= $session->avatar ?>
                            <span><?= $session->display_name ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="<?= base_url($session->locale . '/office/profile') ?>"><i class="fa-solid fa-user-cog fa-fw me-3"></i><span><?= lang('System.menu.my_profile') ?></span></a>
                            <a class="dropdown-item" href="<?= base_url($session->locale . '/office/switch-role') ?>"><i class="fa-solid fa-arrows-rotate fa-fw me-3"></i><span><?= lang('System.menu.switch_role') ?></span></a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?= base_url('logout') ?>">Sign out</a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
        <main class="content">
            <div class="container-fluid p-0">
                <h1 class="text-danger display-1"><i class="fa-solid fa-skull"></i> 404</h1>
                <p><?= lang('Errors.sorryCannotFind') ?></p>
            </div>
        </main>
        <footer class="footer">
            <div class="container-fluid">
                <div class="row text-muted">
                    <div class="col-6 text-start">
                        <ul class="list-inline">
                            <li class="list-inline-item"><a class="text-muted" href="<?= base_url($session->locale . '/office/profile') ?>">Profile</a></li>
                            <li class="list-inline-item"><a class="text-muted" href="<?= base_url($session->locale . '/office/switch-role') ?>">Switch Role</a></li>
                            <li class="list-inline-item"><a class="text-muted" href="<?= base_url('logout') ?>">Sign out</a></li>
                        </ul>
                    </div>
                    <div class="col-6 text-end">
                        <p class="mb-0">
                            &copy; <?= date('Y') . ' ' . $session->organization['organization_name'] ?>
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>
<script src="<?= base_url('appstack/js/app.js') ?>"></script>
<script src="<?= base_url('assets/vendor/tinymce/tinymce.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/toastrjs/toastr.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/Luxon/luxon.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/DataTables/datatables.min.js') ?>"></script>
</body>
</html>
<?php $session = session(); ?>
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
    <title><?= $page_title ?> | <?= $session->app_name ?></title>
    <link href="<?= base_url('file/favicon.jpg') ?>" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+Thai&family=Oxanium:wght@700&family=Poppins:ital@0;1&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/DataTables/datatables.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/flag-icon/css/flag-icon.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/toastrjs/toastr.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('appstack/css/app.css') ?>" rel="stylesheet" />
    <script src="<?= base_url('assets/vendor/tinymce7.8.0/js/tinymce/tinymce.min.js') ?>" defer></script>
</head>
<body>
<div class="wrapper">
    <nav id="sidebar" class="sidebar">
        <div class="sidebar-content js-simplebar">
            <!-- SIDEBAR NAVIGATION -->
            <ul class="sidebar-nav pb-5 mb-5">
                <li class="sidebar-header p-0">
                    <?php
                    if (!empty($slug_group)) {
                        echo match ($slug_group) {
                            'trip' => '<img class="img-fluid" src="' . base_url('appstack/sidebar_journey.jpg') . '" class="rounded" alt="Journey">',
                            'employment', 'tax' => '<img class="img-fluid" src="' . base_url('appstack/sidebar_finance.jpg') . '" class="rounded" alt="Finance">',
                            default => '<img class="img-fluid" src="' . base_url('appstack/sidebar_main.jpg') . '" class="rounded" alt="Header">',
                        };
                    } else {
                        echo '<img class="img-fluid" src="' . base_url('appstack/sidebar_main.jpg') . '" class="rounded" alt="Header">';
                    }
                    ?>
                </li>
                <li class="sidebar-header my-3">
                    <div class="float-start me-3"><?= $session->avatar ?></div>
                    <h6><?= $session->display_name ?></h6>
                    <span><?= $session->user['employee_title'] ?></span>
                </li>
                <li class="sidebar-header my-3">
                    <h6><i class="fa-solid fa-quote-left"></i> <?= get_daily_affirmation() ?></h6>
                </li>
                <?php
                $navigation = [
                    [
                        'header' => null,
                        'items'  => [
                            '/office/dashboard' => '<i class="fa-solid fa-house-chimney fa-fw"></i> Dashboard',
                        ]
                    ],
                    [
                        'header' => 'Finance',
                        'menu'   => [
                            [
                                'id'    => 'employment',
                                'group' => '<i class="fa-solid fa-suitcase fa-fw"></i> Employment',
                                'items' => [
                                    '/office/employment'                        => 'Company',
                                    '/office/employment/company/stats'          => '<i class="fa-solid fa-fw fa-chart-bar"></i> Statistics',
                                    '/office/employment/company/total-income'   => '<i class="fa-solid fa-fw fa-chart-line"></i> Total Income',
                                    '/office/employment/salary'                 => 'Salary',
                                    '/office/employment/salary/stats/currency/' => '<i class="fa-solid fa-fw fa-chart-line"></i> By Currency',
                                    '/office/employment/salary/stats/company/'  => '<i class="fa-solid fa-fw fa-chart-line"></i> By Company',
                                    '/office/employment/freelance'              => 'Freelance',
                                    '/office/employment/freelance/stats'        => '<i class="fa-solid fa-fw fa-chart-bar"></i> Statistics',
                                    '/office/employment/freelance-client'       => '<i class="fa-solid fa-fw fa-users"></i> Freelance Client',
                                    '/office/employment/freelance-income'       => '<i class="fa-solid fa-fw fa-dollar-sign"></i> Freelance Income',
                                    '/office/employment/freelance-income/stats' => '<i class="fa-solid fa-fw fa-chart-line"></i> Statistics',
                                    '/office/employment/part-time'              => 'Part-Time Job',
                                    '/office/employment/part-time/pay-period'   => '<i class="fa-solid fa-fw fa-calendar-check"></i> Part-Time Pay Period',
                                    '/office/employment/part-time/stats'        => '<i class="fa-solid fa-fw fa-chart-bar"></i> Statistics',
                                    '/office/employment/cpf'                    => 'CPF',
                                    '/office/employment/cpf/contribution'       => '<i class="fa-solid fa-fw fa-chart-bar"></i> CPF Contribution',
                                    '/office/employment/cpf/investment'         => '<i class="fa-solid fa-fw fa-chart-bar"></i> CPF Investment',
                                    '/office/employment/cpf/now'                => '<i class="fa-solid fa-fw fa-chart-pie"></i> CPF Now',
                                    '/office/employment/cpf/growth'             => '<i class="fa-solid fa-fw fa-chart-line"></i> CPF Growth',
                                    '/office/employment/cpf/stats'              => '<i class="fa-solid fa-fw fa-chart-bar"></i> CPF Statistics',
                                ]
                            ],
                            [
                                'id'    => 'tax',
                                'group' => '<i class="fa-solid fa-building-columns fa-fw"></i> Tax',
                                'items' => [
                                    '/office/tax'            => 'Tax',
                                    '/office/tax/statistics' => 'Tax Statistics',
                                    '/office/tax/calculator' => 'Tax Calculator',
                                    '/office/tax/projection' => 'Tax Projection',
                                    '/office/tax/comparison' => 'Tax Comparison',
                                ]
                            ],
                            [
                                'id'    =>'document',
                                'group' => '<i class="fa-solid fa-folder-open fa-fw"></i> Document',
                                'items' => [
                                    '/office/document' => 'Document',
                                ]
                            ],
                            [
                                'id'    =>'investment',
                                'group' => '<i class="fa-solid fa-chart-line fa-fw"></i> Investment',
                                'items' => [
                                    '/office/investment' => 'Investment',
                                ]
                            ]
                        ]
                    ],
                    [
                        'header' => 'Journey',
                        'menu' => [
                            [
                                'id'    => 'trip',
                                'group' => '<i class="fa-solid fa-plane-departure fa-fw"></i> Trip',
                                'items' => [
                                    '/office/journey/trip'                         => 'Trip',
                                    '/office/journey/trip/statistics'              => '<i class="fa-solid fa-chart-line fa-fw"></i> Statistics',
                                    '/office/journey/transport'                    => 'Transportation',
                                    '/office/journey/transport/statistics'         => '<i class="fa-solid fa-chart-line fa-fw"></i> Statistics',
                                    '/office/journey/accommodation'                => 'Accommodation',
                                    '/office/journey/accommodation/statistics'     => '<i class="fa-solid fa-chart-line fa-fw"></i> Statistics',
                                    '/office/journey/attraction'                   => 'Attraction',
                                    '/office/journey/attraction/statistics'        => '<i class="fa-solid fa-chart-line fa-fw"></i> Statistics',
                                    '/office/journey/bucket-list'                  => 'Bucket List',
                                    '/office/journey/bucket-list/statistics'       => '<i class="fa-solid fa-chart-bar fa-fw"></i> Statistics',
                                    '/office/journey/holiday'                      => 'Holiday',
                                    '/office/journey/map'                          => 'Map',
                                    '/office/journey/trip/finance'                 => 'Finance',
                                ]
                            ],
                            [
                                'id'    => 'trip-support-data',
                                'group' => '<i class="fa-solid fa-file fa-fw"></i> Supported Data',
                                'items' => [
                                    '/office/journey/port'                         => 'Port',
                                    '/office/journey/port/statistics'              => '<i class="fa-solid fa-chart-line fa-fw"></i> Port',
                                    '/office/journey/operator'                     => 'Operator',
                                    '/office/journey/operator/statistics'          => '<i class="fa-solid fa-chart-line fa-fw"></i> Operator',
                                    '/office/journey/operator/aircraft/statistics' => '<i class="fa-solid fa-chart-line fa-fw"></i> Aircraft',
                                ]
                            ]
                        ]
                    ],
//                    [
//                        'header' => 'Fiction',
//                        'menu'   => [
//                            [
//                                'id'    => 'fiction',
//                                'group' => '<i class="fa-solid fa-book fa-fw"></i> Fiction',
//                                'items' => [
//                                    '/office/fiction'            => 'Fiction',
//                                ]
//                            ]
//                        ]
//                    ],
                    [
                        'header' => 'Health',
                        'menu'   => [
                            [
                                'id'    => 'health',
                                'group' => '<i class="fa-solid fa-dumbbell fa-fw"></i> Health',
                                'items' => [
                                    '/office/health/measurement' => 'Measurement',
                                    '/office/health/activity'    => 'Activity',
                                    '/office/health/vaccine'     => 'Vaccine',
                                    '/office/health/affirmation' => 'Affirmation',
                                ]
                            ],
                            [
                                'id'    => 'health-forms',
                                'group' => '<i class="fa-solid fa-file fa-fw"></i> Forms',
                                'items' => [
                                    '/office/health/mbti'            => 'MBTI (Personality)',
                                    '/office/health/phq9'            => 'PHQ-9 (Depression)',
                                    '/office/health/ooca'            => 'OOCA (Counselling)',
                                    '/office/health/ooca/statistics' => '<i class="fa-solid fa-chart-bar"></i> OOCA Statistics',
                                ]
                            ],
                        ]
                    ],
                    [
                        'header' => '𐑖𐑱𐑝𐑾𐑯',
                        'menu'   => [
                            [
                                'id' => 'shavian',
                                'group' => '<i class="fa-solid fa-language fa-fw"></i> 𐑖𐑱𐑝𐑾𐑯',
                                'items' => [
                                    '/office/shavian'          => '𐑖𐑱𐑝𐑾𐑯',
                                    '/office/shavian-ipa'      => '𐑖𐑱𐑝𐑾𐑯-IPA',
                                    '/office/shavian-keyboard' => '𐑖𐑱𐑝𐑾𐑯 𐑒𐑰𐑚𐑹𐑛'
                                ]
                            ]
                        ]
                    ],
                    [
                        'header' => 'Profile',
                        'items'  => [
                            '/office/profile/data'   => '<i class="fa-solid fa-user-cog fa-fw"></i> Profile',
                            '/office/profile/resume' => '<i class="fa-regular fa-file-lines fa-fw"></i> Resume',
                        ]
                    ],
                    [
                        'header' => 'System',
                        'menu'   => [
                            [
                                'id'    => 'user',
                                'group' => '<i class="fa-solid fa-user fa-fw"></i> User/Role',
                                'items' => [
                                    '/office/user'         => 'User',
                                    '/office/role'         => 'Role',
                                    '/office/organization' => 'Organization',
                                ]
                            ],
                            [
                                'id'    => 'log',
                                'group' => '<i class="fa-solid fa-list fa-fw"></i> Log',
                                'items' => [
                                    '/office/log'          => 'Log',
                                    '/office/log/email'    => 'Email',
                                    '/office/log/log-file' => 'File List',
                                ]
                            ]
                        ]
                    ]
                ];
                foreach ($navigation as $group) {
                    if (!empty($group['header'])) {
                        echo '<li class="sidebar-header">' . $group['header'] . '</li>';
                    }
                    if (!empty($group['menu'])) {
                        foreach ($group['menu'] as $menu) {
                            $active    = ($menu['id'] == @$slug_group ? 'active' : '');
                            $collapsed = ($menu['id'] == @$slug_group ? '' : 'collapsed');
                            $collapse  = ($menu['id'] == @$slug_group ? '' : 'collapse');
                            echo '<li class="sidebar-item ' . $active . '"><a data-bs-target="#sidebar-group-' . $menu['id'] . '" data-bs-toggle="collapse" class="sidebar-link ' . $collapsed . '" aria-expanded="false"><span>' . $menu['group'] . '</span></a>';
                            echo '<ul id="sidebar-group-' . $menu['id'] . '" class="sidebar-dropdown list-unstyled ' . $collapse . '" data-bs-parent="#sidebar" style="">';
                            foreach ($menu['items'] as $url => $label) {
                                echo '<li class="sidebar-item ' . ($slug == $url ? 'active' : '') . '"><a class="sidebar-link" href="' . base_url($session->locale . $url) . '">' . $label . '</a></li>';
                            }
                            echo '</ul></li>';
                        }
                    }
                    if (!empty($group['items'])) {
                        foreach ($group['items'] as $url => $label) {
                            echo '<li class="sidebar-item sidebar-l1 ' . ($slug == $url ? 'active' : '') . '"><a class="sidebar-link" href="' . base_url($session->locale . $url) . '">' . $label . '</a></li>';
                        }
                    }
                }
                ?>
            </ul>
            <!-- SIDEBAR DOWNLOAD BTN -->
            <div class="sidebar-cta d-none">
                <div class="sidebar-cta-content">
                    <strong class="d-inline-block mb-2">Monthly Sales Report</strong>
                    <div class="mb-3 text-sm">
                        Your monthly sales report is ready for download!
                    </div>
                    <div class="d-grid">
                        <a href="#" class="btn btn-primary" target="_blank">Download</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <div class="main">
        <!-- HEADER NAV -->
        <nav class="navbar navbar-expand navbar-bg d-print-none">
            <a class="sidebar-toggle"><i class="hamburger align-self-center"></i></a>
            <!-- SEARCH BAR -->
            <form class="d-none">
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
                    <li class="nav-item dropdown">
                        <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
                            <i class="align-middle" data-lucide="settings"></i>
                        </a>
                        <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
                            <?= $session->avatar ?>
                            <span><?= $session->display_name ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="<?= base_url($session->locale . '/office/profile') ?>"><i class="fa-solid fa-user-cog fa-fw"></i><span><?= lang('System.menu.my_profile') ?></span></a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?= base_url('logout') ?>"><?= lang('System.menu.log_out') ?></a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
        <main class="content">
            <div class="container-fluid p-0">
                <?= $this->renderSection('content') ?>
            </div>
        </main>
        <footer class="footer">
            <div class="container-fluid">
                <div class="row text-muted">
                    <div class="col-md-6">
                        <ul class="list-inline">
                            <li class="list-inline-item">[ <a class="text-muted" href="<?= base_url($session->locale . '/office/profile') ?>">Profile</a> ]</li>
                            <li class="list-inline-item">[ <a class="text-muted" href="<?= base_url('logout') ?>">Sign out</a> ]</li>
                        </ul>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="mb-0">
                            &copy; <?= date('Y') . ' ' . $session->organization['organization_name'] ?>
                            <span id="webgl-support"></span> <span id="logout-countdown"></span> <span id="tiny-mce-logout-skip"></span>
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>
<script src="<?= base_url('appstack/js/app.js') ?>"></script>
<script src="<?= base_url('assets/vendor/toastrjs/toastr.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/Luxon/luxon.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/DataTables/datatables.min.js') ?>"></script>
<script>
    <?php if (isset($skip_logout) && $skip_logout) : ?>
    $('#tiny-mce-logout-skip').text('- NO LOGOUT SCRIPT');
    <?php else : ?>
    let remainingTime = 1800;
    function formatTime(seconds) {const m = String(Math.floor(seconds / 60)).padStart(2, '0');const s = String(seconds % 60).padStart(2, '0');return `LOGOUT IN ${m}:${s}`;}
    function updateCountdown() {
        if (remainingTime >= 0) {$('#logout-countdown').text(formatTime(remainingTime));remainingTime--;}
        else {clearInterval(countdownInterval);window.location.href = '<?= base_url('logout') ?>';}
    } updateCountdown(); const countdownInterval = setInterval(updateCountdown, 1000);
    <?php endif; ?>
    let gl_support = !!window.WebGLRenderingContext && !!document.createElement('canvas').getContext('webgl');
    if (!gl_support) { $('#webgl-support').html('WebGL is not supported!'); } else { $('#webgl-support').html('WebGL is supported!'); }
    let expandTinyMceArea = function (div_id) {$('#'+div_id+'-block').addClass('full-page');$('#'+div_id+'-expand-btn').hide();$('#'+div_id+'-shrink-btn').show();}
    let shrinkTinyMceArea = function (div_id) {$('#'+div_id+'-block').removeClass('full-page');$('#'+div_id+'-shrink-btn').hide();$('#'+div_id+'-expand-btn').show();}
    let DateTime = luxon.DateTime;
    function utcToLocal(utc) {if ('' !== utc) {return DateTime.fromISO(utc).toLocaleString(DateTime.DATETIME_MED);} else {return '-';}}
    $('.avatar-img').on('click', function() {$(this).toggleClass('expanded');});
    $('.copy-to-field').click(function () {let target_id = $(this).data('target-id'), str_to_copy = $(this).data('str-to-copy');$('#'+target_id).val(str_to_copy);});
</script>
</body>
</html>
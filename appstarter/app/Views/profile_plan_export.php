<?php $session = session(); ?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light" data-layout="fluid" data-sidebar-theme="dark" data-sidebar-position="left" data-sidebar-behavior="sticky">
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
    <script src="<?= base_url('assets/vendor/tinymce7.8.0/js/tinymce/tinymce.min.js') ?>" defer></script>
    <style>
        h1,h2,h3,h4,h5,h6,th{font-family:"Oxanium","Noto Serif Thai",sans-serif;}
        @media print {  .page-break-after {page-break-after: always;}  }
    </style>
</head>
<body>
<div class="container">
    <section class="section">
        <div class="row mt-5">
            <div class="col">
                <?php if (0 != $year) : ?>
                    <?php include 'profile_plan_' . $year . '.php'; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>
</body>
</html>
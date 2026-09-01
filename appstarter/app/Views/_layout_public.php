<?php $session = session(); ?>
<!DOCTYPE html>
<?php /*
  HOW TO USE:
  data-layout: fluid (default), boxed
  data-sidebar-theme: dark (default), colored, light
  data-sidebar-position: left (default), right
  data-sidebar-behavior: sticky (default), fixed, compact
*/ ?>
<html lang="en" data-bs-theme="light" data-layout="fluid" data-sidebar-theme="dark" data-sidebar-position="left" data-sidebar-behavior="sticky">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title><?= $page_title ?> | <?= $session->app_name ?></title>
    <link rel="shortcut icon" href="<?= base_url('file/favicon.jpg') ?>" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+Thai&family=Oxanium:wght@700&family=Poppins:ital@0;1&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/toastrjs/toastr.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('appstack/css/app.css') ?>" rel="stylesheet" />
    <style>h1,h2,h3,h4,h5,h6{font-family:"Oxanium",sans-serif;} .alert{padding:1rem;}  svg:not(:host).svg-inline--fa, svg:not(:root).svg-inline--fa {overflow: visible;box-sizing: content-box;margin: auto 0.25rem;} .lock-screen {position:fixed;width:100%;height:100%;top:0;left:0;background-color:rgba(200,200,200,0.75);z-index:9999;display:none;text-align:center;padding-top:100px;}</style>
</head>
<body>
<div class="container-fluid p-0">
    <div class="row g-0">
        <div class="col-xl-6 d-none d-xl-flex">
            <div class="auth-full-page position-relative">
                <img src="<?= base_url('appstack/public-hero.jpg') ?>" class="auth-bg" alt="Unsplash">
                <div class="auth-quote">
                    <i data-lucide="quote"></i>
                    <figure>
                        <blockquote>
                            <p>My potential is limitless, and I am capable of achieving great things.</p>
                        </blockquote>
                        <figcaption>
                            — lovebox.love
                        </figcaption>
                    </figure>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="auth-full-page d-flex p-4 p-xl-5">
                <div class="d-flex flex-column w-100 h-100">
                    <div class="auth-form">
                        <?= $this->renderSection('content') ?>
                    </div>
                    <div class="text-center">
                        <p class="mb-0">
                            &copy; <?= date('Y') . ' ' . $session->organization['organization_name'] ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="lock-screen"><h1>Screen locked. Please refresh.</h1></div>
<script src="<?= base_url('appstack/js/app.js') ?>"></script>
<script src="<?= base_url('assets/vendor/toastrjs/toastr.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/fontawesome/js/all.min.js') ?>"></script>
<script>$(function () { setTimeout(() => { $('.lock-screen').show(); }, 300000); });</script>
</body>
</html>
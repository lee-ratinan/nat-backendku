<?php
$layout = getenv('LAYOUT_FILE_OFFICE');
$layout = (!empty($layout) ? $layout : 'system/_layout_office');
$this->extend($layout);
?>
<?= $this->section('content') ?>
<?php $session = session(); ?>
    <style>
        .text-oa{color:#437271!important;}
        .bg-oa{background-color:#437271!important;color:#222!important;}
        .text-sa{color:#DFB670!important;}
        .bg-sa{background-color:#DFB670!important;color:#222!important;}
        .text-ma{color:#7D9ADE!important;}
        .bg-ma{background-color:#7D9ADE!important;color:#222!important;}
    </style>
    <div class="pagetitle">
        <h1><?= $page_title ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/dashboard') ?>"><?= lang('System.dashboard.page_title') ?></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/employment/cpf') ?>">CPF</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12 col-md-10 col-lg-6">
                <div class="card">
                    <div class="card-body pt-3">
                        <div class="row">
                            <div class="col-12 text-end">
                                <a class="btn btn-outline-primary btn-sm ms-3" href="<?= base_url($session->locale . '/office/employment/cpf/statement/create') ?>"><i class="fa-solid fa-plus-circle"></i> New Statement</a>
                            </div>
                            <div class="col-12">
                                <h5 class="card-title"><i class="fa-solid fa-piggy-bank fa-fw me-3"></i> <?= $page_title ?></h5>
                            </div>
                        </div>
                        <table class="table table-sm table-hover table-striped">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Year</th>
                                <th>Statement</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($statements as $row) : ?>
                                <tr>
                                    <td><a class="btn btn-outline-primary btn-sm" href="<?= base_url($session->locale . '/office/employment/cpf/statement/edit/' . ($row['id'] * $nonce)) ?>"><i class="fa-solid fa-eye"></i></a></td>
                                    <td><?= $row['statement_year'] ?></td>
                                    <td><a class="btn btn-outline-primary btn-sm w-100" href="<?= $row['google_drive_url'] ?>" target="_blank"><i class="fa-solid fa-file-pdf"></i> CPF Statement: <?= $row['statement_year'] ?></a></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
        });
    </script>
<?php $this->endSection() ?>
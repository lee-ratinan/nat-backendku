<?php
$layout = getenv('LAYOUT_FILE_OFFICE');
$layout = (!empty($layout) ? $layout : 'system/_layout_office');
$this->extend($layout);
?>
<?= $this->section('content') ?>
<?php $session = session(); ?>
    <style>@media print {  .page-break-after {page-break-after: always;}  }</style>
    <div class="pagetitle d-print-none">
        <h1><?= $page_title ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/dashboard') ?>"><?= lang('System.dashboard.page_title') ?></a></li>
                <?php if ($year == 0) : ?>
                    <li class="breadcrumb-item active"><?= $page_title ?></li>
                <?php else : ?>
                    <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/profile/plan') ?>">Plan</a></li>
                    <li class="breadcrumb-item active"><?= $year ?></li>
                <?php endif; ?>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row d-print-none">
            <div class="col">
                <a class="btn btn-outline-primary" href="<?= base_url($session->locale . '/office/profile/plan-export/' . $year) ?>" target="_blank"><i class="fa-solid fa-print"></i></a>
                <a class="btn btn-<?= (2030 == $year ? '' : 'outline-') ?>primary" href="<?= base_url($session->locale . '/office/profile/plan/2030') ?>">2030</a>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col">
                <?php if (0 != $year) : ?>
                    <?php include 'profile_plan_' . $year . '.php'; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php $this->endSection() ?>
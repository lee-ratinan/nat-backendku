<?php
$layout = getenv('LAYOUT_FILE_OFFICE');
$layout = (!empty($layout) ? $layout : 'system/_layout_office');
$this->extend($layout);
?>
<?= $this->section('content') ?>
<?php $session = session(); ?>
<style>h6 {margin-top:1rem;}</style>
<div class="pagetitle">
    <h1><?= $page_title ?></h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/dashboard') ?>"><?= lang('System.dashboard.page_title') ?></a></li>
            <li class="breadcrumb-item active"><?= $page_title ?></li>
        </ol>
    </nav>
</div>
<section class="section">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Resume Generation</h5>
                    <form action="<?= base_url($session->locale . '/office/profile/resume/builder') ?>" method="POST">
                        <input class="btn btn-outline-primary" type="submit" value="Submit" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $this->endSection() ?>
<?php
$layout = getenv('LAYOUT_FILE_OFFICE');
$layout = (!empty($layout) ? $layout : 'system/_layout_office');
$this->extend($layout);
?>
<?= $this->section('content') ?>
<?php $session = session(); ?>
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
                    <div class="card-body pt-3">
                        <a class="btn btn-outline-primary btn-sm float-end ms-3" href="<?= base_url($session->locale . '/office/fiction/create') ?>"><i class="fa-solid fa-plus-circle"></i> New Fiction</a>
                        <h5 class="card-title"><i class="fa-solid fa-location-dot fa-fw me-3"></i> <?= $page_title ?></h5>
                        <div class="row">
                            <?php foreach ($titles as $fiction) : ?>
                            <div class="col-lg-4 col-xl-3">
                                <?php
                                $id = $fiction['id'] * $nonce;
                                $edit_link = base_url($session->locale . '/office/fiction/edit/' . $id);
                                $view_link = base_url($session->locale . '/office/fiction/view-entries/' . $fiction['fiction_slug']);
                                ?>
                                <a class="text-decoration-none" href="<?= $view_link ?>">
                                    <img class="img-fluid mb-2" src="<?= base_url('file/fiction_' . $fiction['fiction_slug'] . '.jpg') ?>" alt="<?= $fiction['fiction_title'] ?>" />
                                </a>
                                <div class="input-group mb-3 w-100">
                                    <a class="btn btn-primary" href="<?= $view_link ?>" style="width: 70%">
                                        <b><?= $fiction['fiction_title'] ?></b><br><?= $fiction['pen_name'] ?>
                                    </a>
                                    <a class="btn btn-danger" href="<?= $edit_link ?>" style="width: 30%">
                                        <i class="fa-solid fa-edit"></i><br>Edit
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $this->endSection() ?>
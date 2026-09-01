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
            <?php foreach ($documents as $row) : ?>
                <div class="col-sm-6 col-lg-4 col-xl-3">
                    <div class="card">
                        <?php $image = ('passport' == $row['document_type'] ? 'profile-header-passport.jpg' : 'profile-header-' . strtolower($row['country_code']) . '.jpg'); ?>
                        <img src="<?= base_url('file/' . $image)?>" class="card-img-top" alt="<?= $row['document_title'] ?>">
                        <div class="card-body pt-3">
                            <h6 class="card-title"><?= $row['document_title'] ?></h6>
                            <?php
                            $links = json_decode($row['google_drive_link'], true);
                            if (!empty($links)) {
                                foreach ($links as $key => $link) {
                                    if ('#' == $link || empty($link)) {
                                        echo '<a class="btn btn-outline-danger btn-sm me-3 my-1" disabled>' . $key . '</a>';
                                    } else {
                                        echo '<a class="btn btn-outline-primary btn-sm me-3 my-1" href="' . $link . '" target="_blank">' . $key . '</a>';
                                    }
                                }
                            }
                            ?>
                            <div><?= $document_types[$row['document_type']] ?></div>
                            <div><b>#:</b> <?= empty($row['document_number']) ? 'n/a' : $row['document_number'] ?></div>
                            <div><b>Issued:</b> <?= empty($row['issued_date']) || '0000-00-00' == $row['issued_date'] ? '-' : date(DATE_FORMAT_UI, strtotime($row['issued_date'])) ?></div>
                            <div>
                                <b>Expiry:</b> <?= empty($row['expiry_date']) || '0000-00-00' == $row['expiry_date'] ? '-' : date(DATE_FORMAT_UI, strtotime($row['expiry_date'])) ?>
                                <?php
                                if (!empty($row['expiry_date'])) {
                                    $today = strtotime('now');
                                    $expiry = strtotime($row['expiry_date']);
                                    if ($today >= $expiry) {
                                        echo '<span class="badge bg-danger">Expired</span>';
                                    }
                                }
                                ?>
                            </div>
                            <hr>
                            <?= $row['other_notes'] ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="col-12">
                <p class="small">Data on this page is stored in <code>profile_identity</code> table. No CRUD provided at the moment.</p>
            </div>
        </div>
    </section>
<?php $this->endSection() ?>
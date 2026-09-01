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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/journey/operator') ?>">Operator</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2>By Model</h2>
                        <table class="table table-sm table-hover table-striped">
                            <tbody>
                            <?php $c = 0; ?>
                            <?php foreach ($aircrafts as $type => $data) : ?>
                                <tr>
                                    <td style="width:120px;"><?= $type ?></td>
                                    <td>
                                        <?php foreach ($data as $model => $count) : ?>
                                            <?php $color_set = $colors[$c%4]; $c++; ?>
                                            <h6 class="mb-0"><?= $model ?></h6>
                                            <?php if (40 < $count) : ?>
                                                <span class="badge bg-<?= $color_set[0] ?> rounded-pill w-100 mb-3">- <?= $count ?> -</span>
                                            <?php else : ?>
                                                <?php $length = $count/40*100; ?>
                                                <div class="progress mb-3" role="progressbar" aria-label="Aircraft" aria-valuenow="<?= $length ?>" aria-valuemin="0" aria-valuemax="100">
                                                    <div class="progress-bar bg-<?= $color_set[0] ?>" style="width: <?= $length ?>%"><?= $count ?></div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2>By Manufacturer</h2>
                        <div class="row">
                            <?php foreach ($by_manufacturer as $manufacturer => $data) : ?>
                                <div class="col text-center">
                                    <img alt="<?= $manufacturer ?>" class="img-thumbnail mb-3" style="height:4rem;" src="<?= base_url('file/airplane-' . strtolower($manufacturer) . '.png') ?>" />
                                    <h6 class="display-4"><?= $data ?></h6>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $this->endSection() ?>
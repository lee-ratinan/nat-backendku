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
                        <h2>By Year</h2>
                        <?php $c = 0; ?>
                        <table class="table table-sm table-striped table-hover">
                            <thead>
                            <tr>
                                <th>Operator</th>
                                <th>Count</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php for ($year = date('Y'); $year >= 2006; $year--) : ?>
                                <tr>
                                    <td class="text-center" colspan="2"><h6><?= $year ?></h6></td>
                                </tr>
                                <?php if (isset($by_year[$year])) : ?>
                                    <?php foreach ($by_year[$year] as $operator_id => $count) : ?>
                                        <?php $color_set = $colors[$c % 4]; $c++; ?>
                                        <tr>
                                            <td><b><?= $operators[$operator_id]['name'] ?></b> <?= '-' != $operators[$operator_id]['code'] ? '[' . $operators[$operator_id]['code'] . ']' : '' ?></td>
                                            <td>
                                                <?php for ($i = 0; $i < $count; $i++) : ?>
                                                    <i class="fa-solid fa-circle text-<?= $color_set[0] ?>"></i>
                                                <?php endfor; ?>
                                                <h6 class="d-inline-block mb-0"><?= $count ?></h6>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="2" class="text-center">- No data -</td>
                                    </tr>
                                <?php endif; ?>
                            <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2>By Operator</h2>
                        <table class="table table-sm table-striped table-hover">
                            <thead>
                            <tr>
                                <th colspan="2">Operator</th>
                                <th>Count</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($by_operator as $operator_id => $count): ?>
                                <?php $color_set = $colors[$c % 4]; $c++; ?>
                                <tr>
                                    <td>
                                        <img alt="<?= $operators[$operator_id]['name'] ?>" class="float-end img-thumbnail" style="height:2.5rem" src="<?= base_url('file/operator-' . $operators[$operator_id]['file'] . '.png') ?>"/>
                                        <b><?= $operators[$operator_id]['name'] ?></b> <?= '-' != $operators[$operator_id]['code'] ? '[' . $operators[$operator_id]['code'] . ']' : '' ?>
                                    </td>
                                    <td><?= $modes[$operators[$operator_id]['type']] ?></td>
                                    <td style="min-width:250px;">
                                        <?php if ($count > 30) : ?>
                                            <span class="badge bg-danger rounded-pill w-100">- <?= $count ?> -</span>
                                        <?php else: ?>
                                            <?php $length = $count / 30 * 100; ?>
                                            <div class="progress" role="progressbar" aria-label="Basic example" aria-valuenow="<?= $length ?>" aria-valuemin="0" aria-valuemax="100">
                                                <div class="progress-bar bg-<?= $color_set[0] ?>" style="width: <?= $length ?>%"><?= $count ?></div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $this->endSection() ?>
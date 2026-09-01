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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/journey/attraction') ?>">Attraction</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2>Attraction by Year</h2>
                        <table class="table table-sm table-striped table-hover">
                            <thead>
                            <tr>
                                <th style="width:80px;">Year</th>
                                <th>Type</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php for ($year = date('Y'); $year >= 2018; $year--) : ?>
                                <tr>
                                    <td class="text-center"><h6><?= $year ?></h6></td>
                                    <td>
                                        <?php if (isset($by_year[$year])) : ?>
                                            <?php arsort($by_year[$year]); ?>
                                            <table class="table table-sm table-borderless mb-0">
                                                <?php foreach ($by_year[$year] as $category => $count) : ?>
                                                <tr>
                                                    <td class="p-0"><?= ucwords($category) ?></td>
                                                    <td class="p-0 text-end"><h6 class="d-inline-block">(<?= $count ?>)</h6></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </table>
                                        <?php else: ?>
                                        -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2>Attraction Categories</h2>
                        <?php
                        arsort($categories);
                        $max = max($categories);
                        ?>
                        <table class="table table-sm table-striped table-hover">
                            <thead>
                            <tr>
                                <th>Category</th>
                                <th style="min-width:200px;">Type</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $c = 0; ?>
                            <?php foreach ($categories as $category => $count) : ?>
                                <?php $length = $count/$max*100; $color_set = $colors[$c%4]; $c++; ?>
                                <tr>
                                    <td><?= ucwords($category) ?></td>
                                    <td>
                                        <div class="progress" role="progressbar" aria-label="Count" aria-valuenow="<?= $length ?>" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar bg-<?= $color_set[0] ?>" style="width: <?= $length ?>%"><?= $count ?></div>
                                        </div>
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
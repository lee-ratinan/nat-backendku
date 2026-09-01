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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/journey/trip') ?>">Trip</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2>Visited Countries by Year</h2>
                        <table class="table table-sm table-striped table-hover">
                            <thead>
                            <tr>
                                <th>Year</th>
                                <th style="max-width:80px">#</th>
                                <th style="min-width:180px">Countries</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php for ($year = date('Y'); $year >= 1989; $year--) : ?>
                                <tr>
                                    <?php $count = count($visited_countries_by_year[$year]??[]); ?>
                                    <td><h6><?= $year ?></h6></td>
                                    <td class="text-center"><h6><?= $count ?></h6></td>
                                    <td>
                                        <?php if (isset($visited_countries_by_year[$year])) : ?>
                                        <?php ksort($visited_countries_by_year[$year]); ?>
                                        <h5>
                                            <?php foreach ($visited_countries_by_year[$year] as $country_code => $dummy) : ?>
                                                <span class="badge text-bg-<?= in_array($country_code, $countries_considered_home) ? 'warning' : 'primary' ?> rounded-pill"><span class="flag-icon flag-icon-<?= strtolower($country_code) ?>"></span> <?= $country_code ?></span>
                                            <?php endforeach; ?>
                                        </h5>
                                        <?php else : ?>
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
                    <div class="card-body text-center p-5">
                        <h2><i class="fa-solid fa-globe-asia"></i> <?= count($countries_by_visits) ?></h2>
                        <h3>Visited Countries</h3>
                        <h4>
                            <?php foreach ($countries_by_visits as $country_code => $count) : ?>
                            <span class="badge text-bg-<?= in_array($country_code, $countries_considered_home) ? 'warning' : 'primary' ?> rounded-pill m-1"><span class="flag-icon flag-icon-<?= strtolower($country_code) ?>"></span> <?= $countries[$country_code]['common_name'] ?></span>
                            <?php endforeach; ?>
                        </h4>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body p-3">
                        <h2>Countries by Visits</h2>
                        <table class="table table-sm table-striped table-hover">
                            <thead>
                            <tr>
                                <th style="min-width:100px">Country</th>
                                <th style="max-width:80px">#</th>
                                <th style="min-width:200px"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($countries_considered_home as $country_code) : ?>
                                <tr>
                                    <td><h5 class="d-inline-block"><span class="flag-icon flag-icon-<?= strtolower($country_code) ?>"></span> <?= $countries[$country_code]['common_name'] ?></h5> <i class="fa-solid fa-home"></i></td>
                                    <td colspan="2"><h5><span class="badge text-bg-warning rounded-pill"><?= $countries_by_visits[$country_code] ?></span></h5></td>
                                </tr>
                                <?php unset($countries_by_visits[$country_code]); ?>
                            <?php endforeach; ?>
                            <?php $frequent_max = max($countries_by_visits); ?>
                            <?php foreach ($countries_by_visits as $country_code => $count) : ?>
                                <tr>
                                    <td><span class="flag-icon flag-icon-<?= strtolower($country_code) ?>"></span> <?= $countries[$country_code]['common_name'] ?></td>
                                    <td class="text-center"><h6><?= $count ?></h6></td>
                                    <td>
                                        <?php $percent = round($count/$frequent_max*100); ?>
                                        <div class="progress" role="progressbar" aria-label="<?= $countries[$country_code]['common_name'] ?>" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar bg-<?= $percent > 75 ? 'danger' : 'primary' ?>" style="width: <?= $percent ?>%"></div>
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
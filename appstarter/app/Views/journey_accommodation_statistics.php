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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/journey/accommodation') ?>">Accommodation</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <h2>Accommodation By Year</h2>
                        <table class="table table-sm table-striped table-hover">
                            <thead>
                            <tr>
                                <th style="max-width:40px">Year</th>
                                <th style="min-width:200px"></th>
                                <th class="text-end" style="max-width:80px">Nights</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php for ($year = date('Y'); $year >= 2006; $year--) : ?>
                                <tr>
                                    <?php $class_set = $color_classes[$year % 4]; ?>
                                    <td class="text-center"><h6><?= $year ?></h6></td>
                                    <td>
                                        <table class="table table-sm table-borderless mb-0">
                                            <?php if (isset($by_year[$year])) : ?>
                                            <?php foreach ($by_year[$year]['countries'] as $country_code => $data) : ?>
                                            <tr>
                                                <td style="max-width:40px"><span class="flag-icon flag-icon-<?= strtolower($country_code) ?>"></span></td>
                                                <td style="min-width:200px">
                                                    <div class="progress-stacked">
                                                        <?php foreach ($data['list'] as $i => $num) : ?>
                                                            <?php
                                                            $length = 0;
                                                            if ($by_year[$year]['annual_count'] > $by_year_half) {
                                                                $length = $num/$by_year_max*100;
                                                            } else {
                                                                $length = $num/$by_year_half*100;
                                                            }
                                                            ?>
                                                            <div class="progress" role="progressbar" aria-label="<?= $num ?>" aria-valuenow="<?= $length ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?= $length ?>%">
                                                                <div class="progress-bar bg-<?= $class_set[$i % 2] ?>"><?= $num ?></div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </table>
                                    </td>
                                    <td class="text-end"><h6><?= (isset($by_year[$year]['annual_count']) ? number_format($by_year[$year]['annual_count']) : '-') ?></h6></td>
                                </tr>
                            <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <h2>Accommodation By Country</h2>
                        <table class="table table-sm table-striped table-hover">
                            <thead>
                            <tr>
                                <th style="max-width:120px">Country</th>
                                <th style="min-width:200px"></th>
                                <th class="text-end" style="max-width:80px">Nights</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $counter = 0; ?>
                            <?php foreach ($by_country as $country_code => $data) : ?>
                            <tr>
                                <?php $class_set = $color_classes[$counter % 4]; $counter++; ?>
                                <td><span class="flag-icon flag-icon-<?= strtolower($country_code) ?>"></span> <?= lang('ListCountries.countries.' . $country_code . '.common_name') ?></td>
                                <td>
                                    <div class="progress-stacked">
                                        <?php foreach ($data['list'] as $i => $num) : ?>
                                            <?php
                                            $length = 0;
                                            if ($data['nights'] > $by_country_half) {
                                                $length = $num/$by_country_max*100;
                                            } else {
                                                $length = $num/$by_country_half*100;
                                            }
                                            ?>
                                            <div class="progress" role="progressbar" aria-label="<?= $num ?>" aria-valuenow="<?= $length ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?= $length ?>%">
                                                <div class="progress-bar bg-<?= $class_set[$i % 2] ?>"><?= $num ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td class="text-end"><h6><?= number_format($data['nights']) ?></h6></td>
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
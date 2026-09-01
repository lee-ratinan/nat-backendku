<?php
$layout = getenv('LAYOUT_FILE_OFFICE');
$layout = (!empty($layout) ? $layout : 'system/_layout_office');
$this->extend($layout);
?>
<?= $this->section('content') ?>
<?php $session = session(); ?>
    <style>
        #country-company, #country-day, #home-country-chart {width: 100%;height: 300px;}
    </style>
    <script src="<?= base_url('assets/vendor/amcharts5/index.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/amcharts5/xy.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/amcharts5/percent.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/amcharts5/themes/Animated.js') ?>"></script>
    <div class="pagetitle">
        <h1><?= $page_title ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/dashboard') ?>"><?= lang('System.dashboard.page_title') ?></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/employment') ?>">Employment</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3>Days</h3>
                        <div class="table-responsive">
                            <table id="main-table" class="table table-sm table-striped table-hover">
                                <thead>
                                <tr>
                                    <th style="min-width:120px">Country</th>
                                    <th style="min-width:150px">Company</th>
                                    <th style="min-width:80px" class="text-end">Days</th>
                                    <th style="min-width:120px" class="text-end">Length</th>
                                    <th style="min-width:100px">From</th>
                                    <th style="min-width:100px">To</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($duration as $company) : ?>
                                    <tr>
                                        <td><?= lang('ListCountries.countries.' . $company['country'] . '.common_name') ?></td>
                                        <td><?= $company['name'] ?></td>
                                        <td class="text-end" data-sort="<?= $company['days'] ?>"><?= number_format($company['days']) . (empty($company['dates'][1]) ? '+' : '') ?></td>
                                        <td class="text-end" data-sort="<?= $company['days'] ?>"><?= $company['length'] ?></td>
                                        <td data-sort="<?= $company['dates'][0] ?>"><?= date(DATE_FORMAT_UI, strtotime($company['dates'][0])) ?></td>
                                        <td data-sort="<?= @$company['dates'][1] ?>"><?= (empty($company['dates'][1]) ? '-' : date(DATE_FORMAT_UI, strtotime($company['dates'][1]))) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3>Days</h3>
                        <script>
                            <?php $height = (count($duration) * 30) + 100; ?>
                            <?php echo generate_bar_chart_script($main_chart, 'main-chart', 'company', ['days' => 'Days'], $height . 'px', '{company}: {label}', '', ''); ?>
                        </script>
                        <div id="main-chart"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3>By Country</h3>
                        <div class="table-responsive">
                            <table id="country-table" class="table table-sm table-striped table-hover">
                                <thead>
                                <tr>
                                    <th style="min-width:120px">Country</th>
                                    <th style="min-width:90px" class="text-end">Companies</th>
                                    <th style="min-width:90px" class="text-end">Days*</th>
                                    <th style="min-width:90px" class="text-end">Length*</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($country_days as $country_code => $days) : ?>
                                <tr>
                                    <td><?= lang('ListCountries.countries.' . $country_code . '.common_name') ?></td>
                                    <td class="text-end"><?= number_format($country_companies[$country_code]) ?></td>
                                    <td class="text-end"><?= number_format($days) ?></td>
                                    <td class="text-end"><?= $country_length[$country_code] ?></td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <p class="small">* The number could be overlapping if I work at multiple companies at the same time.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3>By Country</h3>
                        <h4>Companies by country</h4>
                        <script>
                            <?php
                            echo generate_pie_chart_script($charts, 'country-company', 'country', 'companies');
                            ?>
                        </script>
                        <div id="country-company"></div>
                        <hr />
                        <h4>Days by country</h4>
                        <script>
                            <?php
                            echo generate_pie_chart_script($charts, 'country-day', 'country', 'days');
                            ?>
                        </script>
                        <div id="country-day"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3>By Home Country</h3>
                        <div class="table-responsive">
                            <table id="home-country-table" class="table table-sm table-striped table-hover">
                                <thead>
                                <tr>
                                    <th style="min-width:120px">Home Country</th>
                                    <th style="min-width:120px" class="text-end">Companies</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($home_chart as $row) : ?>
                                    <tr>
                                        <td><?= $row['country'] ?></td>
                                        <td class="text-end"><?= number_format($row['count']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3>By Home Country</h3>
                        <script>
                            <?php
                            echo generate_pie_chart_script($home_chart, 'home-country-chart', 'country', 'count');
                            ?>
                        </script>
                        <div id="home-country-chart"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#main-table').DataTable({
                searching: false,
                paging: false,
                info: false,
                order: [2, 'desc'],
            });
            $('#country-table, #home-country-table').DataTable({
                searching: false,
                paging: false,
                info: false,
                order: [1, 'desc'],
            });
        });
    </script>
<?php $this->endSection() ?>
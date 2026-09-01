<?php
$layout = getenv('LAYOUT_FILE_OFFICE');
$layout = (!empty($layout) ? $layout : 'system/_layout_office');
$this->extend($layout);
?>
<?= $this->section('content') ?>
<?php $session = session(); ?>
    <script src="<?= base_url('assets/vendor/amcharts5/index.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/amcharts5/xy.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/amcharts5/themes/Animated.js') ?>"></script>
    <div class="pagetitle">
        <h1><?= $page_title ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/dashboard') ?>"><?= lang('System.dashboard.page_title') ?></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/employment/freelance') ?>">Freelance</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/employment/freelance-income') ?>">Freelance Income</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <script>
                                    <?php
                                    $currencies = [];
                                    foreach ($chart_data as $currency => $chart_for_currency) {
                                        $currencies[] = $currency;
                                        $height       = (count($chart_for_currency) * 30) + 100;
                                        echo generate_bar_chart_script($chart_for_currency, 'main-chart-' . $currency, 'year', ['total' => 'Total', 'taxes' => 'Taxes'], $height . 'px', '{year}: {total}');
                                    }
                                    ?>
                                </script>
                                <?php foreach ($currencies as $currency) : ?>
                                    <div id="main-chart-<?= $currency ?>"></div>
                                <?php endforeach; ?>
                            </div>
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-borderless dttable">
                                        <thead>
                                        <tr>
                                            <th style="min-width:80px">Year</th>
                                            <th style="min-width:160px">Project</th>
                                            <th style="min-width:120px">Date</th>
                                            <th style="min-width:120px">Subtotal</th>
                                            <th style="min-width:120px">Tax</th>
                                            <th style="min-width:120px">Total</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($by_year as $year => $projects) : ?>
                                            <?php foreach ($projects as $data) : ?>
                                                <tr>
                                                    <td><?= $year ?></td>
                                                    <td><?= $data['company_name'] ?><br><?= $data['project_title'] ?></td>
                                                    <td><?= date(DATE_FORMAT_UI, strtotime($data['pay_date'])) ?></td>
                                                    <td class="text-end"><?= currency_format($data['currency'], $data['subtotal_amount']) ?></td>
                                                    <td class="text-end"><?= currency_format($data['currency'], $data['tax_amount']) ?></td>
                                                    <td class="text-end"><?= currency_format($data['currency'], $data['total_amount']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const table = $('.dttable').DataTable({
                searching: true,
                pageLength: 25,
                order: [[0, 'asc']],
                scrollX: true,
            });
        });
    </script>
<?php $this->endSection() ?>
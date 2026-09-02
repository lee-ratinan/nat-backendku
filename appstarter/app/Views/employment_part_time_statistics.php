<?php
$layout = getenv('LAYOUT_FILE_OFFICE');
$layout = (!empty($layout) ? $layout : 'system/_layout_office');
$this->extend($layout);
?>
<?= $this->section('content') ?>
<?php $session = session(); ?>
    <style>
        #main-chart, #monthly-chart {
            width: 100%;
        }
    </style>
    <script src="<?= base_url('assets/vendor/amcharts5/index.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/amcharts5/xy.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/amcharts5/themes/Animated.js') ?>"></script>
    <div class="pagetitle">
        <h1><?= $page_title ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/dashboard') ?>"><?= lang('System.dashboard.page_title') ?></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/employment/part-time') ?>">Part Time Schedule</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body pt-3">
                        <h5 class="card-title">Part-Time Statistics (before CPF)</h5>
                        <div class="row">
                            <div class="col">
                                <script><?= generate_bar_chart_script($chart_data, 'main-chart', 'date', ['subtotal' => 'Subtotal ($)'], $height) ?></script>
                                <div id="main-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body pt-3">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <h5 class="card-title">Deductions (CPF)</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th class="text-end">Subtotal ($)</th>
                                            <th class="text-end">CPF ($)</th>
                                            <th class="text-end">CPF (%)</th>
                                            <th class="text-end">Total ($)</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($deductions as $month => $deduction): ?>
                                        <tr>
                                            <td><?= date('M Y', strtotime($month . '-01')) ?></td>
                                            <td class="text-end"><?= number_format($subtotals[$month], 2) ?></td>
                                            <td class="text-end"><?= number_format($deduction, 2) ?></td>
                                            <td class="text-end">
                                                <?php
                                                if (0 < $subtotals[$month]) {
                                                    $percent = $deduction * 100 / $subtotals[$month];
                                                    echo number_format($percent, 2) . '%';
                                                }
                                                ?>
                                            </td>
                                            <td class="text-end"><?= number_format($totals[$month], 2) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <h5 class="card-title">Monthly Totals</h5>
                                <script><?= generate_bar_chart_script($monthly, 'monthly-chart', 'month', ['total' => 'Total ($)'], $monthly_height) ?></script>
                                <div id="monthly-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $this->endSection() ?>
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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/employment/salary') ?>">Salary</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <?php foreach ($company_list as $this_company) : ?>
                            <a class="btn btn<?= ($company_id == $this_company['id'] ? '' : '-outline') ?>-success btn-sm mb-2" href="<?= base_url($lang . '/office/employment/salary/stats/company/' . $this_company['id']) ?>"><?= $this_company['company_trade_name'] ?></a>
                        <?php endforeach; ?>
                        <h3><?= $company['company_trade_name'] ?></h3>
                        <p>
                            <?= $currency_code ?> |
                            <?= date(DATE_FORMAT_UI, strtotime($company['employment_start_date'])) ?> to
                            <?= (empty($company['employment_end_date']) ? 'Present' : date(DATE_FORMAT_UI, strtotime($company['employment_end_date']))) ?>
                        </p>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <h4>Total Income By Year</h4>
                                <?php if (empty($chart_data)) : ?>
                                    <p>-</p>
                                <?php else: ?>
                                <script>
                                    <?php $height = (count($chart_data) * 30) + 100; ?>
                                    <?php echo generate_bar_chart_script($chart_data, 'chart_1', 'year', ['total' => 'Total', 'subtotal' => 'Subtotal'], $height . 'px'); ?>
                                </script>
                                <div id="chart_1"></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-12 col-md-6">
                                <h4>Base Salaries</h4>
                                <?php if (empty($chart_data_2)) : ?>
                                <p>-</p>
                                <?php else: ?>
                                <script>
                                    <?php $height = (count($chart_data_2) * 30) + 100; ?>
                                    <?php echo generate_bar_chart_script($chart_data_2, 'chart_2', 'month', ['base' => 'Base Salary'], $height . 'px'); ?>
                                </script>
                                <div id="chart_2"></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <script>
                                    <?php echo generate_lines_chart_script($chart_data_3, 'chart_3', 'month', ['total', 'subtotal']); ?>
                                </script>
                                <div id="chart_3" style="height:400px;"></div>
                            </div>
                        </div>
                        <h4 class="mt-3">Summary</h4>
                        <div class="table-responsive">
                            <table id="summary" class="table table-striped table-hover table-borderless">
                                <thead>
                                <tr>
                                    <th>Year</th>
                                    <th class="text-end">Subtotal</th>
                                    <th class="text-end">Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($chart_data as $row) : ?>
                                <tr>
                                    <td><?= $row['year'] ?></td>
                                    <td class="text-end"><?= currency_format($currency_code, $row['subtotal']) ?></td>
                                    <td class="text-end"><?= currency_format($currency_code, $row['total']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('table').DataTable({
                fixedHeader: true,
                searching: false, // don't allow the search for this one
                pageLength: 25,
                scrollX: true,
                order: [[0, 'desc']],
            });
        });
    </script>
<?php $this->endSection() ?>
<?php
$layout = getenv('LAYOUT_FILE_OFFICE');
$layout = (!empty($layout) ? $layout : 'system/_layout_office');
$this->extend($layout);
?>
<?= $this->section('content') ?>
<?php $session = session(); ?>
    <style>
        .row-ordinary-account td{background-color:#437271!important;color:#222!important;}
        .row-special-account td{background-color:#DFB670!important;color:#222!important;}
        .row-medisave-account td{background-color:#7D9ADE!important;color:#222!important;}
    </style>
    <script src="<?= base_url('assets/vendor/amcharts5/index.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/amcharts5/percent.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/amcharts5/themes/Animated.js') ?>"></script>
    <div class="pagetitle">
        <h1><?= $page_title ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/dashboard') ?>"><?= lang('System.dashboard.page_title') ?></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/employment/cpf') ?>">CPF</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body pt-3">
                        <h2>Current Account Balance</h2>
                        <script>
                            <?php echo generate_pie_chart_script($chart_1, 'chart_1', 'account', 'value');?>
                        </script>
                        <div id="chart_1" style="width:100%;height:500px"></div>
                        <table class="table table-striped table-hover table-borderless table-sm mt-3">
                            <?php $grand_total = 0; ?>
                            <?php foreach ($chart_1 as $row) : ?>
                            <tr class="row-<?= str_replace(' ', '-', strtolower($row['account'])) ?>">
                                <td><?= $row['account'] ?></td>
                                <td class="text-end"><?= currency_format('SGD', $row['value']) ?></td>
                                <?php $grand_total += $row['value']; ?>
                            </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td><b>TOTAL</b></td>
                                <td class="text-end"><?= currency_format('SGD', $grand_total) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body pt-3">
                        <h2>YTD Contributions</h2>
                        <script>
                            <?php echo generate_pie_chart_script($chart_2, 'chart_2', 'contributor', 'value');?>
                        </script>
                        <div id="chart_2" style="width:100%;height:500px"></div>
                        <table class="table table-striped table-hover table-borderless table-sm mt-3">
                            <?php $total = 0; ?>
                            <?php foreach ($chart_2 as $row) : ?>
                                <tr>
                                    <td><?= $row['contributor'] ?></td>
                                    <td class="text-end"><?= currency_format('SGD', $row['value']) ?></td>
                                    <?php $total += $row['value']; ?>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td><b>TOTAL</b></td>
                                <td class="text-end"><?= currency_format('SGD', $total) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body pt-3">
                        <h2>Grand Total</h2>
                        <?php
                        $all_i_have = $grand_total + $inv['investment_value'];
                        $total_pc   = ceil($grand_total / $all_i_have * 100);
                        $inv_pc     = 100 - $total_pc;
                        ?>
                        <h3 class="text-center"><?= currency_format('SGD', $all_i_have) ?></h3>
                        <div class="row">
                            <div class="col">
                                <h4>CPF:<br/><?= currency_format('SGD', $grand_total) ?></h4>
                            </div>
                            <div class="col text-end">
                                <h4>INV:<br/><?= currency_format('SGD', $inv['investment_value']) ?></h4>
                            </div>
                        </div>
                        <div class="progress-stacked">
                            <div class="progress" role="progressbar" aria-label="CPF" aria-valuenow="<?= $total_pc ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $total_pc ?>%">
                                <div class="progress-bar bg-warning"></div>
                            </div>
                            <div class="progress" role="progressbar" aria-label="INV" aria-valuenow="<?= $inv_pc ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $inv_pc ?>%">
                                <div class="progress-bar bg-info"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $this->endSection() ?>
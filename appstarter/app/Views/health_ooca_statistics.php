<?php
$layout = getenv('LAYOUT_FILE_OFFICE');
$layout = (!empty($layout) ? $layout : 'system/_layout_office');
$this->extend($layout);
?>
<?= $this->section('content') ?>
<?php $session = session(); ?>
    <style>
        .freq-box-1 {background-color:#16371d!important;color:#fff!important;}
        .freq-box-2 {background-color:#255b31!important;color:#fff!important;}
        .freq-box-3 {background-color:#337f45!important;color:#000!important;}
        .freq-box-4 {background-color:#5bbd72!important;color:#000!important;}
        .freq-box-5 {background-color:#80cc91!important;color:#000!important;}
        th, td {text-align: center;}
    </style>
    <div class="pagetitle">
        <h1><?= $page_title ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/dashboard') ?>"><?= lang('System.dashboard.page_title') ?></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/health/ooca') ?>">OOCA Visit Log</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <tr>
                                    <th>Year</th>
                                    <th>Jan</th>
                                    <th>Feb</th>
                                    <th>Mar</th>
                                    <th>Apr</th>
                                    <th>May</th>
                                    <th>Jun</th>
                                    <th>Jul</th>
                                    <th>Aug</th>
                                    <th>Sep</th>
                                    <th>Oct</th>
                                    <th>Nov</th>
                                    <th>Dec</th>
                                    <th>Total</th>
                                </tr>
                                <?php for ($y = 2022; $y <= date('Y'); $y++) : ?>
                                    <tr>
                                        <th><?= $y ?></th>
                                        <?php for ($m = 1; $m <= 12; $m++) : ?>
                                            <?php $num = $freq_data[$y][$m] ?? 0; ?>
                                            <td class="freq-box freq-box-<?= min(5, $num) ?>"><?= $num ?></td>
                                        <?php endfor; ?>
                                        <td><?= $yr_total[$y] ?? 0 ?></td>
                                    </tr>
                                <?php endfor; ?>
                                <tr>
                                    <td colspan="10"></td>
                                    <td colspan="3">Grand Total</td>
                                    <td><?= $total_records ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {});
    </script>
<?php $this->endSection() ?>
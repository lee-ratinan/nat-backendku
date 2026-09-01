<?php
$layout = getenv('LAYOUT_FILE_OFFICE');
$layout = (!empty($layout) ? $layout : 'system/_layout_office');
$this->extend($layout);
?>
<?= $this->section('content') ?>
<?php $session = session(); ?>
    <script src="<?= base_url('assets/vendor/amcharts5/index.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/amcharts5/xy.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/amcharts5/percent.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/amcharts5/themes/Animated.js') ?>"></script>
    <style>
        .row-ordinary-account td{background-color:#437271!important;color:#222!important;}
        .row-special-account td{background-color:#DFB670!important;color:#222!important;}
        .row-medisave-account td{background-color:#7D9ADE!important;color:#222!important;}
    </style>
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
                        <h3>Summary and Statement</h3>
                        <?php for ($y = 2020; $y <= date('Y'); $y++) : ?>
                            <a class="btn btn-<?= ($y == $year ? '' : 'outline-') ?>success btn-sm mb-2" href="<?= base_url($session->locale . '/office/employment/cpf/stats/' . $y) ?>"><?= $y ?></a>
                        <?php endfor; ?>
                        <h4>Contribution</h4>
                        <div class="table-responsive">
                            <?php
                            $contribution_sum = array_sum(array_column($contribution, 'amount'));
                            ?>
                            <table class="table table-sm table-striped table-hover table-borderless">
                                <?php foreach ($contribution as $i => $row) : ?>
                                <tr>
                                    <td style="width:40%"><?= $row['contributor'] ?></td>
                                    <td style="width:30%" class="text-end text-success"><?= currency_format('SGD', $row['amount']) ?></td>
                                    <?php if (0 == $i) : ?><td rowspan="2" class="text-end text-success"><?= currency_format('SGD', $contribution_sum) ?></td><?php endif; ?>
                                </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                        <h4>Transactions</h4>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hover table-borderless">
                                <thead>
                                <tr>
                                    <th>TC</th>
                                    <th class="text-end text-success" style="width:30%">+</th>
                                    <th class="text-end text-danger" style="width:30%">-</th>
                                </tr>
                                </thead>
                                <?php $total = ['pos' => 0, 'neg' => 0]; ?>
                                <tbody>
                                <?php foreach ($by_tc as $row) : ?>
                                    <tr>
                                        <td><?= $row['transaction_code'] ?></td>
                                        <td class="text-end text-success"><?= currency_format('SGD', @$row['pos']) ?></td>
                                        <td class="text-end text-danger"><?= currency_format('SGD', @$row['neg']) ?></td>
                                    </tr>
                                    <?php
                                    $total['pos'] += @$row['pos'];
                                    $total['neg'] += @$row['neg'];
                                    ?>
                                <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td><b>TOTAL</b></td>
                                    <td class="text-end text-success"><?= currency_format('SGD', $total['pos']) ?></td>
                                    <td class="text-end text-danger"><?= currency_format('SGD', $total['neg']) ?></td>
                                </tr>
                                <?php $grand_total = $total['pos'] - $total['neg']; ?>
                                <tr>
                                    <td></td>
                                    <td colspan="2" class="text-end <?= ($grand_total > 0 ? 'text-success' : 'text-danger') ?>"><?= currency_format('SGD', $grand_total) ?></td>
                                </tr>
                                </tfoot>
                            </table>
                            <?php
                            $total_movement = $total['pos'] + $total['neg'];
                            $positive_pc    = 0;
                            $negative_pc    = 0;
                            if (0 < $total_movement) {
                                $positive_pc = ceil($total['pos'] / $total_movement * 100);
                                $negative_pc = 100 - $positive_pc;
                            }
                            ?>
                        </div>
                        <div class="progress-stacked my-3" style="height: 25px">
                            <div class="progress" role="progressbar" aria-label="Positive" aria-valuenow="<?= $positive_pc ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $positive_pc ?>%; height: 25px; font-size: 1rem;">
                                <div class="progress-bar bg-success">+<?= currency_format('SGD', $total['pos']) ?></div>
                            </div>
                            <div class="progress" role="progressbar" aria-label="Negative" aria-valuenow="<?= $negative_pc ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $negative_pc ?>%; height: 25px; font-size: 1rem;">
                                <div class="progress-bar bg-danger">-<?= currency_format('SGD', $total['neg']) ?></div>
                            </div>
                        </div>
                        <h4>Account Movements</h4>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hover table-borderless">
                                <thead>
                                <tr>
                                    <th class="text-start" style="width:40%">Account</th>
                                    <th class="text-end text-success" style="width:30%">+</th>
                                    <th class="text-end text-danger" style="width:30%">-</th>
                                </tr>
                                </thead>
                                <?php $total = ['pos' => 0, 'neg' => 0]; ?>
                                <tbody>
                                <?php foreach ($by_ac as $row) : ?>
                                    <tr class="row-<?= str_replace(' ', '-', strtolower($row['account'])) ?>">
                                        <td><?= $row['account'] ?></td>
                                        <td class="text-end text-success"><?= currency_format('SGD', @$row['pos']) ?></td>
                                        <td class="text-end text-danger"><?= currency_format('SGD', @$row['neg']) ?></td>
                                    </tr>
                                    <?php
                                    $total['pos'] += @$row['pos'];
                                    $total['neg'] += @$row['neg'];
                                    ?>
                                <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td><b>TOTAL</b></td>
                                    <td class="text-end text-success"><?= currency_format('SGD', $total['pos']) ?></td>
                                    <td class="text-end text-danger"><?= currency_format('SGD', $total['neg']) ?></td>
                                </tr>
                                <?php $grand_total = $total['pos'] - $total['neg']; ?>
                                <tr>
                                    <td></td>
                                    <td colspan="2" class="text-end <?= ($grand_total > 0 ? 'text-success' : 'text-danger') ?>"><?= currency_format('SGD', $grand_total) ?></td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                        <h4>Statement</h4>
                        <?php if (empty($statement)) : ?>
                            <p>n/a</p>
                        <?php else: ?>
                            <a href="<?= $statement['google_drive_url'] ?>" target="_blank" class="btn btn-outline-success btn-sm"><i class="fa-solid fa-file-pdf"></i> View Statement</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body pt-3">
                        <h3>Total Contribution</h3>
                        <script>
                            <?php echo generate_pie_chart_script($contribution, 'total-contribution', 'contributor', 'amount'); ?>
                        </script>
                        <div id="total-contribution" style="width:100%;height:500px"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body pt-3">
                        <h3>Summary by Transaction Code</h3>
                        <script>
                            <?php
                            $height = ((count($by_tc) * 60) + 100) . 'px';
                            echo generate_bar_chart_script($by_tc, 'summary-by-tc', 'transaction_code', ['neg' => 'Withdrawal', 'pos' => 'Deposit'], $height, '', '', '', ['neg' => '0xdc3444', 'pos' => '0x188753']);
                            ?>
                        </script>
                        <div id="summary-by-tc"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body pt-3">
                        <h3>Summary by Account</h3>
                        <script>
                            <?php
                            $height = ((count($by_ac) * 60) + 100). 'px';
                            echo generate_bar_chart_script($by_ac, 'summary-by-account', 'account', ['neg' => 'Withdrawal', 'pos' => 'Deposit'], $height, '', '', '', ['neg' => '0xdc3444', 'pos' => '0x188753']); ?>
                        </script>
                        <div id="summary-by-account"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $this->endSection() ?>
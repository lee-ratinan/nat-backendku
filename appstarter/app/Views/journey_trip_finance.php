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
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h2><i class="fa-solid fa-credit-card fa-fw me-3"></i> Finance</h2>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hover">
                                <thead>
                                <tr>
                                    <th style="width:12%">Year</th>
                                    <th style="width:22%">Transport</th>
                                    <th style="width:22%">Accommodation</th>
                                    <th style="width:22%">Attraction</th>
                                    <th style="width:22%">Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php for ($year = date('Y'); $year >= 2006; $year--) : ?>
                                    <?php
                                    $total = [];
                                    foreach ($all_currencies as $currency) {
                                        $total[$currency] = 0;
                                    }
                                    ?>
                                    <tr>
                                        <td><?= $year ?></td>
                                        <td>
                                            <?php if (isset($financial_data[$year]['transport'])) : ?>
                                                <?php $i = 0; ?>
                                                <?php foreach ($financial_data[$year]['transport'] as $currency => $amounts) : ?>
                                                    <?php $sum = array_sum($amounts);
                                                    $total[$currency] += $sum; ?>
                                                    <span class="badge bg-<?= $color_classes[0][$i % 2] ?> rounded-pill"><?= currency_format($currency, $sum) ?></span>
                                                    <?php $i++; ?>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($financial_data[$year]['accommodation'])) : ?>
                                                <?php $i = 0; ?>
                                                <?php foreach ($financial_data[$year]['accommodation'] as $currency => $amounts) : ?>
                                                    <?php $sum = array_sum($amounts);
                                                    $total[$currency] += $sum; ?>
                                                    <span class="badge bg-<?= $color_classes[1][$i % 2] ?> rounded-pill"><?= currency_format($currency, $sum) ?></span>
                                                    <?php $i++; ?>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($financial_data[$year]['attraction'])) : ?>
                                                <?php $i = 0; ?>
                                                <?php foreach ($financial_data[$year]['attraction'] as $currency => $amounts) : ?>
                                                    <?php $sum = array_sum($amounts);
                                                    $total[$currency] += $sum; ?>
                                                    <span class="badge bg-<?= $color_classes[2][$i % 2] ?> rounded-pill"><?= currency_format($currency, $sum) ?></span>
                                                    <?php $i++; ?>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php $i = 0; ?>
                                            <?php foreach ($total as $currency => $sum) : ?>
                                                <?php if (0 < $sum) : ?>
                                                    <span class="badge bg-<?= $color_classes[3][$i % 2] ?> rounded-pill"><?= currency_format($currency, $sum) ?></span>
                                                    <?php $i++; ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </td>
                                    </tr>
                                    <?php unset($total); ?>
                                <?php endfor; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $this->endSection() ?>
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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/journey/port') ?>">Port</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2>Visits by Year</h2>
                        <table class="table table-sm table-hover table-striped">
                            <tbody>
                            <?php $c = 0; ?>
                            <?php for ($year = date('Y'); $year >= 2006; $year--): ?>
                                <tr>
                                    <td colspan="3" class="text-center"><h6><?= $year ?></h6></td>
                                </tr>
                                <?php if (isset($by_year[$year])): ?>
                                    <?php arsort($by_year[$year]) ?>
                                    <?php foreach ($by_year[$year] as $port_id => $count) : ?>
                                        <?php $color_set = $colors[$c % 4]; $c++; ?>
                                        <tr>
                                            <td style="width:60px;">
                                                <span class="flag-icon flag-icon-<?= strtolower($ports[$port_id]['country_code']) ?>"></span>
                                                <?= explode('</i> ', $modes[$ports[$port_id]['type']])[0] . '</i>' ?>
                                            </td>
                                            <td style="width:180px;"><b><?= $ports[$port_id]['name'] ?></b></td>
                                            <td>
                                                <?php if (10 < $count) : ?>
                                                    <span class="badge bg-<?= $color_set[0] ?> rounded-pill w-100">- <?= $count ?> -</span>
                                                <?php else : ?>
                                                    <?php $length = $count/10*100; ?>
                                                    <div class="progress" role="progressbar" aria-label="Count" aria-valuenow="<?= $length ?>" aria-valuemin="0" aria-valuemax="100">
                                                        <div class="progress-bar bg-<?= $color_set[0] ?>" style="width: <?= $length ?>%"><?= $count ?></div>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">- No data -</td>
                                    </tr>
                                <?php endif; ?>
                            <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2>Visits by Port</h2>
                        <table class="table table-sm table-hover table-striped">
                            <tbody>
                            <?php $c = 0; ?>
                            <?php foreach ($by_port as $port_id => $count) : ?>
                                <?php $color_set = $colors[$c%4]; $c++; ?>
                                <tr>
                                    <td style="width:60px;">
                                        <span class="flag-icon flag-icon-<?= strtolower($ports[$port_id]['country_code']) ?>"></span>
                                        <?= explode('</i> ', $modes[$ports[$port_id]['type']])[0] . '</i>' ?>
                                    </td>
                                    <td style="width:180px;"><b><?= $ports[$port_id]['name'] ?></b></td>
                                    <td>
                                        <?php if (20 < $count) : ?>
                                            <span class="badge bg-<?= $color_set[0] ?> rounded-pill w-100">- <?= $count ?> -</span>
                                        <?php else : ?>
                                            <?php $length = $count/20*100; ?>
                                            <div class="progress" role="progressbar" aria-label="Count" aria-valuenow="<?= $length ?>" aria-valuemin="0" aria-valuemax="100">
                                                <div class="progress-bar bg-<?= $color_set[0] ?>" style="width: <?= $length ?>%"><?= $count ?></div>
                                            </div>
                                        <?php endif; ?>
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
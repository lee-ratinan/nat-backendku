<?php
$layout = getenv('LAYOUT_FILE_OFFICE');
$layout = (!empty($layout) ? $layout : 'system/_layout_office');
$this->extend($layout);
?>
<?= $this->section('content') ?>
<?php $session = session(); ?>
    <style>
        th {min-width:185px;text-align:center;width:14.28%;}
    </style>
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
                        <h5>Part-Time Calendar</h5>
                        <div class="row">
                            <div class="col">
                                <label for="month">Month</label>
                                <input class="form-control" type="month" id="month" name="month" value="<?= $yyyymm ?>" min="2026-04" max="<?= date('Y-m', strtotime('+1 month')) ?>" />
                            </div>
                            <div class="col text-end">
                                <br/>
                                <button id="btn-change" class="btn btn-sm btn-primary">Change Month</button>
                            </div>
                        </div>
                        <hr/>
                        <p>Selected month: <b><?= $month ?></b></p>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>月</th>
                                    <th>火</th>
                                    <th>水</th>
                                    <th>木</th>
                                    <th>金</th>
                                    <th>土</th>
                                    <th>日</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <?php
                                    $i = 1;
                                    while ($i < $dow_first) {
                                        echo '<td class="bg-dark-subtle">-</td>';
                                        $i++;
                                    }
                                    for ($j = 1; $j <= $day_count; $j++) {
                                        if (isset($calendar[$j])) {
                                            echo '<td>';
                                            echo '<h4 class="float-end">' . $j . '</h4>';
                                            echo '<i class="fa-solid fa-clock fa-fw"></i> ' . $calendar[$j]['start'] . '<br/><i class="fa-solid fa-chevron-right fa-fw"></i> ' . $calendar[$j]['end'] . '<br/>';
                                            echo '<i class="fa-solid fa-minus fa-fw"></i> ' . number_format($calendar[$j]['hours'], 2) . 'h';
                                            if (0 < $calendar[$j]['break']) {
                                                echo ' + ' . number_format($calendar[$j]['break'], 2) . 'h br';
                                            }
                                            echo '<br/><i class="fa-solid fa-location fa-fw"></i> ' . $calendar[$j]['location'];
                                            echo '</td>';
                                        } else {
                                            echo '<td class="bg-secondary text-black"><h4 class="text-end">' . $j . '</h4><br/><br/><br/></td>';
                                        }
                                        if ($i == 7) {
                                            echo '</tr><tr>';
                                            $i = 1;
                                        } else {
                                            $i++;
                                        }
                                    }
                                    while ($i <= 7) {
                                        echo '<td class="bg-dark-subtle">-</td>';
                                        $i++;
                                    }
                                    ?>
                                </tr>
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
            $('#btn-change').click(function () {
                let month = $('#month').val();
                document.location.href = '<?= base_url($session->locale . '/office/employment/part-time/calendar') ?>/' + month;
            });
        });
    </script>
<?php $this->endSection() ?>
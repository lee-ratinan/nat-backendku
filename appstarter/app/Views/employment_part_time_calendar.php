<?php
$layout = getenv('LAYOUT_FILE_OFFICE');
$layout = (!empty($layout) ? $layout : 'system/_layout_office');
$this->extend($layout);
?>
<?= $this->section('content') ?>
<?php $session = session(); ?>
    <style>
        th {min-width:150px;text-align:center;width:14.28%;}
        td.working-day {
            /* Change your test colors here */
            --bg-color: transparent;     /* Base cell color */
            --event-color: #525c6c;  /* Stripe color #323c4c */
            /* Test positions (e.g., 9:00 to 15:00 -> 37.5% to 62.5%) */
            --start-pct: 37.5%;
            --end-pct: 62.5%;
            background: linear-gradient(
                to bottom,
                var(--bg-color) 0% var(--start-pct),
                var(--event-color) var(--start-pct) var(--end-pct),
                var(--bg-color) var(--end-pct) 100%
            );
        }
        td.today h4 {
            background-color: #f00;
            color: #fff;
            border-radius: 50%;
        }
        h4 {
            height: 1.5rem;
            line-height: 1.5rem;
            width: 2rem;
            text-align: center;
            margin-bottom: 0;
        }
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
                        <h5 class="card-title">Part-Time Calendar</h5>
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
                                            echo '<td class="working-day" data-date="' . $calendar[$j]['date'] . '" style="--start-pct: ' . $calendar[$j]['start_pct'] . '%; --end-pct: ' . $calendar[$j]['end_pct'] . '%;" data-chk="'.substr($calendar[$j]['start'], 11).'">';
                                            echo '<h4 class="float-end">' . $j . '</h4>';
                                            echo '<i class="fa-solid fa-clock fa-fw"></i> ' . $calendar[$j]['start'] . '<br/><i class="fa-solid fa-chevron-right fa-fw"></i> ' . $calendar[$j]['end'] . '<br/>';
                                            echo '<i class="fa-solid fa-minus fa-fw"></i> ' . hour_format($calendar[$j]['hours']);
                                            if (0 < $calendar[$j]['break']) {
                                                echo ' + ' . hour_format($calendar[$j]['break']);
                                            }
                                            echo '<br/><i class="fa-solid fa-location fa-fw"></i> ' . $calendar[$j]['location'];
                                            echo '</td>';
                                        } else {
                                            echo '<td class="bg-secondary text-black" data-date="' . $yyyymm . '-' . str_pad($j, 2, '0', STR_PAD_LEFT) . '"><h4 class="float-end">' . $j . '</h4><br/><br/><br/></td>';
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
            const today = new Intl.DateTimeFormat('en-CA').format(new Date());
            console.log(today);
            $(`[data-date="${today}"]`).addClass('today');
            $('#btn-change').click(function () {
                let month = $('#month').val();
                document.location.href = '<?= base_url($session->locale . '/office/employment/part-time/calendar') ?>/' + month;
            });
        });
    </script>
<?php $this->endSection() ?>
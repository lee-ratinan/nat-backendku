<?php
$layout = getenv('LAYOUT_FILE_OFFICE');
$layout = (!empty($layout) ? $layout : 'system/_layout_office');
$this->extend($layout);
?>
<?= $this->section('content') ?>
    <?php $session = session(); ?>
    <div class="pagetitle">
        <h1><?= lang('System.dashboard.welcome', [$session->display_name]) ?></h1>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2>Daily Affirmation</h2>
                        <hr/>
                        <h3 class="text-center my-5"><i class="fa-solid fa-quote-left"></i> <?= get_daily_affirmation() ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2>Add Health Activity</h2>
                        <a href="<?= base_url($session->locale . '/office/health/activity') ?>" class="btn btn-primary">View All</a>
                        <a href="<?= base_url($session->locale . '/office/health/activity/new/ejac') ?>" class="btn btn-primary"><i class="fa-solid fa-plus-circle"></i> Add New</a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2>Upcoming Holidays</h2>
                        <?php
                        if (empty($holidays)) {
                            echo '<p>No upcoming holidays.</p>';
                        } else {
                            $this_date = '';
                            foreach ($holidays as $holiday) {
                                if ($this_date != $holiday['holiday_date']) {
                                    $this_date = $holiday['holiday_date'];
                                    echo '<h5 class="mt-1 mb-0"><i class="fa-regular fa-calendar-check"></i> ' . date(DATE_FORMAT_UI, strtotime($holiday['holiday_date'])) . '</h5>';
                                }
                                echo '<div class="mb-2">' . ($holiday['holiday_date'] != $holiday['holiday_date_to'] ? 'to ' . date(DATE_FORMAT_UI, strtotime($holiday['holiday_date_to'])) . ': ' : '');
                                echo ('XV' == $holiday['country_code'] ? 'Vacation' : '<span class="flag-icon flag-icon-' . strtolower($holiday['country_code']) . '"></span> ' . $countries[$holiday['country_code']]['common_name']) . ' - <b>' . $holiday['holiday_name'] . '</b>';
                                echo '</div>';
                            }
                        }
                        ?>
                        <a href="<?= base_url($session->locale . '/office/journey/holiday') ?>" class="btn btn-primary">View All</a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2>YTD Income</h2>
                        <?php if (empty($ytd_currencies)) : ?>
                            <p>No income data available.</p>
                        <?php else : ?>
                            <?php foreach ($ytd_currencies as $ccy) : ?>
                                <h4 class="mt-2 mb-0 text-center"><?= $ccy ?></h4>
                                <div class="row mb-2">
                                    <div class="col-6 text-center">
                                        <h5>Subtotal</h5>
                                        <?= currency_format($ccy, $ytd_totals[$ccy]['subtotal']) ?>
                                    </div>
                                    <div class="col-6 text-center">
                                        <h5>Total</h5>
                                        <?= currency_format($ccy, $ytd_totals[$ccy]['total']) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <a href="<?= base_url($session->locale . '/office/employment/salary') ?>" class="btn btn-primary">View All</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {});
    </script>
<?php $this->endSection() ?>
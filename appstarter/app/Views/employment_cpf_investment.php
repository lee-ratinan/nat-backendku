<?php
$layout = getenv('LAYOUT_FILE_OFFICE');
$layout = (!empty($layout) ? $layout : 'system/_layout_office');
$this->extend($layout);
?>
<?= $this->section('content') ?>
<?php $session = session(); ?>
    <style>
        #chartdiv {
            width: 100%;
            height: 500px;
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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/employment/cpf') ?>">CPF</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body pt-3">
                        <h5 class="card-title"><i class="fa-solid fa-piggy-bank fa-fw me-3"></i> <?= $page_title ?></h5>
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <h3>Total Investment Deducted:</h3>
                                <h2 class="text-center"><?= currency_format('SGD', $total_investment_deducted) ?></h2>
                                <p class="text-center">From TC INV</p>
                            </div>
                            <div class="col-12 col-md-4">
                                <h3>Latest Value:</h3>
                                <h2 class="text-center"><?= currency_format('SGD', $latest_inv_value) ?></h2>
                                <p class="text-center">
                                    <?php
                                    $profit = $latest_inv_value - $total_investment_deducted;
                                    if ($profit < 0) {
                                        echo '<span class="text-danger">Loss: ' . currency_format('SGD', $profit) . '</span>';
                                    } else {
                                        echo '<span class="text-success">Profit: ' . currency_format('SGD', $profit) . '</span>';
                                    }
                                    echo '<br/>as of ' . date(DATE_FORMAT_UI, $latest_date/1000);
                                    ?>
                                </p>
                            </div>
                            <div class="col-12 col-md-4">
                                <h3>Total Fees</h3>
                                <h2 class="text-center"><?= currency_format('SGD', $total_fees) ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body pt-3">
                        <h5 class="card-title">History</h5>
                        <h6>Add Snapshot</h6>
                        <?php
                        $fields = [
                            'snapshot_date',
                            'investment_value'
                        ];
                        foreach ($fields as $field) {
                            generate_form_field($field, $config[$field], null);
                        }
                        echo '<div class="text-end"><button class="btn btn-primary btn-sm" id="btn-save-snapshot"><i class="fa-solid fa-save"></i> Save</button></div>';
                        ?>

                        <script>
                            <?php echo generate_line_chart_script($chart_data, 'chartdiv', 'date', 'value');?>
                        </script>
                        <div id="chartdiv"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#btn-save-snapshot').click(function (e) {
                e.preventDefault();
                let snapshot_date = $('#snapshot_date').val(),
                    investment_value = $('#investment_value').val();
                if ('' === snapshot_date) {
                    toastr.warning('Please ensure snapshot date is filled.');
                    $('#snapshot_date').focus();
                    return;
                }
                if ('' === investment_value || 100 > investment_value) {
                    toastr.warning('Please ensure the value is filled and is positive.');
                    $('#investment_value').focus();
                    return;
                }
                $(this).prop('disabled', true);
                $.ajax({
                    url: '<?= base_url('en/office/employment/cpf/investment/snapshot') ?>',
                    type: 'post',
                    data: {
                        snapshot_date: snapshot_date,
                        investment_value: investment_value,
                    },
                    success: function (response) {
                        if ('success' === response.status) {
                            toastr.success(response.toast);
                            setTimeout(function () {window.location.reload();}, 5000);
                        } else {
                            let message = (response.toast ?? 'Sorry! Something went wrong. Please try again.');
                            toastr.error(message);
                            $('#btn-save-snapshot').prop('disabled', false);
                        }
                    },
                    error: function (xhr, status, error) {
                        let response = JSON.parse(xhr.responseText);
                        let error_message = (response.toast ?? 'Sorry! Something went wrong. Please try again.');
                        $('#btn-save-snapshot').prop('disabled', false);
                        toastr.error(error_message);
                    }
                });
            });
        });
    </script>
<?php $this->endSection() ?>
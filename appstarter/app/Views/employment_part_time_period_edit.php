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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/employment/part-time') ?>">Part Time Schedule</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= $page_title ?></h5>
                        <?php
                        $fields = [
                            'id',
                            'company_id',
                            'period_start',
                            'period_end',
                            'actual_hours',
                            'subtotal_income',
                            'income_deduction',
                            'total_income',
                            'average_hourly_income',
                            'google_drive_link',
                        ];
                        foreach ($fields as $field) {
                            generate_form_field($field, $config[$field], @$row[$field]);
                        }
                        ?>
                        <div class="text-end">
                            <button class="btn btn-primary btn-sm" id="btn-save"><i class="fa-solid fa-save"></i> Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <input type="hidden" id="total_hours" />
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#actual_hours, #subtotal_income').change(function () {
                let actual_hours = $('#actual_hours').val(),
                    subtotal_income = $('#subtotal_income').val();
                actual_hours = Number.parseFloat(actual_hours);
                subtotal_income = Number.parseFloat(subtotal_income);
                if (0.0 < subtotal_income && 0.0 < actual_hours) {
                    let average_hourly_income = (subtotal_income / actual_hours).toFixed(2);
                    $('#average_hourly_income').val(average_hourly_income);
                }
            });
            $('#subtotal_income, #income_deduction').change(function () {
                let subtotal_income = $('#subtotal_income').val(),
                    income_deduction = $('#income_deduction').val();
                subtotal_income = Number.parseFloat(subtotal_income);
                income_deduction = Number.parseFloat(income_deduction);
                if (0.0 < subtotal_income && 0.0 < income_deduction) {
                    let total_income = (subtotal_income - income_deduction).toFixed(2);
                    $('#total_income').val(total_income);
                }
            });
            $('#btn-save').click(function (e) {
                e.preventDefault();
                let ids = ['company_id', 'period_start', 'period_end',];
                for (let i = 0; i < ids.length; i++) {
                    if ('' === $('#' + ids[i]).val()) {
                        toastr.warning('Please ensure all mandatory fields are filled.');
                        $('#' + ids[i]).focus();
                        return;
                    }
                }
                $(this).prop('disabled', true);
                $.ajax({
                    url: '<?= base_url('en/office/employment/part-time/period/edit') ?>',
                    type: 'post',
                    data: {
                        'id': '<?= $id ?>',
                        'company_id': $('#company_id').val(),
                        'period_start': $('#period_start').val(),
                        'period_end': $('#period_end').val(),
                        'actual_hours': $('#actual_hours').val(),
                        'subtotal_income': $('#subtotal_income').val(),
                        'income_deduction': $('#income_deduction').val(),
                        'total_income': $('#total_income').val(),
                        'average_hourly_income': $('#average_hourly_income').val(),
                        'google_drive_link': $('#google_drive_link').val(),
                    },
                    success: function (response) {
                        if ('success' === response.status) {
                            toastr.success(response.toast);
                            setTimeout(function () {window.location.href = response.redirect;}, 5000);
                        } else {
                            let message = (response.toast ?? 'Sorry! Something went wrong. Please try again.');
                            toastr.error(message);
                            $('#btn-save').prop('disabled', false);
                        }
                    },
                    error: function (xhr, status, error) {
                        let response = JSON.parse(xhr.responseText);
                        let error_message = (response.toast ?? 'Sorry! Something went wrong. Please try again.');
                        $('#btn-save').prop('disabled', false);
                        toastr.error(error_message);
                    }
                });
            });
        });
    </script>
<?php $this->endSection() ?>
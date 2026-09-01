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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/employment/salary') ?>">Salary</a></li>
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
                            'company_id',
                            'pay_date',
                            'tax_year',
                            'tax_country_code',
                            'payment_method',
                            'payment_currency',
                            'pay_type',
                            'base_amount',
                            'allowance_amount',
                            'training_amount',
                            'overtime_amount',
                            'adjustment_amount',
                            'bonus_amount',
                            'subtotal_amount',
                            'social_security_amount',
                            'us_tax_fed_amount',
                            'us_tax_state_amount',
                            'us_tax_city_amount',
                            'us_tax_med_ee_amount',
                            'us_tax_oasdi_ee_amount',
                            'th_tax_amount',
                            'sg_tax_amount',
                            'au_tax_amount',
                            'claim_amount',
                            'provident_fund_amount',
                            'total_amount',
                            'payment_details',
                            'google_drive_link'
                        ];
                        foreach ($fields as $field) {
                            generate_form_field($field, $config[$field], @$salary[$field]);
                        }
                        ?>
                        <div class="text-end">
                            <button class="btn btn-primary btn-sm" id="btn-save-salary"><i class="fa-solid fa-save"></i> Save</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ('edit' == $mode) : ?>
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Salary Details</h5>
                            <table class="table table-sm table-borderless">
                                <?php foreach ($fields as $field) : ?>
                                    <tr>
                                        <td class="text-end"><?= $config[$field]['label'] ?></td>
                                        <td>
                                            <?php
                                            echo match ($field) {
                                                'company_id', 'payment_method', 'pay_type' => $config[$field]['options'][$salary[$field]],
                                                'tax_country_code' => lang('ListCountries.countries.' . $salary[$field] . '.common_name'),
                                                'pay_date' => date(DATE_FORMAT_UI, strtotime($salary[$field])),
                                                'google_drive_link' => (empty($salary[$field]) ? '-' : '<a href="' . $salary[$field] . '" target="_blank">Click</a><br>'),
                                                'tax_year', 'payment_currency', 'payment_details' => (empty($salary[$field]) ? '-' : $salary[$field]),
                                                default => currency_format($salary['payment_currency'], $salary[$field] ?? 0),
                                            };
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let getFloat = function (value) {
                const val = value.trim();
                return val === '' ? 0 : parseFloat(val);
            };
            $('#base_amount, #allowance_amount, #training_amount, #overtime_amount, #adjustment_amount, #bonus_amount').change(function () {
                let base_amount = getFloat($('#base_amount').val()),
                    allowance_amount = getFloat($('#allowance_amount').val()),
                    training_amount = getFloat($('#training_amount').val()),
                    overtime_amount = getFloat($('#overtime_amount').val()),
                    adjustment_amount = getFloat($('#adjustment_amount').val()),
                    bonus_amount = getFloat($('#bonus_amount').val());
                $('#subtotal_amount').val(base_amount+allowance_amount+training_amount+overtime_amount+adjustment_amount+bonus_amount);
            });
            $('#social_security_amount, #us_tax_fed_amount, #us_tax_state_amount, #us_tax_city_amount, #us_tax_med_ee_amount, #us_tax_oasdi_ee_amount, #th_tax_amount, #sg_tax_amount, #au_tax_amount, #claim_amount, #provident_fund_amount').change(function () {
                let social_security_amount = getFloat($('#social_security_amount').val()),
                    us_tax_fed_amount = getFloat($('#us_tax_fed_amount').val()),
                    us_tax_state_amount = getFloat($('#us_tax_state_amount').val()),
                    us_tax_city_amount = getFloat($('#us_tax_city_amount').val()),
                    us_tax_med_ee_amount = getFloat($('#us_tax_med_ee_amount').val()),
                    us_tax_oasdi_ee_amount = getFloat($('#us_tax_oasdi_ee_amount').val()),
                    th_tax_amount = getFloat($('#th_tax_amount').val()),
                    sg_tax_amount = getFloat($('#sg_tax_amount').val()),
                    au_tax_amount = getFloat($('#au_tax_amount').val()),
                    claim_amount = getFloat($('#claim_amount').val()),
                    provident_fund_amount = getFloat($('#provident_fund_amount').val()),
                    subtotal_amount = getFloat($('#subtotal_amount').val());
                $('#total_amount').val(subtotal_amount+social_security_amount+us_tax_fed_amount+us_tax_state_amount+us_tax_city_amount+us_tax_med_ee_amount+us_tax_oasdi_ee_amount+th_tax_amount+sg_tax_amount+au_tax_amount+claim_amount+provident_fund_amount);
            });
            $('#btn-save-salary').click(function (e) {
                e.preventDefault();
                let ids = ['company_id', 'pay_date', 'tax_year', 'tax_country_code', 'payment_method', 'payment_currency', 'pay_type', 'subtotal_amount', 'total_amount', 'payment_details', 'google_drive_link'];
                for (let i = 0; i < ids.length; i++) {
                    if ('' === $('#' + ids[i]).val()) {
                        toastr.warning('Please ensure all mandatory fields are filled.');
                        $('#' + ids[i]).focus();
                        return;
                    }
                }
                $(this).prop('disabled', true);
                $.ajax({
                    url: '<?= base_url('en/office/employment/salary/edit') ?>',
                    type: 'post',
                    data: {
                        mode: '<?= $mode ?>',
                        id: <?= $salary['id'] ?? '0' ?>,
                        <?php foreach ($fields as $field) : ?>
                        <?= $field ?>: $('#<?= $field ?>').val(),
                        <?php endforeach; ?>
                    },
                    success: function (response) {
                        if ('success' === response.status) {
                            toastr.success(response.toast);
                            setTimeout(function () {window.location.href = response.redirect;}, 5000);
                        } else {
                            let message = (response.toast ?? 'Sorry! Something went wrong. Please try again.');
                            toastr.error(message);
                            $('#btn-save-salary').prop('disabled', false);
                        }
                    },
                    error: function (xhr, status, error) {
                        let response = JSON.parse(xhr.responseText);
                        let error_message = (response.toast ?? 'Sorry! Something went wrong. Please try again.');
                        $('#btn-save-salary').prop('disabled', false);
                        toastr.error(error_message);
                    }
                });
            });
        });
    </script>
<?php $this->endSection() ?>
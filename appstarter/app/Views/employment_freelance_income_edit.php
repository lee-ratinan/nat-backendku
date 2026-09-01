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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/employment/freelance') ?>">Freelance</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/employment/freelance-income') ?>">Freelance Income</a></li>
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
                            'project_id',
                            'pay_date',
                            'payment_method',
                            'payment_currency',
                            'base_amount',
                            'deduction_amount',
                            'claim_amount',
                            'subtotal_amount',
                            'tax_amount',
                            'total_amount',
                            'payment_details',
                            'google_drive_link',
                        ];
                        foreach ($fields as $field) {
                            generate_form_field($field, $config[$field], @$income[$field]);
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
                        <h5 class="card-title">Payment Details</h5>
                        <table class="table table-sm table-borderless">
                            <?php foreach ($fields as $field) : ?>
                                <tr>
                                    <td class="text-end"><?= $config[$field]['label'] ?></td>
                                    <td>
                                        <?php
                                        switch ($field) {
                                            case 'pay_date':
                                                echo date(DATE_FORMAT_UI, strtotime($income[$field]));
                                                break;
                                            case 'payment_method':
                                                echo $config[$field]['options'][$income[$field]];
                                                break;
                                            case 'google_drive_link':
                                                echo (empty($income[$field]) ? '-' : '<a href="' . $income[$field] . '" target="_blank">Click</a><br>');
                                                break;
                                            case 'payment_currency':
                                            case 'payment_details':
                                                echo $income[$field];
                                                break;
                                            default:
                                                echo currency_format($income['payment_currency'], $income[$field]);
                                                break;
                                        }
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
            $('#base_amount, #deduction_amount, #claim_amount').change(function () {
                let base_amount = getFloat($('#base_amount').val()),
                    deduction_amount = getFloat($('#deduction_amount').val()),
                    claim_amount = getFloat($('#claim_amount').val());
                $('#subtotal_amount').val(base_amount + deduction_amount + claim_amount);
            });
            $('#tax_amount').change(function () {
                let subtotal_amount = getFloat($('#subtotal_amount').val()),
                    tax_amount = getFloat($('#tax_amount').val());
                $('#total_amount').val(subtotal_amount + tax_amount);
            });
            $('#btn-save-salary').click(function (e) {
                e.preventDefault();
                let ids = ['project_id', 'pay_date', 'payment_method', 'payment_currency', 'base_amount', 'deduction_amount', 'claim_amount', 'subtotal_amount', 'tax_amount', 'total_amount', 'payment_details', 'google_drive_link'];
                for (let i = 0; i < ids.length; i++) {
                    if ('' === $('#' + ids[i]).val()) {
                        toastr.warning('Please ensure all mandatory fields are filled.');
                        $('#' + ids[i]).focus();
                        return;
                    }
                }
                $(this).prop('disabled', true);
                $.ajax({
                    url: '<?= base_url('en/office/employment/freelance-income/edit') ?>',
                    type: 'post',
                    data: {
                        mode: '<?= $mode ?>',
                        id: <?= $income['id'] ?? '0' ?>,
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
                            $('#btn-save-project').prop('disabled', false);
                        }
                    },
                    error: function (xhr, status, error) {
                        let response = JSON.parse(xhr.responseText);
                        let error_message = (response.toast ?? 'Sorry! Something went wrong. Please try again.');
                        $('#btn-save-project').prop('disabled', false);
                        toastr.error(error_message);
                    }
                });
            });
        });
    </script>
<?php $this->endSection() ?>
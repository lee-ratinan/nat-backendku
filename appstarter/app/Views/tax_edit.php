<?php
$layout = getenv('LAYOUT_FILE_OFFICE');
$layout = (!empty($layout) ? $layout : 'system/_layout_office');
$this->extend($layout);
?>
<?= $this->section('content') ?>
<?php $session = session(); ?>
<?php
$tax_department = [
    'AU' => 'ATO: Australian Taxation Office',
    'SG' => 'IRAS: Inland Revenue Authority of Singapore',
    'TH' => 'RD: Revenue Department',
    'US' => 'IRS: Internal Revenue Service',
];
?>
    <div class="pagetitle">
        <h1><?= $page_title ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/dashboard') ?>"><?= lang('System.dashboard.page_title') ?></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/tax') ?>">Tax</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tax</h5>
                        <?php if ('edit' == $mode) : ?>
                            <h6><?= $tax_department[$tax_year['country_code']] ?></h6>
                            <div class="text-end mb-3">
                                <div class="btn btn-sm btn-outline-primary" id="edit-tax-year"><i class="fa-solid fa-edit fa-fw me-2"></i> Edit Tax Year</div>
                            </div>
                        <?php endif; ?>
                        <div class="mb-5" <?= ('edit' == $mode ? 'style="display:none;"' : '') ?> id="edit-tax-year-form">
                            <?php
                            generate_form_field('tax_year', $ty_config['tax_year'], @$tax_year['tax_year']);
                            generate_form_field('country_code', $ty_config['country_code'], @$tax_year['country_code']);
                            generate_form_field('google_drive_link', $ty_config['google_drive_link'], @$tax_year['google_drive_link']);
                            generate_form_field('currency_code', $ty_config['currency_code'], @$tax_year['currency_code']);
                            generate_form_field('total_income', $ty_config['total_income'], @$tax_year['total_income']);
                            generate_form_field('taxable_income', $ty_config['taxable_income'], @$tax_year['taxable_income']);
                            generate_form_field('final_tax_amount', $ty_config['final_tax_amount'], @$tax_year['final_tax_amount']);
                            generate_form_field('taxpayer_id', $ty_config['taxpayer_id'], @$tax_year['taxpayer_id']);
                            ?>
                            <div class="text-end">
                                <button class="btn btn-sm btn-outline-primary" id="submit-form"><i class="fa-solid fa-save fa-fw me-2"></i> Save Tax Year</button>
                            </div>
                        </div>
                        <div class="mb-5" id="edit-tax-year-info">
                            <?php if ('edit' == $mode) : ?>
                                <a class="btn btn-outline-primary btn-sm float-end" href="<?= $tax_year['google_drive_link'] ?>" target="_blank"><i class="fa-brands fa-google-drive"></i></a>
                                <h6>Tax Record</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        Taxpayer Name<h4><?= $taxpayer['taxpayer_name'] ?></h4>
                                    </div>
                                    <div class="col-md-4">
                                        <?= $taxpayer['taxpayer_id_key'] ?><h4><?= $taxpayer['taxpayer_id_value'] ?></h4>
                                    </div>
                                    <div class="col-6 col-md-2">
                                        Filing Status<br><h4><?= $taxpayer['filing_status'] ?></h4>
                                    </div>
                                    <div class="col-6 col-md-2">
                                        Citizenship Status<br><h4><?= $taxpayer['citizenship_status'] ?></h4>
                                    </div>
                                    <div class="col-12">
                                        Address<br><h4><?= $taxpayer['taxpayer_address'] ?></h4>
                                    </div>
                                    <div class="col-md-4">
                                        Total Income
                                        <h4><?= currency_format($tax_year['currency_code'], $tax_year['total_income']) ?></h4>
                                    </div>
                                    <div class="col-md-4">
                                        Taxable Income
                                        <h4><?= currency_format($tax_year['currency_code'], $tax_year['taxable_income']) ?></h4>
                                    </div>
                                    <div class="col-md-4">
                                        Tax Amount
                                        <h4><?= currency_format($tax_year['currency_code'], $tax_year['final_tax_amount']) ?></h4>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if ('edit' == $mode) : ?>
                            <h6 class="card-title">Details</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <tbody>
                                    <?php foreach ($tax_records as $category => $values) : ?>
                                        <tr>
                                            <td rowspan="<?= (count($values) + 1) ?>" style="min-width:85px"><?= ucfirst($category) ?></td>
                                            <?php $sum = 0.00; ?>
                                            <?php foreach ($values as $row) : ?>
                                                <td style="min-width:50px"><a class="btn btn-outline-primary btn-sm" href="<?= base_url($session->locale . '/office/tax/record/edit/' . $tax_id_for_link . '/' . ($row['new_id'])) ?>"><i class="fa-solid fa-edit"></i></a></td>
                                                <td style="min-width:200px"><?= $row['tax_description'] ?></td>
                                                <td style="min-width:120px" class="text-end"><?= currency_format($tax_year['currency_code'], $row['money_amount']) ?></td>
                                                <td style="min-width:300px"><?= $row['item_notes'] ?></td>
                                                <?php $sum += $row['money_amount']; ?>
                                            </tr>
                                            <tr>
                                            <?php endforeach; ?>
                                            <?php if ('record' == $category && 0 > $sum) { $sum = 0.00; } ?>
                                            <td colspan="2" class="text-end">TOTAL</td>
                                            <td class="text-end"><?= currency_format($tax_year['currency_code'], $sum) ?></td>
                                            <td></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <a class="btn btn-outline-primary btn-sm" href="<?= base_url($session->locale . '/office/tax/record/edit/create/' . $tax_id_for_link) ?>"><i class="fa-solid fa-plus"></i> Add more record</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#edit-tax-year').click(function () {
                $('#edit-tax-year-form').slideToggle();
                $('#edit-tax-year-info').slideToggle();
            });
            $('#submit-form').click(function (e) {
                e.preventDefault();
                let id = '<?= $tax_year['id'] ?? 0 ?>',
                    mode = '<?= $mode ?>',
                    tax_year = $('#tax_year').val(),
                    country_code = $('#country_code').val(),
                    google_drive_link = $('#google_drive_link').val(),
                    currency_code = $('#currency_code').val(),
                    total_income = $('#total_income').val(),
                    taxable_income = $('#taxable_income').val(),
                    final_tax_amount = $('#final_tax_amount').val(),
                    taxpayer_id = $('#taxpayer_id').val();
                if ('' === tax_year || '' === country_code || '' === currency_code || '' === taxpayer_id) {
                    toastr.info('Please fill in the required fields.');
                    return false;
                }
                $.ajax({
                        url: '<?= base_url($session->locale . '/office/tax/edit') ?>',
                        type: 'post',
                        data: {
                            id: id,
                            mode: mode,
                            tax_year: tax_year,
                            country_code: country_code,
                            google_drive_link: google_drive_link,
                            currency_code: currency_code,
                            total_income: total_income,
                            taxable_income: taxable_income,
                            final_tax_amount: final_tax_amount,
                            taxpayer_id: taxpayer_id
                        },
                        success: function (response) {
                            if ('success' === response.status) {
                                toastr.success(response.toast);
                                setTimeout(function () {
                                    window.location.href = response.url;
                                }, 1000);
                            } else {
                                toastr.error(response.toast ?? 'Failed to save transport.');
                            }
                        },
                        error: function (xhr, status, error) {
                            let response = JSON.parse(xhr.responseText);
                            let error_message = (response.toast ?? '<?= lang('System.status_message.generic_error') ?>');
                            toastr.error(error_message);
                        }
                    }
                );
            });
        });
    </script>
<?php $this->endSection() ?>
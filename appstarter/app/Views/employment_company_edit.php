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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/employment') ?>">Employment</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <?php if ('edit' == $mode) : ?>
                            <img class="img-thumbnail mb-3" src="<?= base_url('file/company-' . $company['company_slug'] . '.png') ?>" alt="<?= $company['company_legal_name'] ?>" />
                        <?php endif; ?>
                        <h5 class="card-title"><?= $page_title ?></h5>
                        <?php
                        $fields = [
                            'company_legal_name',
                            'company_trade_name',
                            'company_slug',
                            'company_other_names',
                            'company_address',
                            'company_country_code',
                            'company_hq_country_code',
                            'company_currency_code',
                            'company_website',
                            'company_details',
                            'company_registration',
                            'company_color',
                            'employment_start_date',
                            'employment_end_date',
                            'position_titles'
                        ];
                        foreach ($fields as $field) {
                            generate_form_field($field, $config[$field], @$company[$field]);
                        }
                        ?>
                        <div class="text-end">
                            <button class="btn btn-primary btn-sm" id="btn-save-company"><i class="fa-solid fa-save"></i> Save</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ('edit' == $mode) : ?>
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Company Details</h5>
                        <table class="table table-sm table-borderless">
                            <?php foreach ($fields as $field) : ?>
                            <tr>
                                <td class="text-end"><?= $config[$field]['label'] ?></td>
                                <td>
                                    <?php
                                    switch ($field) {
                                        case 'company_other_names':
                                            echo ($company[$field] ? str_replace(';', '<br>', $company[$field]) : '-');
                                            break;
                                        case 'company_country_code':
                                        case 'company_hq_country_code':
                                            echo lang('ListCountries.countries.'. $company[$field] . '.common_name');
                                            break;
                                        case 'company_website':
                                            $websites = explode(';', $company[$field]);
                                            foreach ($websites as $website) {
                                                echo '<a href="' . $website . '" target="_blank">' . $website . '</a><br>';
                                            }
                                            break;
                                        case 'company_color':
                                            echo '<span class="badge rounded-pill px-5" style="background-color:' . $company[$field] . '"> ' . $company[$field] . ' </div>';
                                            break;
                                        case 'employment_start_date':
                                        case 'employment_end_date':
                                            echo (empty($company[$field]) || '0000-00-00' == $company[$field]) ? '-' : date(DATE_FORMAT_UI, strtotime($company[$field]));
                                            break;
                                        default:
                                            echo $company[$field];
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
            $('#company_trade_name').change(function () {
                let trade_name = (($(this).val()).trim()).replace(/\s{2,}/g, ' '); // TRIM and REPLACE SPACES
                $(this).val(trade_name);
                $('#company_slug').val(trade_name.toLowerCase().replace(/\s/g, '-').replace(/[^a-zA-Z0-9\-]/g, ''));
            });
            $('#btn-save-company').click(function (e) {
                e.preventDefault();
                let ids = ['company_slug', 'company_legal_name', 'company_trade_name', 'company_address', 'company_country_code', 'company_hq_country_code', 'company_currency_code', 'company_website', 'company_details', 'company_registration', 'company_color', 'employment_start_date', 'position_titles'];
                for (let i = 0; i < ids.length; i++) {
                    if ('' === $('#' + ids[i]).val()) {
                        toastr.warning('Please ensure all mandatory fields are filled.');
                        $('#' + ids[i]).focus();
                        return;
                    }
                }
                let website = $('#company_website').val();
                let website_regex = /^(https?:\/\/)?([\w\-]+\.)+[\w\-]{2,}(\/\S*)?$/i;
                if (!website_regex.test(website)) {
                    toastr.warning('The website URL is invalid.');
                    $('#company_website').focus();
                    return;
                }
                $(this).prop('disabled', true);
                $.ajax({
                    url: '<?= base_url('en/office/employment/company/edit') ?>',
                    type: 'post',
                    data: {
                        mode: '<?= $mode ?>',
                        id: <?= $company['id'] ?? '0' ?>,
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
                            $('#btn-save-company').prop('disabled', false);
                        }
                    },
                    error: function (xhr, status, error) {
                        let response = JSON.parse(xhr.responseText);
                        let error_message = (response.toast ?? 'Sorry! Something went wrong. Please try again.');
                        $('#btn-save-company').prop('disabled', false);
                        toastr.error(error_message);
                    }
                });
            });
        });
    </script>
<?php $this->endSection() ?>
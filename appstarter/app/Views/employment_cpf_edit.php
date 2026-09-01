<?php
$layout = getenv('LAYOUT_FILE_OFFICE');
$layout = (!empty($layout) ? $layout : 'system/_layout_office');
$this->extend($layout);
?>
<?= $this->section('content') ?>
<?php $session = session(); ?>
    <style>
        .text-oa{color:#437271!important;}
        .bg-oa{background-color:#437271!important;color:#222!important;}
        .text-sa{color:#DFB670!important;}
        .bg-sa{background-color:#DFB670!important;color:#222!important;}
        .text-ma{color:#7D9ADE!important;}
        .bg-ma{background-color:#7D9ADE!important;color:#222!important;}
    </style>
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
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= $page_title ?></h5>
                        <?php
                        $max_company_id = 0;
                        $fields         = [
                            'transaction_date',
                            'transaction_code',
                            '---',
                            'ordinary_previous',
                            '+',
                            'ordinary_amount',
                            '=',
                            'ordinary_balance',
                            '---',
                            'special_previous',
                            '+',
                            'special_amount',
                            '=',
                            'special_balance',
                            '---',
                            'medisave_previous',
                            '+',
                            'medisave_amount',
                            '=',
                            'medisave_balance',
                            '---',
                            'account_previous',
                            '+',
                            'transaction_amount',
                            '=',
                            'account_balance',
                            '---',
                            'previous_con',
                            'contribution_month',
                            'company_id',
                            '---',
                            'staff_previous',
                            '+',
                            'staff_contribution',
                            '=',
                            'staff_ytd',
                            '---',
                            'company_previous',
                            '+',
                            'company_match',
                            '=',
                            'company_ytd',
                        ];
                        if ('new' == $mode) {
                            $cpf['ordinary_previous']  = $cpf_latest['ordinary_balance'];
                            $cpf['ordinary_amount']    = '0.0';
                            $cpf['ordinary_balance']   = $cpf_latest['ordinary_balance'];
                            $cpf['special_previous']   = $cpf_latest['special_balance'];
                            $cpf['special_amount']     = '0.0';
                            $cpf['special_balance']    = $cpf_latest['special_balance'];
                            $cpf['medisave_previous']  = $cpf_latest['medisave_balance'];
                            $cpf['medisave_amount']    = '0.0';
                            $cpf['medisave_balance']   = $cpf_latest['medisave_balance'];
                            $cpf['account_previous']   = $cpf_latest['account_balance'];
                            $cpf['transaction_amount'] = '0.0';
                            $cpf['account_balance']    = $cpf_latest['account_balance'];
                            $cpf['staff_previous']     = $cpf_last_con['staff_ytd'];
                            $cpf['staff_contribution'] = '0.0';
                            $cpf['staff_ytd']          = $cpf_last_con['staff_ytd'];
                            $cpf['company_previous']   = $cpf_last_con['company_ytd'];
                            $cpf['company_match']      = '0.0';
                            $cpf['company_ytd']        = $cpf_last_con['company_ytd'];
                            $cpf['previous_con']       = substr($cpf_last_con['contribution_month'], 0, 4);
                            $all_company_ids           = array_keys($config['company_id']['options']);
                            $max_company_id            = max($all_company_ids);
                            $config['ordinary_previous'] = [
                                'type'     => 'text',
                                'label'    => '<span class="badge bg-oa rounded-pill">Previous Balance</span>',
                                'readonly' => true,
                            ];
                            $config['special_previous']  = [
                                'type'     => 'text',
                                'label'    => '<span class="badge bg-sa rounded-pill">Previous Balance</span>',
                                'readonly' => true,
                            ];
                            $config['medisave_previous'] = [
                                'type'     => 'text',
                                'label'    => '<span class="badge bg-ma rounded-pill">Previous Balance</span>',
                                'readonly' => true,
                            ];
                            $config['account_previous']  = [
                                'type'     => 'text',
                                'label'    => '<span class="badge bg-success rounded-pill">Previous Balance</span>',
                                'readonly' => true,
                            ];
                            $config['previous_con']   = [
                                'type'     => 'text',
                                'label'    => 'Previous Contribution Year',
                                'readonly' => true
                            ];
                            $config['staff_previous']   = [
                                'type'     => 'text',
                                'label'    => '<span class="badge bg-warning rounded-pill">Previous Staff Contribution YTD</span>',
                                'readonly' => true,
                            ];
                            $config['company_previous'] = [
                                'type'     => 'text',
                                'label'    => '<span class="badge bg-danger rounded-pill">Previous Company Match YTD</span>',
                                'readonly' => true,
                            ];
                            echo '<div class="row">';
                            $separators = [
                                'Ordinary Account',
                                'Special Account',
                                'MediSave Account',
                                'All Accounts',
                                'Contribution Data',
                                'My Contribution',
                                'Company Match'
                            ];
                            $counter = 0;
                            foreach ($fields as $field) {
                                if ('---' == $field) {
                                    echo '<div class="text-center mb-3">' . @$separators[$counter] . '</div>';
                                    $counter++;
                                } else if ('+' == $field || '=' == $field) {
                                    echo '';
                                } else {
                                    echo '<div class="col-4">';
                                    generate_form_field($field, $config[$field], @$cpf[$field]);
                                    echo '</div>';
                                }
                            }
                            echo '</div>';
                            echo '<div class="text-end"><button class="btn btn-primary btn-sm" id="btn-save-cpf"><i class="fa-solid fa-save"></i> Save</button></div>';
                        } else {
                            echo '<table class="table table-sm table-borderless">';
                            foreach ($fields as $field) {
                                if (in_array($field, ['ordinary_previous', 'special_previous', 'medisave_previous', 'account_previous', 'staff_previous', 'company_previous', 'previous_con', 'staff_previous', 'company_previous', '---', '+', '='])) {
                                    continue;
                                }
                                if ('CON' != $cpf['transaction_code'] && 'contribution_month' == $field) {
                                    break;
                                } else if ('contribution_month' == $field) {
                                    echo '<tr><td colspan="2" class="text-center"><b>--- CONTRIBUTION ---</b></td></tr>';
                                }
                                echo '<tr><td class="text-end">' . $config[$field]['label'] . '</td><td>';
                                switch ($field) {
                                    case 'transaction_code':
                                    case 'company_id':
                                        echo (empty($cpf[$field]) ? '-' : $config[$field]['options'][$cpf[$field]]);
                                        break;
                                    case 'transaction_date':
                                        echo date(DATE_FORMAT_UI, strtotime($cpf[$field]));
                                        break;
                                    case 'contribution_month':
                                        echo (empty($cpf[$field]) ? '-' : date(MONTH_FORMAT_UI, strtotime($cpf[$field] . '-01')));
                                        break;
                                    default:
                                        echo (empty($cpf[$field]) ? '-' : currency_format('SGD', $cpf[$field]));
                                        break;
                                }
                                echo '</td></tr>';
                            }
                            echo '</table>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#staff_previous').attr('data-value', $('#staff_previous').val());
            $('#company_previous').attr('data-value', $('#company_previous').val());
            let getFloat = function (value) {
                const val = value.trim();
                return val === '' ? 0 : parseFloat(val);
            };
            let calculateBalance = function () {
                let ordinary_amount = getFloat($('#ordinary_amount').val()),
                    special_amount = getFloat($('#special_amount').val()),
                    medisave_amount = getFloat($('#medisave_amount').val()),
                    total_amount = ordinary_amount + special_amount + medisave_amount,
                    previous_balance = getFloat($('#account_previous').val());
                $('#transaction_amount').val(total_amount);
                $('#account_balance').val(previous_balance+total_amount);
            };
            // LISTENER
            $('#transaction_code').change(function () {
                let transaction_code = $(this).val();
                if ('CON' === transaction_code) {
                    $('#contribution_month').val('<?= date('Y-m', strtotime('previous month')) ?>').prop('readonly', false);
                    $.each($('#company_id option'), function (index, option) {
                        let val = $(this).val();
                        if ('-1' === val) { // Not a company, so disable it
                            $(this).prop('disabled', true);
                        } else { // This is a company - enable it for CON
                            $(this).prop('disabled', false);
                            if ('<?= $max_company_id ?>' === val) { // This is the max company, so select it
                                $(this).prop('selected', true);
                            }
                        }
                    });
                    $('#staff_contribution, #staff_ytd, #company_match, #company_ytd').prop('readonly', false);
                } else {
                    $('#contribution_month').val('0000-00').prop('readonly', true);
                    $('#company_id option[value=-1]').prop('disabled', false).prop('selected', true);
                    $.each($('#company_id option'), function (index, option) {
                        let val = $(this).val();
                        if ('-1' !== val) { // It's the company, disable it because this is not CON
                            $(this).prop('disabled', true);
                        }
                    });
                    $('#staff_contribution, #staff_ytd, #company_match, #company_ytd').prop('readonly', true);
                }
            });
            $('#ordinary_amount').change(function () {
                let ordinary_previous = getFloat($('#ordinary_previous').val()),
                    ordinary_amount = getFloat($('#ordinary_amount').val());
                $('#ordinary_balance').val(ordinary_amount+ordinary_previous);
                calculateBalance();
            });
            $('#special_amount').change(function () {
                let special_previous = getFloat($('#special_previous').val()),
                    special_amount = getFloat($('#special_amount').val());
                $('#special_balance').val(special_amount+special_previous);
                calculateBalance();
            });
            $('#medisave_amount').change(function () {
                let medisave_previous = getFloat($('#medisave_previous').val()),
                    medisave_amount = getFloat($('#medisave_amount').val());
                $('#medisave_balance').val(medisave_amount + medisave_previous);
                calculateBalance();
            });
            $('#contribution_month').change(function () {
                let contribution_month = $(this).val(),
                    regex = /\d{4}-\d{2}/;
                if (!regex.test(contribution_month)) {
                    $(this).val('');
                    toastr.warning('The contribution month is invalid.');
                    return;
                }
                let prev_con_yr = $('#previous_con').val(),
                    this_con_yr = contribution_month.slice(0, 4);
                if (prev_con_yr !== this_con_yr) { // start a new contribution year
                    $('#staff_previous').val('0.00');
                    $('#company_previous').val('0.00');
                } else {
                    $('#staff_previous').val($('#staff_previous').attr('data-value'));
                    $('#company_previous').val($('#company_previous').attr('data-value'));
                }
            });
            $('#staff_contribution').change(function () {
                let staff_contribution = getFloat($('#staff_contribution').val()),
                    staff_previous = getFloat($('#staff_previous').val());
                $('#staff_ytd').val(staff_contribution+staff_previous);
            });
            $('#company_match').change(function () {
                let company_match = getFloat($('#company_match').val()),
                    company_previous = getFloat($('#company_previous').val());
                $('#company_ytd').val(company_match+company_previous);
            });
            $('#btn-save-cpf').click(function (e) {
                e.preventDefault();
                let ids = ['transaction_date', 'transaction_code', 'ordinary_amount', 'ordinary_balance', 'special_amount', 'special_balance', 'medisave_amount', 'medisave_balance', 'transaction_amount', 'account_balance', 'contribution_month', 'company_id', 'staff_contribution', 'staff_ytd', 'company_match', 'company_ytd'];
                for (let i = 0; i < ids.length; i++) {
                    if ('' === $('#' + ids[i]).val()) {
                        toastr.warning('Please ensure all mandatory fields are filled.');
                        $('#' + ids[i]).focus();
                        return;
                    }
                }
                $(this).prop('disabled', true);
                $.ajax({
                    url: '<?= base_url('en/office/employment/cpf/edit') ?>',
                    type: 'post',
                    data: {
                        <?php foreach ($fields as $field) : ?>
                        <?php if (in_array($field, ['---', '+', '='])) continue; ?>
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
                            $('#btn-save-cpf').prop('disabled', false);
                        }
                    },
                    error: function (xhr, status, error) {
                        let response = JSON.parse(xhr.responseText);
                        let error_message = (response.toast ?? 'Sorry! Something went wrong. Please try again.');
                        $('#btn-save-cpf').prop('disabled', false);
                        toastr.error(error_message);
                    }
                });
            });
        });
    </script>
<?php $this->endSection() ?>
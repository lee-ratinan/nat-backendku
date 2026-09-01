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
                        <h5 class="card-title"><?= $page_title ?></h5>
                        <div class="card-text">
                            <h6>Taxable Income</h6>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <table class="table table-sm table-hover">
                                        <tr>
                                            <td><label for="monthly-salary">MONTHLY SALARY</label></td>
                                            <td><input class="form-control form-control-sm" type="number" id="monthly-salary" value="7500" /></td>
                                        </tr>
                                        <tr>
                                            <td><label for="annual-salary">ANNUAL SALARY</label></td>
                                            <td><input class="form-control form-control-sm" type="number" id="annual-salary" value="90000" /></td>
                                        </tr>
                                        <tr>
                                            <td><label for="tax-country">TAX COUNTRY</label></td>
                                            <td>
                                                <select class="form-select form-select-sm" id="tax-country">
                                                    <optgroup label="SOUTHEAST ASIA">
                                                        <option value="SG">SINGAPORE (S$)</option>
                                                        <option value="TH">THAILAND (฿)</option>
                                                        <option value="MY" disabled>MALAYSIA (RM)</option>
                                                    </optgroup>
                                                    <optgroup label="OCEANIA">
                                                        <option value="AU">AUSTRALIA (A$)</option>
                                                        <option value="NZ" disabled>NEW ZEALAND (NZ$)</option>
                                                    </optgroup>
                                                    <optgroup label="NORTH AMERICA">
                                                        <option value="US" disabled>UNITED STATES / CALIFORNIA ($)</option>
                                                        <option value="US" disabled>UNITED STATES / NEW YORK ($)</option>
                                                        <option value="US" disabled>CANADA / ONTARIO (C$)</option>
                                                        <option value="US" disabled>CANADA / BRITISH COLUMBIA (C$)</option>
                                                    </optgroup>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-outline-primary" id="btn-tax-calculate">Calculate</button>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-12 col-md-6" id="target-calculation-area">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#monthly-salary').on('change', function () {
                $('#annual-salary').val($(this).val() * 12);
            });
            $('#annual-salary').on('change', function () {
                $('#monthly-salary').val($(this).val() / 12);
            });
            $('#btn-tax-calculate').click(function (e) {
                e.preventDefault();
                let annual_salary = $('#annual-salary').val(),
                    monthly_salary = $('#monthly-salary').val(),
                    tax_country   = $('#tax-country').val();
                $.ajax({
                    url: '<?= base_url($session->locale . '/office/tax/calculator') ?>',
                    type: 'POST',
                    data: {
                        tax_country: tax_country,
                        monthly_income: monthly_salary,
                        annual_income: annual_salary
                    },
                    success: function (response) {
                        $('#target-calculation-area').html(response);
                    }
                });
            });
        });
    </script>
<?php $this->endSection() ?>
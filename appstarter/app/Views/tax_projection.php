<?php
$layout = getenv('LAYOUT_FILE_OFFICE');
$layout = (!empty($layout) ? $layout : 'system/_layout_office');
$this->extend($layout);
?>
<?= $this->section('content') ?>
<?php $session = session(); ?>
    <script src="<?= base_url('assets/vendor/amcharts5/index.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/amcharts5/xy.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/amcharts5/themes/Animated.js') ?>"></script>
    <style>#chartdiv {width: 100%;height: 500px;}</style>
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
                        <h5 class="card-title">Tax Projection</h5>
                        <div class="row">
                            <div class="col-12">
                                <h6>Income Data</h6>
                                <table class="table table-sm table-hover">
                                    <tr>
                                        <td><label for="min-salary">INCOME RANGE</label></td>
                                        <td><input class="form-control form-control-sm" type="number" id="min-income" value="3000" /></td>
                                        <td><label for="max-salary">TO</label></td>
                                        <td><input class="form-control form-control-sm" type="number" id="max-income" value="15000" /></td>
                                    </tr>
                                    <tr>
                                        <td><label for="step">STEP</label></td>
                                        <td><input class="form-control form-control-sm" type="number" id="step" value="1000" /></td>
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
                                        <td colspan="3"></td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-outline-primary" id="btn-tax-calculate">Calculate</button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12" id="target-calculation-area">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#btn-tax-calculate').click(function (e) {
                e.preventDefault();
                $.ajax({
                    url: '<?= base_url($session->locale . '/office/tax/projection') ?>',
                    type: 'POST',
                    data: {
                        tax_country: $('#tax-country').val(),
                        min_income: $('#min-income').val(),
                        max_income: $('#max-income').val(),
                        step: $('#step').val()
                    },
                    success: function (response) {
                        $('#target-calculation-area').html(response);
                    }
                });
            });
        });
    </script>
<?php $this->endSection() ?>
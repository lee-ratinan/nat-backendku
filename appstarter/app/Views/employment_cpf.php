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
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body pt-3">
                        <div class="text-end">
                            <a class="btn btn-outline-primary btn-sm" href="<?= base_url($session->locale . '/office/employment/cpf/statement') ?>"><i class="fa-solid fa-file-pdf"></i> Annual Statement</a>
                            <a class="btn btn-outline-primary btn-sm ms-3" href="<?= base_url($session->locale . '/office/employment/cpf/create') ?>"><i class="fa-solid fa-plus-circle"></i> New CPF</a>
                        </div>
                        <h5 class="card-title"><i class="fa-solid fa-piggy-bank fa-fw me-3"></i> <?= $page_title ?></h5>
                        <div class="row mb-3">
                            <div class="col-6 col-md-4">
                                <label for="transaction_code" class="form-label">Transaction Code</label><br>
                                <select class="form-select form-select-sm" id="transaction_code">
                                    <option value="">All</option>
                                    <option value="CON">Contribution (CON)</option>
                                    <option value="CSL">CareShield Life (CSL)</option>
                                    <option value="DPS">Dependants’ Protection Scheme (DPS)</option>
                                    <option value="INT">Interest (INT)</option>
                                    <option value="INV">Investment (INV)</option>
                                    <option value="MSL">MediShield Life (MSL)</option>
                                    <option value="SUP">ElderShield Supplement / CareShield Life Supplement (SUP)</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-4">
                                <label for="company_id" class="form-label">Company</label><br>
                                <select class="form-select form-select-sm" id="company_id">
                                    <option value="">All</option>
                                    <?php foreach ($companies as $country_code => $company): ?>
                                        <option value="<?= $company['id'] ?>"><?= $company['company_legal_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6 col-md-4">
                                <label for="year" class="form-label">Year</label><br>
                                <select class="form-select form-select-sm" id="year">
                                    <option value="">All</option>
                                    <option value="<?= date('Y') ?>" selected><?= date('Y') ?></option>
                                    <?php for ($year = date('Y')-1; $year > 2019; $year--): ?>
                                        <option value="<?= $year ?>"><?= $year ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <small>Remarks: When filtered by contribution (CON) and year, it will filter by the month the contribution was made for, otherwise, the year will be applied to the transaction date.</small>
                            </div>
                            <div class="col-12 text-end">
                                <button id="btn-reset" class="btn btn-sm btn-outline-primary">Reset</button>
                                <button id="btn-filter" class="btn btn-sm btn-primary">Filter</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hover">
                                <thead>
                                <tr>
                                    <th rowspan="2"></th>
                                    <th rowspan="2" style="min-width:100px">Date</th>
                                    <th rowspan="2" style="min-width:80px">Code</th>
                                    <th colspan="2" class="bg-oa">ORDINARY ACCOUNT</th>
                                    <th colspan="2" class="bg-sa">SPACIAL ACCOUNT</th>
                                    <th colspan="2" class="bg-ma">MEDISAVE ACCOUNT</th>
                                    <th colspan="2">TOTAL</th>
                                    <th colspan="2">For</th>
                                    <th colspan="4">CONTRIBUTION</th>
                                </tr>
                                <tr>
                                    <th style="min-width:150px">Amount</th>
                                    <th style="min-width:150px">Balance</th>
                                    <th style="min-width:150px">Amount</th>
                                    <th style="min-width:150px">Balance</th>
                                    <th style="min-width:150px">Amount</th>
                                    <th style="min-width:150px">Balance</th>
                                    <th style="min-width:150px">Transaction Total</th>
                                    <th style="min-width:150px">CPF Balance</th>
                                    <th style="min-width:100px">Month</th>
                                    <th style="min-width:150px">Company</th>
                                    <th style="min-width:150px">Staff Contribution</th>
                                    <th style="min-width:150px">Staff YTD</th>
                                    <th style="min-width:150px">Company Match</th>
                                    <th style="min-width:150px">Company YTD</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const table = $('table').DataTable({
                processing: true,
                serverSide: true,
                fixedHeader: true,
                searching: false,
                ordering: false,
                pageLength: 50,
                ajax: {
                    url: '<?= base_url($session->locale . '/office/employment/cpf') ?>',
                    type: 'POST',
                    data: function (d) {
                        d.year = $('#year').val();
                        d.transaction_code = $('#transaction_code').val();
                        d.company_id = $('#company_id').val();
                    },
                },
                order: [[1, 'asc']],
                columnDefs: [
                    {className: 'text-end', targets: [3,4,5,6,7,8,9,10,13,14,15,16] },
                    {className: 'text-oa', targets: [3,4] },
                    {className: 'text-sa', targets: [5,6] },
                    {className: 'text-ma', targets: [7,8] },
                    {className: 'text-success', targets: [9,10]},
                    {className: 'text-warning', targets: [13,14]},
                    {className: 'text-danger', targets: [15,16]},
                ],
                fixedColumns: {start:2},
                scrollX: true,
            });
            $('#btn-filter').on('click', function () {
                table.ajax.reload();
            });
            $('#btn-reset').on('click', function () {
                $('#year').val('');
                $('#transaction_code').val('');
                $('#company_id').val('');
                table.ajax.reload();
            });
        });
    </script>
<?php $this->endSection() ?>
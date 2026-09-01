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
                            <a class="btn btn-outline-primary btn-sm" href="<?= base_url($session->locale . '/office/employment/salary/create') ?>"><i class="fa-solid fa-plus-circle"></i> New Salary</a>
                        </div>
                        <h5 class="card-title"><i class="fa-solid fa-dollar-sign fa-fw me-3"></i> <?= $page_title ?></h5>
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-4">
                                <label for="company_id" class="form-label">Company</label><br>
                                <select class="form-select form-select-sm" id="company_id">
                                    <option value="">All</option>
                                    <?php foreach ($companies as $country_code => $group): ?>
                                        <optgroup label="<?= lang('ListCountries.countries.' . $country_code . '.common_name') ?>">
                                            <?php foreach ($group as $company) : ?>
                                                <option value="<?= $company['id'] ?>"><?= $company['name'] ?></option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6 col-md-4">
                                <label for="currency_code" class="form-label">Currency</label><br>
                                <select class="form-select form-select-sm" id="currency_code">
                                    <option value="">All</option>
                                    <?php foreach ($currencies as $code): ?>
                                        <option value="<?= $code ?>"><?= $code ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6 col-md-4">
                                <label for="year" class="form-label">Year</label><br>
                                <select class="form-select form-select-sm" id="year">
                                    <option value="">All</option>
                                    <?php for ($year = date('Y'); $year > 2009; $year--): ?>
                                        <option value="<?= $year ?>"><?= $year ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col text-end">
                                <button id="btn-reset" class="btn btn-sm btn-outline-primary">Reset</button>
                                <button id="btn-filter" class="btn btn-sm btn-primary">Filter</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hover">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th><i class="fa-solid fa-file-pdf"></i></th>
                                    <th style="min-width:100px">Pay Date</th>
                                    <th style="min-width:180px">Company</th>
                                    <th style="min-width:100px">Tax Year</th>
                                    <th style="min-width:125px">Country</th>
                                    <th style="min-width:100px">Method</th>
                                    <th style="min-width:100px">Currency</th>
                                    <th style="min-width:100px">Type</th>
                                    <th style="min-width:150px">Base Salary</th>
                                    <th style="min-width:150px">Allowance</th>
                                    <th style="min-width:150px">Training</th>
                                    <th style="min-width:150px">OT</th>
                                    <th style="min-width:150px">Adjustment</th>
                                    <th style="min-width:150px">Bonus</th>
                                    <th style="min-width:150px">Subtotal</th>
                                    <th style="min-width:150px">Social Security</th>
                                    <th style="min-width:150px"><span class="flag-icon flag-icon-us"></span> FED</th>
                                    <th style="min-width:150px"><span class="flag-icon flag-icon-us"></span> STATE</th>
                                    <th style="min-width:150px"><span class="flag-icon flag-icon-us"></span> CITY</th>
                                    <th style="min-width:150px"><span class="flag-icon flag-icon-us"></span> MED EE</th>
                                    <th style="min-width:150px"><span class="flag-icon flag-icon-us"></span> OASDI EE</th>
                                    <th style="min-width:150px"><span class="flag-icon flag-icon-th"></span> Tax</th>
                                    <th style="min-width:150px"><span class="flag-icon flag-icon-sg"></span> Tax</th>
                                    <th style="min-width:150px"><span class="flag-icon flag-icon-au"></span> Tax</th>
                                    <th style="min-width:150px">Claim</th>
                                    <th style="min-width:150px">Provident Fund</th>
                                    <th style="min-width:150px">Total</th>
                                    <th style="min-width:225px">Details</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                <tr>
                                    <th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                                    <th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                                    <th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let serverFooter = [];
            const table = $('table').DataTable({
                processing: true,
                serverSide: true,
                fixedHeader: true,
                searching: false, // don't allow the search for this one
                pageLength: 25,
                fixedColumns: {start: 3},
                scrollX: true,
                ajax: {
                    url: '<?= base_url($session->locale . '/office/employment/salary') ?>',
                    type: 'POST',
                    data: function (d) {
                        d.currency_code = $('#currency_code').val();
                        d.company_id    = $('#company_id').val();
                        d.year          = $('#year').val();
                    },
                    dataSrc: function (json) {
                        serverFooter = json.footer;
                        return json.data;
                    }
                },
                order: [[2, 'desc']],
                columnDefs: [
                    {orderable: false, targets: [0,1]},
                    {className: 'text-end', targets: [9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27] }
                ],
                footerCallback: function () {
                    let api = this.api();
                    for (let i = 7; i <= 28; i++) {
                        api.column(i).footer().innerHTML = serverFooter[i];
                    }
                }
            });
            $('#btn-filter').on('click', function () {
                table.ajax.reload();
            });
            $('#btn-reset').on('click', function () {
                $('#currency_code').val('');
                $('#company_id').val('');
                $('#year').val('');
                table.ajax.reload();
            });
        });
    </script>
<?php $this->endSection() ?>
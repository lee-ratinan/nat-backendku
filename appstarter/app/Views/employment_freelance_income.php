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
                            <a class="btn btn-outline-primary btn-sm" href="<?= base_url($session->locale . '/office/employment/freelance-income/create') ?>"><i class="fa-solid fa-plus-circle"></i> New Income</a>
                        </div>
                        <h5 class="card-title"><i class="fa-solid fa-dollar-sign fa-fw me-3"></i> <?= $page_title ?></h5>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label for="company_id" class="form-label">Company</label><br>
                                <select class="form-select form-select-sm" id="company_id">
                                    <option value="">All</option>
                                    <?php foreach ($companies as $company_id => $company_name): ?>
                                        <option value="<?= $company_id ?>"><?= $company_name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6">
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
                                    <th style="min-width:140px">Project</th>
                                    <th style="min-width:100px">Company</th>
                                    <th style="min-width:100px">Pay Date</th>
                                    <th style="min-width:125px">Payment Method</th>
                                    <th style="min-width:80px">Currency</th>
                                    <th style="min-width:150px">Base Amount</th>
                                    <th style="min-width:150px">Deduction</th>
                                    <th style="min-width:150px">Claim</th>
                                    <th style="min-width:150px">Subtotal</th>
                                    <th style="min-width:150px">Tax Deduction</th>
                                    <th style="min-width:150px">Total</th>
                                    <th style="min-width:225px">Details</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
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
                pageLength: 50,
                fixedColumns: {start: 3},
                scrollX: true,
                ajax: {
                    url: '<?= base_url($session->locale . '/office/employment/freelance-income') ?>',
                    type: 'POST',
                    data: function (d) {
                        d.company_id    = $('#company_id').val();
                        d.year          = $('#year').val();
                    },
                    dataSrc: function (json) {
                        serverFooter = json.footer;
                        return json.data;
                    }
                },
                order: [[4, 'desc']],
                columnDefs: [
                    {orderable: false, targets: 0},
                    {className: 'text-end', targets: [7,8,9,10,11,12] }
                ],
                footerCallback: function () {
                    let api = this.api();
                    for (let i = 6; i <= 12; i++) {
                        api.column(i).footer().innerHTML = serverFooter[i];
                    }
                }
            });
            $('#btn-filter').on('click', function () {
                table.ajax.reload();
            });
            $('#btn-reset').on('click', function () {
                $('#company_id').val('');
                $('#project_id').val('');
                $('#year').val('');
                table.ajax.reload();
            });
        });
    </script>
<?php $this->endSection() ?>
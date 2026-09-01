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
                            <a class="btn btn-outline-primary btn-sm" href="<?= base_url($session->locale . '/office/employment/company/create') ?>"><i class="fa-solid fa-plus-circle"></i> New Company</a>
                        </div>
                        <h5 class="card-title"><i class="fa-solid fa-suitcase fa-fw me-3"></i> <?= $page_title ?></h5>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="country_code" class="form-label">Country</label><br>
                                <select class="form-select form-select-sm" id="country_code">
                                    <option value="">All</option>
                                    <?php foreach ($countries as $code): ?>
                                        <option value="<?= $code ?>"><?= lang('ListCountries.countries.' . $code . '.common_name') ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col">
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
                                    <td style="min-width:75px"></td>
                                    <th style="min-width:250px">Company</th>
                                    <th style="min-width:120px">Country</th>
                                    <th style="min-width:120px">Home Country</th>
                                    <th style="min-width:120px">Start</th>
                                    <th style="min-width:120px">Termination</th>
                                    <th style="min-width:200px">Positions</th>
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
                searching: true,
                pageLength:25,
                ajax: {
                    url: '<?= base_url($session->locale . '/office/employment/company') ?>',
                    type: 'POST',
                    data: function (d) {
                        d.country_code = $('#country_code').val();
                        d.year = $('#year').val();
                    }
                },
                order: [[5, 'desc']],
                columnDefs: [{orderable: false, targets: [0,2]}],
                fixedColumns: {start:2},
                scrollX: true,
            });
            $('#btn-filter').on('click', function () {
                table.ajax.reload();
            });
            $('#btn-reset').on('click', function () {
                $('#country_code').val('');
                $('#year').val('');
                $('#journey_status').val('');
                $('#mode_of_transport').val('');
                table.ajax.reload();
            });
        });
    </script>
<?php $this->endSection() ?>
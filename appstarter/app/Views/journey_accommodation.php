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
                        <h5 class="card-title"><i class="fa-solid fa-bed fa-fw me-3"></i> <?= $page_title ?></h5>
                        <div class="row mb-3 g-3">
                            <div class="col-6 col-md-4">
                                <label for="country_code" class="form-label">Country</label><br>
                                <select class="form-select form-select-sm" id="country_code">
                                    <option value="">All</option>
                                    <?php foreach ($countries as $code => $name): ?>
                                        <option value="<?= $code ?>"><?= $name['common_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6 col-md-4">
                                <label for="year" class="form-label">Year</label><br>
                                <select class="form-select form-select-sm" id="year">
                                    <option value="">All</option>
                                    <?php for ($year = date('Y'); $year >= 1989; $year--): ?>
                                        <option value="<?= $year ?>"><?= $year ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-6 col-md-4">
                                <label for="journey_status" class="form-label">Status</label><br>
                                <select class="form-select form-select-sm" id="journey_status">
                                    <option value="">All</option>
                                    <option value="as_planned">Confirmed</option>
                                    <option value="canceled">Canceled</option>
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
                                    <th>ID</th>
                                    <th style="min-width:120px;">Country</th>
                                    <th style="min-width:120px;">Check-in</th>
                                    <th style="min-width:120px;">Check-out</th>
                                    <th style="min-width:80px;">Nights</th>
                                    <th style="min-width:200px;">Hotel</th>
                                    <th style="min-width:120px;">Channel</th>
                                    <th style="min-width:100px;">Room Type</th>
                                    <th style="min-width:100px;">Breakfast</th>
                                    <th style="min-width:150px;">Price</th>
                                    <th style="min-width:150px;">Remarks</th>
                                    <th style="min-width:50px;">Link</th>
                                    <th>Status</th>
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
                pageLength: 50,
                ajax: {
                    url: '<?= base_url($session->locale . '/office/journey/accommodation') ?>',
                    type: 'POST',
                    data: function (d) {
                        d.country_code = $('#country_code').val();
                        d.year = $('#year').val();
                        d.journey_status = $('#journey_status').val();
                    },
                },
                ordering: false,
                columnDefs: [{orderable: false, targets: 0}],
                fixedColumns: {start:3},
                scrollX: true,
            });
            $('#btn-filter').on('click', function () {
                table.ajax.reload();
            });
            $('#btn-reset').on('click', function () {
                $('#country_code').val('');
                $('#year').val('');
                $('#journey_status').val('');
                table.ajax.reload();
            });
        });
    </script>
<?php $this->endSection() ?>
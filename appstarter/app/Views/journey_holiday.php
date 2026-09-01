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
                        <a class="btn btn-outline-primary btn-sm float-end ms-3" href="<?= base_url($session->locale . '/office/journey/holiday/create') ?>"><i class="fa-solid fa-plus-circle"></i> New Holiday</a>
                        <h5 class="card-title"><i class="fa-solid fa-ticket fa-fw me-3"></i> <?= $page_title ?></h5>
                        <div class="row mb-3 g-3">
                            <div class="col">
                                <label for="country_code" class="form-label">Country</label><br>
                                <select class="form-select form-select-sm" id="country_code">
                                    <option value="">All</option>
                                    <option value="XV">Vacation</option>
                                    <optgroup label="Australia">
                                        <option value="AU-ALL">Australia’s Holidays</option>
                                        <option value="AU-NAT">Australia National</option>
                                        <option value="AU-NSW">New South Wales</option>
                                        <option value="AU-QLD">Queensland</option>
                                        <option value="AU-VIC">Victoria</option>
                                        <option value="AU-WA">Western Australia</option>
                                    </optgroup>
                                    <optgroup label="Southeast Asia">
                                        <option value="SG">Singapore</option>
                                        <option value="TH">Thailand</option>
                                    </optgroup>
                                    <optgroup label="United States">
                                        <option value="US-ALL">All US Holidays</option>
                                        <option value="US-FED">US Federal</option>
                                        <option value="US-CA">California</option>
                                        <option value="US-IL">Illinois</option>
                                        <option value="US-NY">New York</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="col">
                                <label for="start" class="form-label">Start</label><br>
                                <input type="date" class="form-control form-control-sm" id="start" value="<?= date('Y') ?>-01-01" min="2025-01-01" />
                            </div>
                            <div class="col">
                                <label for="end" class="form-label">End</label><br>
                                <input type="date" class="form-control form-control-sm" id="end" value="<?= date('Y') ?>-12-31" min="2025-01-01" />
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
                                    <th style="min-width:120px;">Date</th>
                                    <th style="min-width:200px;">Detail</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="small">
                            For Singapore
                            <ul>
                                <li>The in-lieu is to be given if the holiday falls on Saturday,</li>
                                <li>The next working day will be a holiday if the holiday falls on Sunday.</li>
                            </ul>
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
                pageLength: -1,
                ajax: {
                    url: '<?= base_url($session->locale . '/office/journey/holiday') ?>',
                    type: 'POST',
                    data: function (d) {
                        d.country_code = $('#country_code').val();
                        d.start = $('#start').val();
                        d.end = $('#end').val();
                    },
                },
                ordering: false,
                paging: false
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
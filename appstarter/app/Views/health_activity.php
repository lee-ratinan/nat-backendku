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
                        <div class="row">
                            <div class="col-12">
                                <h5 class="card-title"><i class="fa-solid fa-spa fa-fw me-3"></i> <?= $page_title ?></h5>
                            </div>
                            <div class="col-12 text-end mb-3">
                                <?php foreach ($record_cate as $key => $type) : ?>
                                    <a class="btn btn-outline-primary btn-sm" href="<?= base_url($session->locale . '/office/health/activity/new/' . $key) ?>"><i class="fa-solid fa-plus-circle"></i> <?= $type ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="from" class="form-label">From</label><br>
                                <input type="date" class="form-control form-control-sm" id="from" value="<?= date(DATE_FORMAT_DB, strtotime('-60day')) ?>" min="2022-05-01" max="<?= date(DATE_FORMAT_DB, strtotime('+1day')) ?>">
                            </div>
                            <div class="col">
                                <label for="to" class="form-label">To</label><br>
                                <input type="date" class="form-control form-control-sm" id="to" value="<?= date(DATE_FORMAT_DB, strtotime('+1day')) ?>" min="2022-05-01" max="<?= date(DATE_FORMAT_DB, strtotime('+1day')) ?>">
                            </div>
                            <div class="col">
                                <label for="record_type" class="form-label">Record Type</label><br>
                                <select class="form-select form-select-sm" id="record_type">
                                    <option value="">All</option>
                                    <?php foreach ($record_types as $category => $types) : ?>
                                        <optgroup label="<?= $record_cate[$category] ?>">
                                            <option value="<?= $category ?>:"><?= $record_cate[$category] ?> / *</option>
                                            <?php foreach ($types as $type => $value) : ?>
                                                <option value="<?= $category . ':' . $type ?>"><?= $value ?></option>
                                            <?php endforeach; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col">
                                <label for="is_ejac" class="form-label">Reached?</label><br>
                                <select class="form-select form-select-sm" id="is_ejac">
                                    <option value="">All</option>
                                    <option value="Y">Yes</option>
                                    <option value="N">No</option>
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
                                    <th style="min-width:200px">Time</th>
                                    <th style="min-width:100px">Duration</th>
                                    <th style="min-width:100px">Time from PE</th>
                                    <th style="min-width:160px">Detail</th>
                                    <th style="min-width:100px">Reached?</th>
                                    <th style="min-width:180px">Spa</th>
                                    <th style="min-width:100px">Price</th>
                                    <th style="min-width:150px">Notes</th>
                                    <th style="min-width:150px">Location</th>
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
                    url: '<?= base_url($session->locale . '/office/health/activity') ?>',
                    type: 'POST',
                    data: function (d) {
                        d.from = $('#from').val();
                        d.to = $('#to').val();
                        d.record_type = $('#record_type').val();
                        d.is_ejac = $('#is_ejac').val();
                        d.event_location = $('#event_location').val();
                    }
                },
                order: [[0, 'desc']],
                columnDefs: [{orderable: false, targets: 0}],
                drawCallback: function () {
                    let DateTime = luxon.DateTime;
                    $('.utc-to-local-time').each(function () {
                        const utc = $(this).text();
                        if ('' !== utc) {
                            $(this).text(DateTime.fromISO(utc).toLocaleString(DateTime.DATETIME_MED));
                        } else {
                            $(this).text('-');
                        }
                    });
                },
            });
            $('#btn-filter').on('click', function () {
                table.ajax.reload();
            });
            $('#btn-reset').on('click', function () {
                $('#from').val('2022-05-01');
                $('#to').val('<?= date(DATE_FORMAT_DB) ?>');
                $('#record_type').val('');
                $('#is_ejac').val('');
                $('#event_location').val('');
                table.ajax.reload();
            });
        });
    </script>
<?php $this->endSection() ?>
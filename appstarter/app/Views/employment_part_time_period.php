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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/employment/part-time') ?>">Part Time Schedule</a></li>
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
                            <a class="btn btn-outline-primary btn-sm" href="<?= base_url($session->locale . '/office/employment/part-time/pay-period/edit/new') ?>"><i class="fa-solid fa-plus-circle"></i> New Period</a>
                        </div>
                        <h5 class="card-title">Part-Time Pay Period</h5>
                        <div class="row g-3 mb-3">
                            <div class="col">
                                <label for="start_date">Start Date</label><br/><input class="form-control" type="date" id="start_date" name="start_date" placeholder="Start Date" min="2026-04-01" max="<?= date('Y-m-d') ?>" />
                            </div>
                            <div class="col">
                                <label for="end_date">End Date</label><br/><input class="form-control" type="date" id="end_date" name="end_date" placeholder="End Date" min="2026-04-01" max="<?= date('Y-m-t', strtotime('+1 month')) ?>" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col text-end">
                                <button id="btn-reset" class="btn btn-sm btn-outline-primary">Reset</button>
                                <button id="btn-filter" class="btn btn-sm btn-primary">Filter</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-borderless table-striped table-hover dttable w-100">
                                <thead>
                                <tr>
                                    <th style="min-width:125px;"></th>
                                    <th style="min-width:125px;">Company</th>
                                    <th style="min-width:130px;">Start</th>
                                    <th style="min-width:130px;">End</th>
                                    <th style="min-width:100px;">Scheduled<br/>(hrs)</th>
                                    <th style="min-width:100px;">Recorded<br/>(hrs)</th>
                                    <th style="min-width:75px;" class="text-end">diff<br/>(hrs)</th>
                                    <th style="min-width:100px;">Subtotal<br/>($)</th>
                                    <th style="min-width:100px;">CPF<br/>($)</th>
                                    <th style="min-width:100px;">Total<br/>($)</th>
                                    <th style="min-width:100px;">Average<br/>($/hr)</th>
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
                                    <th class="text-end"></th>
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
            const table = $('.dttable').DataTable({
                processing: true,
                serverSide: true,
                fixedHeader: true,
                searching: false, // don't allow the search for this one
                pageLength: 50,
                scrollX: true,
                ajax: {
                    url: '<?= base_url($session->locale . '/office/employment/part-time/pay-period') ?>',
                    type: 'POST',
                    data: function (d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    },
                    dataSrc: function (json) {
                        serverFooter = json.footer;
                        return json.data;
                    }
                },
                order: [[2, 'desc']],
                footerCallback: function () {
                    let api = this.api();
                    for (let i = 0; i <= 8; i++) {
                        api.column(i).footer().innerHTML = serverFooter[i];
                    }
                }
            });
            $('#btn-filter').on('click', function () {
                table.ajax.reload();
            });
            $('#btn-reset').on('click', function () {
                $('#start_date').val('');
                $('#end_date').val('');
                $('#period_id').val('');
                table.ajax.reload();
            });
        });
    </script>
<?php $this->endSection() ?>
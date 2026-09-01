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
        <a class="btn btn-outline-primary mb-4" href="<?= base_url($session->locale . '/office/document/public-document/compiled-work') ?>" target="_blank">Compiled Work Experience</a>
    </div>
    <section class="section">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body pt-3">
                        <div class="text-end">
                            <a class="btn btn-outline-primary btn-sm" href="<?= base_url($session->locale . '/office/document/create') ?>"><i class="fa-solid fa-plus-circle"></i> New Document</a>
                        </div>
                        <h5 class="card-title"><i class="fa-solid fa-suitcase fa-fw me-3"></i> <?= $page_title ?></h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hover">
                                <thead>
                                <tr>
                                    <th rowspan="2" style="min-width:180px">Company</th>
                                    <th rowspan="2" style="min-width:180px">Document Title</th>
                                    <th rowspan="2" style="min-width:100px">Status</th>
                                    <th rowspan="2" style="min-width:150px">Created At</th>
                                    <th rowspan="2" style="min-width:150px">Updated At</th>
                                    <th rowspan="2" style="max-width:120px">Edit</th>
                                    <th colspan="4" class="text-center">View</th>
                                </tr>
                                <tr>
                                    <th style="max-width:120px">Public</th>
                                    <th style="max-width:120px">Anonymized</th>
                                    <th style="max-width:120px">Internal</th>
                                    <th style="max-width:120px">Draft</th>
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
                    url: '<?= base_url($session->locale . '/office/document') ?>',
                    type: 'POST'
                },
                order: [[2, 'desc']],
                columnDefs: [{orderable: false, targets: [3,4,5]}],
                fixedColumns: {start:1},
                scrollX: true,
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
        });
    </script>
<?php $this->endSection() ?>
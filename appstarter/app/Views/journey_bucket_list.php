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
                        <a class="btn btn-outline-primary btn-sm float-end ms-3" href="<?= base_url($session->locale . '/office/journey/bucket-list/create') ?>"><i class="fa-solid fa-plus-circle"></i> New Bucket</a>
                        <h5 class="card-title"><i class="fa-solid fa-bed fa-fw me-3"></i> <?= $page_title ?></h5>
                        <div class="row mb-3 g-3">
                            <div class="col-6">
                                <label for="category_code" class="form-label">Category</label><br>
                                <select class="form-select form-select-sm" id="category_code">
                                    <option value="">All</option>
                                    <?php foreach ($categories as $code => $name): ?>
                                        <option value="<?= $code ?>"><?= $name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="bucket_status" class="form-label">Status</label><br>
                                <select class="form-select form-select-sm" id="bucket_status">
                                    <option value="">All</option>
                                    <option value="Y">Did It!</option>
                                    <option value="N">Want</option>
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
                                    <th style="max-width:80px"></th>
                                    <th style="min-width:180px">Activity</th>
                                    <th style="min-width:120px">Category</th>
                                    <th style="min-width:120px">Where</th>
                                    <th style="min-width:120px">When</th>
                                    <th style="min-width:120px">Associated Trip</th>
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
                    url: '<?= base_url($session->locale . '/office/journey/bucket-list') ?>',
                    type: 'POST',
                    data: function (d) {
                        d.category_code = $('#category_code').val();
                        d.bucket_status = $('#bucket_status').val();
                    },
                },
                order: [[1, 'asc']],
                columnDefs: [{orderable: false, targets: [0,5]}],
                fixedColumns: {start:2},
                scrollX: true,
            });
            $('#btn-filter').on('click', function () {
                table.ajax.reload();
            });
            $('#btn-reset').on('click', function () {
                $('#category_code').val('');
                $('#bucket_status').val('');
                table.ajax.reload();
            });
        });
    </script>
<?php $this->endSection() ?>
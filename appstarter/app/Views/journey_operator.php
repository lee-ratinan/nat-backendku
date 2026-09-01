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
                        <a class="btn btn-outline-primary btn-sm float-end ms-3" href="<?= base_url($session->locale . '/office/journey/operator/create') ?>"><i class="fa-solid fa-plus-circle"></i> New Operator</a>
                        <h5 class="card-title"><i class="fa-solid fa-user-tie fa-fw me-3"></i> <?= $page_title ?></h5>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="mode_of_transport" class="form-label">Mode of Transport</label><br>
                                <select class="form-select form-select-sm" id="mode_of_transport">
                                    <option value="">All</option>
                                    <?php foreach ($modes as $key => $value): ?>
                                        <option value="<?= $key ?>"><?= $value ?></option>
                                    <?php endforeach; ?>
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
                                    <th style="width:5%"></th>
                                    <th style="width:5%">ID</th>
                                    <th style="width:20%">Mode of Transport</th>
                                    <th style="width:35%">Code</th>
                                    <th style="width:35%">Name</th>
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
                    url: '<?= base_url($session->locale . '/office/journey/operator') ?>',
                    type: 'POST',
                    data: function (d) {
                        d.mode_of_transport = $('#mode_of_transport').val();
                    }
                },
                order: [[4, 'asc']],
                columnDefs: [{orderable: false, targets: 0}],
            });
            $('#btn-filter').on('click', function () {
                table.ajax.reload();
            });
            $('#btn-reset').on('click', function () {
                $('#country_code').val('');
                $('#mode_of_transport').val('');
                table.ajax.reload();
            });
        });
    </script>
<?php $this->endSection() ?>
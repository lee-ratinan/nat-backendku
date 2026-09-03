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
                            <a class="btn btn-outline-primary btn-sm" href="<?= base_url($session->locale . '/office/anticipation/edit/0') ?>"><i class="fa-solid fa-plus-circle"></i> New Anticipation</a>
                        </div>
                        <h5 class="card-title"><?= $page_title ?></h5>
                        <div class="row g-3 mb-3">
                            <div class="col">
                                <label for="start_date">Start Date</label><br/><input class="form-control" type="date" id="start_date" name="start_date" placeholder="Start Date" min="2026-04-01" />
                            </div>
                            <div class="col">
                                <label for="end_date">End Date</label><br/><input class="form-control" type="date" id="end_date" name="end_date" placeholder="End Date" min="2026-04-01" />
                            </div>
                            <div class="col">
                                <label for="anticipation_category">Category</label><br/>
                                <select class="form-select" id="anticipation_category" name="anticipation_category">
                                    <option value="">All</option>
                                    <?php foreach ($categories as $id => $category): ?>
                                        <option value="<?= $id ?>"><?= $category ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col">
                                <label for="item_status">Status</label><br/>
                                <select class="form-select" id="item_status" name="item_status">
                                    <option value="">All</option>
                                    <?php foreach ($statuses as $id => $status): ?>
                                        <option value="<?= $id ?>"><?= $status ?></option>
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
                            <table class="table table-borderless table-striped table-hover dttable w-100">
                                <thead>
                                <tr>
                                    <th style="width:50px;"></th>
                                    <th style="min-width:150px;">Category</th>
                                    <th style="min-width:200px;">Title</th>
                                    <th style="min-width:150px;">Target</th>
                                    <th style="min-width:130px;">Priority</th>
                                    <th style="min-width:130px;">Status</th>
                                    <th style="min-width:130px;">Completion</th>
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
            const table = $('table').DataTable({
                processing: true,
                serverSide: true,
                fixedHeader: true,
                searching: false, // don't allow the search for this one
                pageLength: 50,
                scrollX: true,
                ajax: {
                    url: '<?= base_url($session->locale . '/office/anticipation') ?>',
                    type: 'POST',
                    data: function (d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.anticipation_category = $('#anticipation_category').val();
                        d.item_status = $('#item_status').val();
                    }
                },
                order: [[2, 'asc']]
            });
            $('#btn-filter').on('click', function () {
                table.ajax.reload();
            });
            $('#btn-reset').on('click', function () {
                $('#start_date').val('');
                $('#end_date').val('');
                $('#anticipation_category').val('');
                $('#item_status').val('');
                table.ajax.reload();
            });
        });
    </script>
<?php $this->endSection() ?>
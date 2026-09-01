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
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body pt-3">
                        <div class="text-end">
                            <a class="btn btn-outline-primary btn-sm" href="<?= base_url($session->locale . '/office/health/ooca/create') ?>"><i class="fa-solid fa-plus-circle"></i> New Record</a>
                        </div>
                        <h5 class="card-title"><i class="fa-solid fa-suitcase fa-fw me-3"></i> <?= $page_title ?></h5>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="year" class="form-label">Year</label><br>
                                <select class="form-select form-select-sm" id="year">
                                    <option value="">All</option>
                                    <?php for ($year = date('Y'); $year > 2020; $year--): ?>
                                        <option value="<?= $year ?>"><?= $year ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col text-end">
                                <button id="btn-reset" class="btn btn-sm btn-outline-primary">Reset</button>
                                <button id="btn-filter" class="btn btn-sm btn-primary"><i class="fa-solid fa-filter"></i> Filter</button>
                                <button id="btn-export" class="btn btn-sm btn-primary"><i class="fa-solid fa-file-export"></i> Export</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hover">
                                <thead>
                                <tr>
                                    <th style="width:60px"></th>
                                    <th style="width:60px"></th>
                                    <td style="min-width:150px"><i class="fa-solid fa-calendar-check"></i> วันที่</td>
                                    <td style="min-width:200px"><i class="fa-solid fa-user"></i> ชื่อผู้ให้คำปรึกษา</td>
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
        document.addEventListener('DOMContentLoaded', function() {
            const table = $('table').DataTable({
                processing: true,
                serverSide: true,
                fixedHeader: true,
                searching: true,
                pageLength: 25,
                ajax: {
                    url: '<?= base_url($session->locale . '/office/health/ooca') ?>',
                    type: 'POST',
                    data: function (d) {
                        d.year = $('#year').val();
                    }
                },
                order: [[2, 'desc']],
            });
            $('#btn-filter').on('click', function () {
                table.ajax.reload();
            });
            $('#btn-reset').on('click', function () {
                $('#year').val('');
                table.ajax.reload();
            });
            $('#btn-export').on('click', function () {
                let year = $('#year').val();
                if (year === '') {
                    toastr.warning('Please select year before export.');
                    return;
                }
                window.open('<?= base_url($session->locale . '/office/health/ooca/export/') ?>' + year);
            });
        });
    </script>
<?php $this->endSection() ?>
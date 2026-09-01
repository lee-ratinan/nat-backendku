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
                            <a class="btn btn-outline-primary btn-sm" href="<?= base_url($session->locale . '/office/employment/part-time/edit/new') ?>"><i class="fa-solid fa-plus-circle"></i> New Schedule</a>
                        </div>
                        <h5>Part-Time Schedule</h5>
                        <div class="row g-3 mb-3">
                            <div class="col">
                                <label for="start_date">Start Date</label><br/><input class="form-control" type="date" id="start_date" name="start_date" placeholder="Start Date" min="2026-04-01" max="<?= date('Y-m-d') ?>" />
                            </div>
                            <div class="col">
                                <label for="end_date">End Date</label><br/><input class="form-control" type="date" id="end_date" name="end_date" placeholder="End Date" min="2026-04-01" max="<?= date('Y-m-t', strtotime('+1 month')) ?>" />
                            </div>
                            <div class="col">
                                <label for="week_filter">Week of:</label><br/><input class="form-control" type="week" id="week_filter" name="week_filter" placeholder="Week of" min="2026-W15">
                            </div>
                            <div class="col">
                                <label for="period_id">Pay Period</label><br/>
                                <select class="form-control" id="period_id" name="period_id">
                                    <option value="">All</option>
                                    <?php foreach ($periods as $id => $period) : ?>
                                        <option value="<?= $id ?>"><?= $period ?></option>
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
                            <table class="table table-borderless table-striped table-hover dttable">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>Period</th>
                                    <th>Start</th>
                                    <th>End</th>
                                    <th>Scheduled (hrs)</th>
                                    <th>Break (hrs)</th>
                                    <th>Location</th>
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
                    url: '<?= base_url($session->locale . '/office/employment/part-time') ?>',
                    type: 'POST',
                    data: function (d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.period_id = $('#period_id').val();
                    },
                    dataSrc: function (json) {
                        serverFooter = json.footer;
                        return json.data;
                    }
                },
                order: [[2, 'desc']],
                footerCallback: function () {
                    let api = this.api();
                    for (let i = 0; i <= 5; i++) {
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
            $('#week_filter').on('change', function() {
                const weekValue = $(this).val(); // Example: "2026-W35"
                if (!weekValue) return;
                // 1. Split the string into Year and Week number
                const [year, week] = weekValue.split('-W').map(Number);
                // 2. Locate January 4th (always guaranteed to be in ISO Week 1)
                const jan4 = new Date(Date.UTC(year, 0, 4));
                const jan4Day = jan4.getUTCDay() === 0 ? 7 : jan4.getUTCDay();
                // 3. Calculate Monday of Week 1, then skip ahead to the selected week
                const monday = new Date(jan4.getTime());
                monday.setUTCDate(jan4.getUTCDate() - jan4Day + 1 + (week - 1) * 7);
                // 4. Calculate Sunday by adding 6 days to Monday
                const sunday = new Date(monday.getTime());
                sunday.setUTCDate(monday.getUTCDate() + 6);
                // 5. Format to readable YYYY-MM-DD strings
                const mondayFormatted = monday.toISOString().split('T')[0];
                const sundayFormatted = sunday.toISOString().split('T')[0];
                // 6. Use results
                $('#start_date').val(mondayFormatted);
                $('#end_date').val(sundayFormatted);
            });
        });
    </script>
<?php $this->endSection() ?>
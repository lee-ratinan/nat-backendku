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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/employment/freelance') ?>">Freelance</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <h4>Projects By Company/Client</h4>
                            <table class="table table-borderless table-striped table-hover dttable">
                                <thead>
                                <tr>
                                    <th style="min-width:150px">Company</th>
                                    <th style="min-width:150px">Client</th>
                                    <th style="min-width:200px">Project</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $count_by_company = [];
                                $count_by_client  = [];
                                $count_by_year    = [];
                                ?>
                                <?php foreach ($by_company as $company_name => $clients) : ?>
                                    <?php foreach ($clients as $client_name => $projects) : ?>
                                        <tr>
                                            <td><?= $company_name ?></td>
                                            <td><?= $client_name ?></td>
                                            <td><?= implode('<br>', $projects) ?></td>
                                            <?php
                                            $count_by_company[$company_name] = (isset($count_by_company[$company_name]) ? $count_by_company[$company_name] + count($projects) : count($projects));
                                            $count_by_client[$client_name]   = count($projects);
                                            ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <h4 class="mt-3">Project By Year</h4>
                            <table class="table table-borderless table-striped table-hover dttable">
                                <thead>
                                <tr>
                                    <th style="min-width:80px">Year</th>
                                    <th style="min-width:150px">Company</th>
                                    <th style="min-width:150px">Client</th>
                                    <th style="min-width:200px">Project</th>
                                    <th style="min-width:120px">Start</th>
                                    <th style="min-width:120px">End</th>
                                    <th style="min-width:100px">Duration</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($by_year as $year => $projects) : ?>
                                        <?php $count_by_year[$year] = count($projects); ?>
                                        <?php foreach ($projects as $project) : ?>
                                        <tr>
                                            <td><?= $year ?></td>
                                            <td><?= $project['company_name'] ?></td>
                                            <td><?= $project['client_name'] ?></td>
                                            <td><?= $project['project_title'] ?></td>
                                            <td><?= date(DATE_FORMAT_UI, strtotime($project['start_date'])) ?></td>
                                            <td><?= (empty($project['end_date']) ? '-' : date(DATE_FORMAT_UI, strtotime($project['end_date']))) ?></td>
                                            <td><?= $project['days'] ?> days</td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h4>Count By Company</h4>
                                <table class="table table-borderless table-striped table-hover">
                                    <tbody>
                                    <?php foreach ($count_by_company as $key => $count) : ?>
                                    <tr>
                                        <td style="width:150px"><?= $key ?> (<?= $count ?>)</td>
                                        <td><?= str_repeat('<i class="fa-solid fa-star text-warning"></i>', $count) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <h4 class="mt-3">Count By Year</h4>
                                <table class="table table-borderless table-striped table-hover">
                                    <tbody>
                                    <?php foreach ($count_by_year as $key => $count) : ?>
                                        <tr>
                                            <td style="width:150px"><?= $key ?> (<?= $count ?>)</td>
                                            <td><?= str_repeat('<i class="fa-solid fa-star text-warning"></i>', $count) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4>Count By Client</h4>
                                <table class="table table-borderless table-striped table-hover">
                                    <tbody>
                                    <?php foreach ($count_by_client as $key => $count) : ?>
                                        <tr>
                                            <td style="width:150px"><?= $key ?> (<?= $count ?>)</td>
                                            <td><?= str_repeat('<i class="fa-solid fa-star text-warning"></i>', $count) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const table = $('.dttable').DataTable({
                searching: true,
                pageLength: 25,
                order: [[0, 'asc']],
                scrollX: true,
            });
        });
    </script>
<?php $this->endSection() ?>
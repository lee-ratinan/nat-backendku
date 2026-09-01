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
                    <div class="card-body">
                        <h2 class="card-title"><i class="fa-solid fa-list"></i> COVID Vaccination Record</h2>
                        <p>Updated vaccination certification: <a href="https://drive.google.com/file/d/1IxZKkNjAhbo1aq0pd1hDheiBa3xw2T4W/view" target="_blank"><i class="fa-brands fa-google-drive"></i> View</a></p>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                <tr>
                                    <th style="min-width:100px;">Date</th>
                                    <th style="min-width:80px;">Details</th>
                                    <th style="min-width:200px;">Vaccines Information</th>
                                    <th style="min-width:150px;">Vaccination Center</th>
                                    <th style="min-width:100px;">Country</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Jul 17, 2021</td>
                                    <td>Dose 1/2</td>
                                    <td>COVID-19 MODERNA/SPIKEVAX ORIGINAL (A-COV)</td>
                                    <td>Punggol 21 CC<br>(Raffles Medical)</td>
                                    <td>Singapore</td>
                                </tr>
                                <tr>
                                    <td>Aug 14, 2021</td>
                                    <td>Dose 2/2</td>
                                    <td>COVID-19 MODERNA/SPIKEVAX ORIGINAL (A-COV)</td>
                                    <td>Punggol 21 CC<br>(Raffles Medical)</td>
                                    <td>Singapore</td>
                                </tr>
                                <tr>
                                    <td>Jan 14, 2022</td>
                                    <td>Booster 1</td>
                                    <td>COVID-19 MODERNA/SPIKEVAX ORIGINAL (A-COV)</td>
                                    <td>Punggol 21 CC<br>(Raffles Medical)</td>
                                    <td>Singapore</td>
                                </tr>
                                <tr>
                                    <td>Feb 4, 2023</td>
                                    <td>Booster 2</td>
                                    <td>COVID-19 MODERNA/SPIKEVAX BIVALENT ORIGINAL/OMICRON (A-COV)</td>
                                    <td>JTVC Sengkang</td>
                                    <td>Singapore</td>
                                </tr>
                                <tr>
                                    <td>Feb 3, 2024</td>
                                    <td>Booster 3</td>
                                    <td>COVID-19 MODERNA/SPIKEVAX OMICRON XBB.1.5 (A-COV)</td>
                                    <td>JTVC Sengkang</td>
                                    <td>Singapore</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <h2 class="card-title"><i class="fa-solid fa-fw fa-viruses"></i> Infection Record</h2>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                <tr>
                                    <th>Date Tested</th>
                                    <th>Result</th>
                                    <th>Record</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Nov 2, 2022</td>
                                    <td>Positive</td>
                                    <td><a href="https://drive.google.com/file/d/1WyNljwY7ZelnIgQnXpQJgIG6SB0P9E8r/view" target="_blank"><i class="fa-brands fa-google-drive"></i> View</a></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $this->endSection() ?>
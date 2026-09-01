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
                        <div class="row">
                            <div class="col">
                                <h2>FitnessFirst Singapore</h2>
                                <div class="row">
                                    <div class="col-6">Membership No.</div><div class="col-6 text-end">20203643</div>
                                    <div class="col-6">Membership Since</div><div class="col-6 text-end">January 18, 2022</div>
                                    <div class="col-6">Home Club</div><div class="col-6 text-end">100 AM</div>
                                    <div class="col-6">Package</div><div class="col-6 text-end">Platinum Passport</div>
                                    <div class="col-6">Access</div><div class="col-6 text-end">All Clubs (with access in foreign countries)</div>
                                </div>
                                <hr class="my-3" />
                                <div class="row g-3">
                                    <div class="col-12">
                                        <button class="btn btn-outline-danger btn-gym mb-3 btn-default" data-target="sg"><span class="flag-icon flag-icon-sg"></span> Singapore</button>
                                        <button class="btn btn-outline-danger btn-gym mb-3" data-target="th"><span class="flag-icon flag-icon-th"></span> Thailand</button>
                                        <button class="btn btn-outline-danger btn-gym mb-3" data-target="my"><span class="flag-icon flag-icon-my"></span> Malaysia</button>
                                        <button class="btn btn-outline-danger btn-gym mb-3" data-target="id"><span class="flag-icon flag-icon-id"></span> Indonesia</button>
                                        <a href="<?= base_url($session->locale . '/office/health/gym-finder') ?>" class="btn btn-outline-danger btn-sm float-end"><i class="fa-solid fa-external-link"></i> Gym Finder</a>
                                    </div>
                                    <div class="col-12" id="target-map"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('.btn-gym').click(function() {
                $('.btn-gym').removeClass('btn-danger text-white').addClass('btn-outline-danger');
                $(this).addClass('btn-danger text-white');
                let target_map = $(this).data('target');
                if (target_map === 'sg') {
                    $('#target-map').html('<iframe src="https://www.google.com/maps/d/embed?mid=1cSePLAeeNDB_nV6k3VExKuL-9HNkx40&ehbc=2E312F&noprof=1" style="width:100%;height:75vh"></iframe>');
                } else if (target_map === 'th') {
                    $('#target-map').html('<iframe src="https://www.google.com/maps/d/embed?mid=1ouXRfIXH8savkj_TIufpD8Pod5LTsEs&ehbc=2E312F&noprof=1" style="width:100%;height:75vh"></iframe>');
                } else if (target_map === 'my') {
                    $('#target-map').html('<iframe src="https://www.google.com/maps/d/embed?mid=1BIqdtd-566rmPzEsqeu1YpZwgiTlC6Q&ehbc=2E312F&noprof=1" style="width:100%;height:75vh"></iframe>');
                } else if (target_map === 'id') {
                    $('#target-map').html('<iframe src="https://www.google.com/maps/d/embed?mid=1Yk7G--0Q8c3SC4l-1UALXehIxVVu5FU&ehbc=2E312F&noprof=1" style="width:100%;height:75vh"></iframe>');
                }
            });
            $('.btn-default').click();
        });
    </script>
<?php $this->endSection() ?>
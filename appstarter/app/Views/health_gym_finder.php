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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/health/gym') ?>">Gym</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title"><i class="fa-solid fa-dumbbell"></i> <?= $page_title ?></h2>
                        <div class="row mb-3">
                            <div class="col-12 col-lg-4">
                                <div class="mb-3">
                                    <label for="city_code" class="form-label">Country</label><br>
                                    <select class="form-select form-select-sm" id="city_code">
                                        <option value="SG">Singapore</option>
                                        <option value="TH">Thailand</option>
                                        <option value="MY">Malaysia</option>
                                    </select>
                                </div>
                                <div class="text-end">
                                    <button id="btn-filter" class="btn btn-sm btn-outline-danger">Find <i class="fa-solid fa-magnifying-glass-location"></i></button>
                                </div>
                            </div>
                            <div class="col-12 col-lg-8" id="target">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let filter_gym = function(coords) {
                let city_code = $('#city_code').val();
                if (coords === null) { coords = { latitude: 1.3521, longitude: 103.8198 }; }
                else { coords = coords.coords; }
                console.log('Calling gym finder API for ' + coords.latitude + ', ' + coords.longitude);
                $.post('<?= base_url($session->locale . '/office/health/gym-finder') ?>',
                    {latitude: coords.latitude, longitude: coords.longitude, city_code: city_code},
                    function (data) {
                        $('#target').html('');
                        let counter = 0;
                        $.each(data.data, function (index, gym) {
                            counter++;
                            $('#target').append('<a class="btn btn-outline-danger btn-sm float-end" href="'+gym.url+'" target="_blank">Check the website <i class="fa-solid fa-arrow-up-right-from-square"></i></a><h6>('+counter+') '+gym.club+'</h6><p>'+gym.distance + 'km<br>Opens ('+gym.day+'): '+gym.open+' - '+gym.close+'</p><hr />');
                            $('#btn-filter').prop('disabled', false);
                        });
                    }
                );
            }
            let show_error = function(error) {
                toastr.error('Error: ' + error.message);
            }
            $('#btn-filter').click(function() {
                $(this).prop('disabled', true);
                $('#target').html('<i class="fa-solid fa-circle-notch fa-spin"></i> Loading...');
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(filter_gym, show_error);
                } else {
                    filter_gym(null);
                    console.log("Geolocation is not supported by this browser. Use default location");
                }
            });
        });
    </script>
<?php $this->endSection() ?>
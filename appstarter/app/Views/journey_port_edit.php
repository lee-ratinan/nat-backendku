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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/journey/port') ?>">Port</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= $page_title ?></h5>
                        <?php
                        generate_form_field('mode_of_transport', $config['mode_of_transport'], @$port['mode_of_transport']);
                        generate_form_field('port_code_1', $config['port_code_1'], @$port['port_code_1']);
                        generate_form_field('port_code_2', $config['port_code_2'], @$port['port_code_2']);
                        generate_form_field('country_code', $config['country_code'], @$port['country_code']);
                        generate_form_field('location_latitude', $config['location_latitude'], @$port['location_latitude']);
                        generate_form_field('location_longitude', $config['location_longitude'], @$port['location_longitude']);
                        generate_form_field('port_name', $config['port_name'], @$port['port_name']);
                        generate_form_field('port_local_name', $config['port_local_name'], @$port['port_local_name']);
                        generate_form_field('port_full_name', $config['port_full_name'], @$port['port_full_name']);
                        generate_form_field('city_name', $config['city_name'], @$port['city_name']);
                        ?>
                        <div class="text-end">
                            <hr>
                            <button class="btn btn-sm btn-outline-primary" id="btn-save-journey-port"><i class="fa-solid fa-save fa-fw me-2"></i> Save Port</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#btn-save-journey-port').click(function (e) {
                e.preventDefault();
                $.ajax({
                    url: '<?= base_url($session->locale . '/office/journey/port/edit') ?>',
                    type: 'post',
                    data: {
                        id: '<?= $port['id'] ?? 0 ?>',
                        mode: '<?= $mode ?>',
                        mode_of_transport: $('#mode_of_transport').val(),
                        port_code_1: $('#port_code_1').val(),
                        port_code_2: $('#port_code_2').val(),
                        country_code: $('#country_code').val(),
                        location_latitude: $('#location_latitude').val(),
                        location_longitude: $('#location_longitude').val(),
                        port_name: $('#port_name').val(),
                        port_local_name: $('#port_local_name').val(),
                        port_full_name: $('#port_full_name').val(),
                        city_name: $('#city_name').val()
                    },
                    success: function (response) {
                        if ('success' === response.status) {
                            toastr.success(response.toast);
                            setTimeout(function () {
                                window.location.href = response.url;
                            }, 1000);
                        } else {
                            toastr.error(response.toast ?? 'Failed to save transport.');
                        }
                    },
                    error: function (xhr, status, error) {
                        let response = JSON.parse(xhr.responseText);
                        let error_message = (response.toast ?? '<?= lang('System.status_message.generic_error') ?>');
                        toastr.error(error_message);
                    }
                });
            });
        });
    </script>
<?php $this->endSection() ?>
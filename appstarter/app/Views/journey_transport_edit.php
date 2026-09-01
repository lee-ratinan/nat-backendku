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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/journey/transport') ?>">Transport</a></li>
                <li class="breadcrumb-item"><a href="<?= $parent ?>">Parent Trip</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Transport</h5>
                        <?php
                        generate_form_field('operator_id', $config['operator_id'], @$transport['operator_id']);
                        generate_form_field('departure_port_id', $config['departure_port_id'], @$transport['departure_port_id']);
                        generate_form_field('arrival_port_id', $config['arrival_port_id'], @$transport['arrival_port_id']);
                        generate_form_field('flight_number', $config['flight_number'], @$transport['flight_number']);
                        generate_form_field('pnr_number', $config['pnr_number'], @$transport['pnr_number']);
                        generate_form_field('departure_date_time', $config['departure_date_time'], @$transport['departure_date_time']);
                        generate_form_field('departure_timezone', $config['departure_timezone'], @$transport['departure_timezone']);
                        generate_form_field('arrival_date_time', $config['arrival_date_time'], @$transport['arrival_date_time']);
                        generate_form_field('arrival_timezone', $config['arrival_timezone'], @$transport['arrival_timezone']);
                        generate_form_field('is_time_known', $config['is_time_known'], @$transport['is_time_known']);
                        generate_form_field('mode_of_transport', $config['mode_of_transport'], @$transport['mode_of_transport']);
                        generate_form_field('craft_type', $config['craft_type'], @$transport['craft_type']);
                        echo '<div class="row"><div class="col-6">';
                        generate_form_field('price_amount', $config['price_amount'], @$transport['price_amount']);
                        echo '</div><div class="col-6">';
                        generate_form_field('price_currency_code', $config['price_currency_code'], @$transport['price_currency_code']);
                        echo '</div></div><div class="row"><div class="col-6">';
                        generate_form_field('charged_amount', $config['charged_amount'], @$transport['charged_amount']);
                        echo '</div><div class="col-6">';
                        generate_form_field('charged_currency_code', $config['charged_currency_code'], @$transport['charged_currency_code']);
                        echo '</div></div>';
                        generate_form_field('journey_details', $config['journey_details'], @$transport['journey_details']);
                        generate_form_field('journey_status', $config['journey_status'], @$transport['journey_status']);
                        generate_form_field('google_drive_link', $config['google_drive_link'], @$transport['google_drive_link']);
                        ?>
                        <div class="text-end">
                            <hr>
                            <button class="btn btn-sm btn-outline-primary" id="btn-save-journey-transport"><i class="fa-solid fa-save fa-fw me-2"></i> Save Transport</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#btn-save-journey-transport').click(function (e) {
                e.preventDefault();
                let price_amount = $('#price_amount').val(),
                    price_currency_code = $('#price_currency_code').val(),
                    charged_amount = $('#charged_amount').val(),
                    charged_currency_code = $('#charged_currency_code').val();
                if (('' !== price_amount && '' === price_currency_code)
                    || ('' === price_amount && '' !== price_currency_code)) {
                    toastr.info('Price amount and its associated currency must be filled together.');
                    return false;
                }
                if (('' !== charged_amount && '' === charged_currency_code)
                    || ('' === charged_amount && '' !== charged_currency_code)) {
                    toastr.info('Charged amount and its associated currency must be filled together.');
                    return false;
                }
                $.ajax({
                    url: '<?= base_url($session->locale . '/office/journey/transport/edit') ?>',
                    type: 'post',
                    data: {
                        id: '<?= $transport['id'] ?? 0 ?>',
                        mode: '<?= $mode ?>',
                        journey_id: '<?= $journey_id ?>',
                        operator_id: $('#operator_id').val(),
                        departure_port_id: $('#departure_port_id').val(),
                        arrival_port_id: $('#arrival_port_id').val(),
                        flight_number: $('#flight_number').val(),
                        pnr_number: $('#pnr_number').val(),
                        departure_date_time: $('#departure_date_time').val(),
                        departure_timezone: $('#departure_timezone').val(),
                        arrival_date_time: $('#arrival_date_time').val(),
                        arrival_timezone: $('#arrival_timezone').val(),
                        is_time_known: $('#is_time_known').val(),
                        mode_of_transport: $('#mode_of_transport').val(),
                        craft_type: $('#craft_type').val(),
                        price_amount: price_amount,
                        price_currency_code: price_currency_code,
                        charged_amount: charged_amount,
                        charged_currency_code: charged_currency_code,
                        journey_details: $('#journey_details').val(),
                        journey_status: $('#journey_status').val(),
                        google_drive_link: $('#google_drive_link').val()
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
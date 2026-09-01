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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/journey/accommodation') ?>">Accommodation</a></li>
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
                        <h5 class="card-title">Accommodation</h5>
                        <?php
                        generate_form_field('country_code', $config['country_code'], @$accommodation['country_code']);
                        generate_form_field('check_in_date', $config['check_in_date'], @$accommodation['check_in_date']);
                        generate_form_field('check_out_date', $config['check_out_date'], @$accommodation['check_out_date']);
                        generate_form_field('accommodation_timezone', $config['accommodation_timezone'], @$accommodation['accommodation_timezone']);
                        generate_form_field('hotel_name', $config['hotel_name'], @$accommodation['hotel_name']);
                        generate_form_field('hotel_address', $config['hotel_address'], @$accommodation['hotel_address']);
                        generate_form_field('booking_channel', $config['booking_channel'], @$accommodation['booking_channel']);
                        generate_form_field('room_type', $config['room_type'], @$accommodation['room_type']);
                        generate_form_field('breakfast_included', $config['breakfast_included'], @$accommodation['breakfast_included']);
                        echo '<div class="row"><div class="col-6">';
                        generate_form_field('price_amount', $config['price_amount'], @$accommodation['price_amount']);
                        echo '</div><div class="col-6">';
                        generate_form_field('price_currency_code', $config['price_currency_code'], @$accommodation['price_currency_code']);
                        echo '</div></div><div class="row"><div class="col-6">';
                        generate_form_field('charged_amount', $config['charged_amount'], @$accommodation['charged_amount']);
                        echo '</div><div class="col-6">';
                        generate_form_field('charged_currency_code', $config['charged_currency_code'], @$accommodation['charged_currency_code']);
                        echo '</div></div>';
                        generate_form_field('journey_details', $config['journey_details'], @$accommodation['journey_details']);
                        generate_form_field('journey_status', $config['journey_status'], @$accommodation['journey_status']);
                        generate_form_field('google_drive_link', $config['google_drive_link'], @$accommodation['google_drive_link']);
                        ?>
                        <div class="text-end">
                            <hr>
                            <button class="btn btn-sm btn-outline-primary" id="btn-save-journey-accommodation"><i class="fa-solid fa-save fa-fw me-2"></i> Save Accommodation</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#btn-save-journey-accommodation').click(function (e) {
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
                    url: '<?= base_url($session->locale . '/office/journey/accommodation/edit') ?>',
                    type: 'post',
                    data: {
                        id: '<?= $accommodation['id'] ?? 0 ?>',
                        mode: '<?= $mode ?>',
                        journey_id: '<?= $journey_id ?>',
                        country_code: $('#country_code').val(),
                        check_in_date: $('#check_in_date').val(),
                        check_out_date: $('#check_out_date').val(),
                        accommodation_timezone: $('#accommodation_timezone').val(),
                        hotel_name: $('#hotel_name').val(),
                        hotel_address: $('#hotel_address').val(),
                        booking_channel: $('#booking_channel').val(),
                        room_type: $('#room_type').val(),
                        breakfast_included: $('#breakfast_included').val(),
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
                            toastr.error(response.toast ?? 'Failed to save accommodation.');
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
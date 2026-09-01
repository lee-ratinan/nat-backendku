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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/journey/attraction') ?>">Attraction</a></li>
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
                        <h5 class="card-title">Attraction</h5>
                        <?php
                        generate_form_field('country_code', $config['country_code'], @$attraction['country_code']);
                        generate_form_field('attraction_date', $config['attraction_date'], @$attraction['attraction_date']);
                        generate_form_field('attraction_title', $config['attraction_title'], @$attraction['attraction_title']);
                        generate_form_field('attraction_type', $config['attraction_type'], @$attraction['attraction_type']);
                        echo '<div class="row"><div class="col-6">';
                        generate_form_field('price_amount', $config['price_amount'], @$attraction['price_amount']);
                        echo '</div><div class="col-6">';
                        generate_form_field('price_currency_code', $config['price_currency_code'], @$attraction['price_currency_code']);
                        echo '</div></div><div class="row"><div class="col-6">';
                        generate_form_field('charged_amount', $config['charged_amount'], @$attraction['charged_amount']);
                        echo '</div><div class="col-6">';
                        generate_form_field('charged_currency_code', $config['charged_currency_code'], @$attraction['charged_currency_code']);
                        echo '</div></div>';
                        generate_form_field('journey_details', $config['journey_details'], @$attraction['journey_details']);
                        generate_form_field('journey_status', $config['journey_status'], @$attraction['journey_status']);
                        generate_form_field('google_drive_link', $config['google_drive_link'], @$attraction['google_drive_link']);
                        ?>
                        <div class="text-end">
                            <hr>
                            <button class="btn btn-sm btn-outline-primary" id="btn-save-journey-attraction"><i class="fa-solid fa-save fa-fw me-2"></i> Save Attraction</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#btn-save-journey-attraction').click(function (e) {
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
                    url: '<?= base_url($session->locale . '/office/journey/attraction/edit') ?>',
                    type: 'post',
                    data: {
                        id: '<?= $attraction['id'] ?? 0 ?>',
                        mode: '<?= $mode ?>',
                        journey_id: '<?= $journey_id ?>',
                        country_code: $('#country_code').val(),
                        attraction_date: $('#attraction_date').val(),
                        attraction_title: $('#attraction_title').val(),
                        attraction_type: $('#attraction_type').val(),
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
                            toastr.error(response.toast ?? 'Failed to save attraction.');
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
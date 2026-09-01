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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/journey/holiday') ?>">Holiday</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fa-solid fa-umbrella-beach"></i> Holiday</h5>
                        <?php
                        generate_form_field('country_code', $config['country_code'], @$holiday['country_code']);
                        generate_form_field('region_code', $config['region_code'], @$holiday['region_code']);
                        generate_form_field('holiday_date', $config['holiday_date'], @$holiday['holiday_date']);
                        generate_form_field('holiday_date_to', $config['holiday_date_to'], @$holiday['holiday_date_to']);
                        generate_form_field('holiday_name', $config['holiday_name'], @$holiday['holiday_name']);
                        ?>
                        <div class="text-end">
                            <button class="btn btn-sm btn-outline-primary" id="submit-form"><i class="fa-solid fa-save fa-fw me-2"></i> Save Holiday</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#holiday_date').change(function () {
                let holiday_date = $('#holiday_date').val();
                $('#holiday_date_to').val(holiday_date).attr('min', holiday_date);
            });
            $('#submit-form').click(function (e) {
                e.preventDefault();
                let id = '<?= $holiday['id'] ?? 0 ?>',
                    mode = '<?= $mode ?>',
                    country_code = $('#country_code').val(),
                    region_code = $('#region_code').val(),
                    holiday_date = $('#holiday_date').val(),
                    holiday_date_to = $('#holiday_date_to').val(),
                    holiday_name = $('#holiday_name').val();
                if ('' === country_code || '' === holiday_date || '' === holiday_name) {
                    toastr.info('Please fill in the required fields.');
                    return false;
                }
                if ('' === holiday_date_to) {
                    holiday_date_to = holiday_date;
                }
                $.ajax({
                    url: '<?= base_url($session->locale . '/office/journey/holiday/edit') ?>',
                    type: 'post',
                    data: {
                        id: id,
                        mode: mode,
                        country_code: country_code,
                        region_code: region_code,
                        holiday_date: holiday_date,
                        holiday_date_to: holiday_date_to,
                        holiday_name: holiday_name
                    },
                    success: function (response) {
                        if ('success' === response.status) {
                            toastr.success(response.toast);
                            setTimeout(function () {
                                window.location.href = response.url;
                            }, 1000);
                        } else {
                            toastr.error(response.toast ?? 'Failed to save holiday.');
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
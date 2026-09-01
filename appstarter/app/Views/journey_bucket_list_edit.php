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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/journey/bucket-list') ?>">Bucket List</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Bucket List</h5>
                        <?php
                        $fields = [
                            'activity_name',
                            'activity_name_local',
                            'activity_slug',
                            'activity_location',
                            'country_code',
                            'category_code',
                            'completed_dates',
                            'description',
                            'trip_codes',
                            'building_height',
                            'building_built_year',
                        ];
                        foreach ($fields as $field) {
                            generate_form_field($field, $config[$field], @$bucket_item[$field]);
                        }
                        ?>
                        <div class="text-end">
                            <hr>
                            <button class="btn btn-sm btn-outline-primary" id="btn-save-journey-attraction"><i class="fa-solid fa-save fa-fw me-2"></i> Save Bucket List Item</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#activity_name').change(function () {
                let activity_name = (($(this).val()).trim()).replace(/\s{2,}/g, ' '); // TRIM and REPLACE SPACES
                $(this).val(activity_name);
                $('#activity_slug').val(activity_name.toLowerCase().replace(/\s/g, '-').replace(/[^a-zA-Z0-9\-]/g, ''));
            });
            $('#btn-save-journey-attraction').click(function (e) {
                e.preventDefault();
                let ids = ['activity_name', 'activity_slug', 'category_code'];
                for (let i = 0; i < ids.length; i++) {
                    if ('' === $('#' + ids[i]).val()) {
                        toastr.warning('Please ensure all mandatory fields are filled.');
                        $('#' + ids[i]).focus();
                        return;
                    }
                }
                $.ajax({
                    url: '<?= base_url($session->locale . '/office/journey/bucket-list/edit') ?>',
                    type: 'post',
                    data: {
                        mode: '<?= $mode ?>',
                        id: <?= $bucket_item['id'] ?? '0' ?>,
                        <?php foreach ($fields as $field) : ?>
                        <?= $field ?>: $('#<?= $field ?>').val(),
                        <?php endforeach; ?>
                    },
                    success: function (response) {
                        if ('success' === response.status) {
                            toastr.success(response.toast);
                            setTimeout(function () {
                                window.location.href = response.redirect;
                            }, 1000);
                        } else {
                            toastr.error(response.toast ?? 'Failed to save bucket list.');
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
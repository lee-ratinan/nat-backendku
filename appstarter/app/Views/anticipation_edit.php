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
            <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/employment/part-time') ?>">Part Time Schedule</a></li>
            <li class="breadcrumb-item active"><?= $page_title ?></li>
        </ol>
    </nav>
</div>
<section class="section">
    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?= $page_title ?></h5>
                    <?php
                    $fields = [
                        'anticipation_category',
                        'anticipation_title',
                        'why_it_matters',
                        'external_url',
                        'image_url',
                        'target_date',
                        'date_precision',
                        'is_favorite',
                        'item_status',
                        'completed_at',
                        'completion_note',
                    ];
                    foreach ($fields as $field) {
                        generate_form_field($field, $config[$field], @$anticipation[$field]);
                    }
                    ?>
                    <div class="text-end">
                        <button class="btn btn-primary btn-sm" id="btn-save"><i class="fa-solid fa-save"></i> Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('#btn-save').click(function (e) {
            e.preventDefault();
            let ids = ['anticipation_category', 'anticipation_title', 'target_date', 'date_precision', 'is_favorite', 'item_status'];
            for (let i = 0; i < ids.length; i++) {
                if ('' === $('#' + ids[i]).val()) {
                    toastr.warning('Please ensure all mandatory fields are filled.');
                    $('#' + ids[i]).focus();
                    return;
                }
            }
            $(this).prop('disabled', true);
            $.ajax({
                url: '<?= base_url('en/office/anticipation/save') ?>',
                type: 'post',
                data: {
                    'id': '<?= $anticipation_id ?>',
                    'anticipation_category': $('#anticipation_category').val(),
                    'anticipation_title': $('#anticipation_title').val(),
                    'why_it_matters': $('#why_it_matters').val(),
                    'external_url': $('#external_url').val(),
                    'image_url': $('#image_url').val(),
                    'target_date': $('#target_date').val(),
                    'date_precision': $('#date_precision').val(),
                    'is_favorite': $('#is_favorite').val(),
                    'item_status': $('#item_status').val(),
                    'completed_at': $('#completed_at').val(),
                    'completion_note': $('#completion_note').val()
                },
                success: function (response) {
                    if ('success' === response.status) {
                        toastr.success(response.toast);
                        setTimeout(function () {window.location.href = response.redirect;}, 5000);
                    } else {
                        let message = (response.toast ?? 'Sorry! Something went wrong. Please try again.');
                        toastr.error(message);
                        $('#btn-save').prop('disabled', false);
                    }
                },
                error: function (xhr, status, error) {
                    let response = JSON.parse(xhr.responseText);
                    let error_message = (response.toast ?? 'Sorry! Something went wrong. Please try again.');
                    $('#btn-save').prop('disabled', false);
                    toastr.error(error_message);
                }
            });
        });
    });
</script>
<?php $this->endSection() ?>
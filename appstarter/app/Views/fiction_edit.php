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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/fiction') ?>">Fiction</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-body pt-3">
                        <h5 class="card-title"><?= $page_title ?></h5>
                        <?php
                        $fields = [
                            'fiction_title',
                            'fiction_slug',
                            'fiction_genre',
                            'pen_name'
                        ];
                        foreach ($fields as $field) {
                            generate_form_field($field, $config[$field], @$row[$field]);
                        }
                        ?>
                        <div class="text-end">
                            <button class="btn btn-primary btn-sm" id="btn-save"><i class="fa-solid fa-save"></i> Save</button>
                        </div>
                        <?php if (isset($row['fiction_slug'])) : ?>
                            <hr />
                            <a class="btn btn-outline-primary w-100" href="<?= base_url($session->locale . '/office/fiction/view-entries/' . $row['fiction_slug']) ?>">Start Writing</a>
                            <hr />
                            <h6><i class="fa-solid fa-cloud-arrow-up"></i> Upload Cover</h6>
                            <form id="form-upload-cover" action="<?= base_url($session->locale . '/office/fiction/upload-cover') ?>" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="script_action" value="upload-cover"/>
                                <input type="hidden" name="fiction_slug" value="<?= $row['fiction_slug'] ?>"/>
                                <input type="file" id="fiction_cover" name="fiction_cover" class="form-control my-3" accept=".jpg,.png" />
                                <p class="small">Only .jpg file is acceptable, around 636x900px, max 300kb.</p>
                                <div class="text-end">
                                    <button id="btn-upload-cover" type="submit" class="btn btn-primary"><i class="fa-solid fa-cloud-arrow-up"></i> Upload</button>
                                </div>
                            </form>
                            <hr />
                            <h6>Current Cover:</h6>
                            <img class="img-fluid mb-2" src="<?= base_url('file/fiction_' . $row['fiction_slug'] . '.jpg') ?>" alt="<?= $row['fiction_title'] ?>" />
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#fiction_title').change(function () {
                let fiction_title = (($(this).val()).trim()).replace(/\s{2,}/g, ' '); // TRIM and REPLACE SPACES
                $(this).val(fiction_title);
                $('#fiction_slug').val(fiction_title.toLowerCase().replace(/\s/g, '-').replace(/[^a-zA-Z0-9\-]/g, ''));
            });
            $('#btn-save').click(function (e) {
                e.preventDefault();
                let ids = ['fiction_title', 'fiction_slug', 'fiction_genre', 'pen_name'];
                for (let i = 0; i < ids.length; i++) {
                    if ('' === $('#' + ids[i]).val()) {
                        toastr.warning('Please ensure all mandatory fields are filled.');
                        $('#' + ids[i]).focus();
                        return;
                    }
                }
                $(this).prop('disabled', true);
                $.ajax({
                    url: '<?= base_url('en/office/fiction/edit') ?>',
                    type: 'post',
                    data: {
                        mode: '<?= $mode ?>',
                        id: <?= $row['id'] ?? '0' ?>,
                        <?php foreach ($fields as $field) : ?>
                        <?= $field ?>: $('#<?= $field ?>').val(),
                        <?php endforeach; ?>
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
            // upload
            $('#btn-upload-cover').on('click', function (e) {
                e.preventDefault();
                // check if the file is selected
                if ($('#fiction_cover').val() === '') {
                    toastr.warning('Please select an the cover file to upload.');
                    $('#fiction_cover').focus();
                    return;
                }
                $('#btn-upload-cover').prop('disabled', true);
                // submit form in AJAX
                $.ajax({
                    url: '<?= base_url($session->locale . '/office/fiction/upload-cover') ?>',
                    type: 'POST',
                    data: new FormData($('#form-upload-cover')[0]),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (response) {
                        $('#btn-upload-cover').prop('disabled', false);
                        if (response.status === 'success') {
                            toastr.success(response.toast);
                            setTimeout(function () {
                                window.location.reload();
                            }, 5000);
                        } else {
                            toastr.error(response.toast);
                        }
                    },
                    error: function (xhr, status, error) {
                        let response = JSON.parse(xhr.responseText);
                        let error_message = (response.toast ?? 'Sorry! Something went wrong. Please try again.');
                        $('#btn-upload-cover').prop('disabled', false);
                        toastr.error(error_message);
                    }
                });
            });
        });
    </script>
<?php $this->endSection() ?>
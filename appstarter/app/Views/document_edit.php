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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/document') ?>">Document</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <?php $published_version_numbers = []; ?>
    <section class="section">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= $page_title ?></h5>
                        <?php if (!empty($published)) : ?>
                        <h6>Published Versions</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover table-striped">
                                <thead>
                                <tr>
                                    <th>Version</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>View</th>
                                    <th>Delete</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($published as $publish) : ?>
                                <tr>
                                    <td><?= $publish['version_number'] ?></td>
                                    <td><?= $publish['doc_title'] ?></td>
                                    <td><?= $publish['version_description'] ?></td>
                                    <td><a class="btn btn-outline-primary btn-sm" target="_blank" href="<?= base_url($session->locale . '/office/document/internal-document/' . $publish['doc_slug'] . '/' . $publish['version_number']) ?>"><i class="fa-solid fa-eye"></i></a></td>
                                    <td>
                                        <button class="btn btn-outline-danger btn-sm btn-delete" id="btn-delete-<?= $publish['id']*$nonce ?>" data-id="<?= $publish['id']*$nonce ?>"><i class="fa-solid fa-trash-can"></i></button>
                                        <button class="btn btn-danger btn-sm btn-confirm-delete d-none" id="btn-confirm-delete-<?= $publish['id']*$nonce ?>" data-id="<?= $publish['id']*$nonce ?>"><i class="fa-solid fa-check-circle"></i></button>
                                        <button class="btn btn-danger btn-sm btn-cancel-delete d-none" id="btn-cancel-delete-<?= $publish['id']*$nonce ?>" data-id="<?= $publish['id']*$nonce ?>"><i class="fa-solid fa-cancel"></i></button>
                                    </td>
                                </tr>
                                <?php $published_version_numbers[] = $publish['version_number']; ?>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                        <?php
                        $fields = [
                            'doc_title',
                            'doc_slug',
                            'company_id',
                            'doc_status',
                            'doc_content',
                            'version_number',
                            'version_description',
                        ];
                        foreach ($fields as $field) {
                            generate_form_field($field, $config[$field], @$document[$field]);
                        }
                        ?>
                        <div class="text-end">
                            <button class="btn btn-primary btn-sm" id="btn-save-document"><i class="fa-solid fa-save"></i> Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let autosaveTimer;
            const autosaveDelay = 15000; // 15 seconds
            let lastContent = "";
            $('.btn-delete').click(function (e) {
                e.preventDefault();
                let id = $(this).data('id');
                $(this).addClass('d-none');
                $('#btn-confirm-delete-'+id).removeClass('d-none');
                $('#btn-cancel-delete-'+id).removeClass('d-none');
            });
            $('.btn-confirm-delete').click(function (e) {
                e.preventDefault();
                let id = $(this).data('id');
                $(this).prop('disabled', true);
                $.ajax({
                    url: '<?= base_url('en/office/document/delete') ?>',
                    type: 'post',
                    data: {
                        id: id,
                    },
                    success: function (response) {
                        if ('success' === response.status) {
                            toastr.success('The version has been deleted successfully.');
                        } else {
                            toastr.error('Something went wrong. Please try again.');
                        }
                        setTimeout(function () {location.reload();}, 5000);
                    },
                    error: function (xhr, status, error) {
                        toastr.error('Something went wrong. Please try again.');
                        setTimeout(function () {location.reload();}, 5000);
                    }
                })
            });
            $('.btn-cancel-delete').click(function (e) {
                e.preventDefault();
                let id = $(this).data('id');
                $(this).addClass('d-none');
                $('#btn-delete-'+id).removeClass('d-none');
                $('#btn-confirm-delete-'+id).addClass('d-none');
            });
            tinymce.init({
                setup: function (editor) {
                    <?php if ('edit' == $mode) : ?>
                    editor.on('init', function () {
                        lastContent = editor.getContent();
                    });
                    editor.on('change input', function () {
                        $('#autosave-label').addClass('d-none');
                        clearTimeout(autosaveTimer);
                        autosaveTimer = setTimeout(function () {
                            const currentContent = editor.getContent();
                            if (currentContent !== lastContent) {
                                $.ajax({
                                    url: '<?= base_url($session->locale . '/office/document/autosave') ?>',
                                    type: 'post',
                                    data: {
                                        id: <?= $document['id'] ?? '0' ?>,
                                        doc_content: currentContent
                                    },
                                    success: function (response) {
                                        if ('success' === response.status) {
                                            $('#autosave-label').removeClass('d-none');
                                        } else {
                                            toastr.error('Autosave failed!')
                                        }
                                    },
                                    error: function (xhr, status, error) {
                                        toastr.error('Autosave failed!')
                                    }
                                });
                            }
                        }, autosaveDelay);
                    });
                    <?php endif; ?>
                },
                selector: 'textarea.tinymce',
                skin: 'oxide-dark',
                height: '90vh',
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | blocks | ' +
                    'bold italic strikethrough | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | removeformat'
            });
            $('#doc_title').change(function () {
                let document_title = (($(this).val()).trim()).replace(/\s{2,}/g, ' '); // TRIM and REPLACE SPACES
                $(this).val(document_title);
                $('#doc_slug').val(document_title.toLowerCase().replace(/\s/g, '-').replace(/[^a-zA-Z0-9\-]/g, ''));
            });
            $('#version_number').change(function (e) {
                e.preventDefault();
                let used_numbers = <?= json_encode($published_version_numbers) ?>,
                    this_number  = $(this).val();
                if (used_numbers.indexOf(this_number) > -1) {
                    toastr.warning('The version number is not unique.');
                    $(this).val('').focus();
                }
            });
            $('#btn-save-document').click(function (e) {
                e.preventDefault();
                let ids = ['doc_title', 'doc_slug'];
                for (let i = 0; i < ids.length; i++) {
                    if ('' === $('#' + ids[i]).val()) {
                        toastr.warning('Please ensure all mandatory fields are filled.');
                        $('#' + ids[i]).focus();
                        return;
                    }
                }
                let version_number = $('#version_number').val();
                let version_description = $('#version_description').val();
                if ('' !== version_number && '' === version_description) {
                    toastr.warning('Version description cannot be left empty in order to publish a new version.');
                    $('#version_description').focus();
                    return;
                }
                $(this).prop('disabled', true);
                $.ajax({
                    url: '<?= base_url('en/office/document/edit') ?>',
                    type: 'post',
                    data: {
                        mode: '<?= $mode ?>',
                        id: <?= $document['id'] ?? '0' ?>,
                        doc_title: $('#doc_title').val(),
                        doc_slug: $('#doc_slug').val(),
                        company_id: $('#company_id').val(),
                        doc_status: $('#doc_status').val(),
                        doc_content: tinymce.get('doc_content').getContent(),
                        version_number: $('#version_number').val(),
                        version_description: $('#version_description').val(),
                    },
                    success: function (response) {
                        if ('success' === response.status) {
                            toastr.success(response.toast);
                            setTimeout(function () {window.location.href = response.redirect;}, 5000);
                        } else {
                            let message = (response.toast ?? 'Sorry! Something went wrong. Please try again.');
                            toastr.error(message);
                            $('#btn-save-document').prop('disabled', false);
                        }
                    },
                    error: function (xhr, status, error) {
                        let response = JSON.parse(xhr.responseText);
                        let error_message = (response.toast ?? 'Sorry! Something went wrong. Please try again.');
                        $('#btn-save-document').prop('disabled', false);
                        toastr.error(error_message);
                    }
                });
            });
        });
    </script>
<?php $this->endSection() ?>
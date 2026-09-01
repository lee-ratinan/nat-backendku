<?php
$layout = getenv('LAYOUT_FILE_OFFICE');
$layout = (!empty($layout) ? $layout : 'system/_layout_office');
$this->extend($layout);
?>
<?= $this->section('content') ?>
<?php $session = session(); ?>
    <style>
        .tox-promotion { display:none; }
    </style>
    <div class="pagetitle">
        <h1><?= $page_title ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/dashboard') ?>"><?= lang('System.dashboard.page_title') ?></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/health/ooca') ?>">OOCA Visit Log</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body pt-3">
                        <?php
                        $fields = [
                            'visit_date',
                            'psychologist_name',
                            'note_what_happened',
                            'note_what_i_said',
                            'note_what_suggested',
                        ];
                        foreach ($fields as $field) {
                            generate_form_field($field, $configuration[$field], @$record[$field]);
                        }
                        ?>
                        <div class="text-end">
                            <?php if (isset($record['id'])) : ?>
                                <a href="<?= base_url($session->locale . '/office/health/ooca/view/' . ($record['id'] * $nonce)) ?>" class="btn btn-link btn-sm"><i class="fa-solid fa-eye"></i> Cancel</a>
                            <?php endif; ?>
                            <button class="btn btn-primary btn-sm" id="btn-save"><i class="fa-solid fa-save"></i> Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            tinymce.init({
                selector: 'textarea.tinymce',
                skin: 'oxide-dark',
                height: 500,
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | blocks | ' +
                    'bold italic backcolor | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | ' +
                    'removeformat | help'
            });
            $('#btn-save').click(function (e) {
                e.preventDefault();
                let ids = ['visit_date', 'psychologist_name'];
                for (let i = 0; i < ids.length; i++) {
                    if ('' === $('#' + ids[i]).val()) {
                        toastr.warning('Please ensure all mandatory fields are filled.');
                        $('#' + ids[i]).focus();
                        return;
                    }
                }
                let tinyids = ['note_what_happened', 'note_what_i_said', 'note_what_suggested'];
                for (let i = 0; i < tinyids.length; i++) {
                    if ('' === tinymce.get(tinyids[i]).getContent()) {
                        toastr.warning('Please ensure all mandatory fields are filled.');
                        tinymce.get(tinyids[i]).focus();
                        return;
                    }
                }
                $(this).prop('disabled', true);
                $.ajax({
                    url: '<?= base_url('en/office/health/ooca/edit') ?>',
                    type: 'post',
                    data: {
                        id: <?= $record['id'] ?? '0' ?>,
                        visit_date: $('#visit_date').val(),
                        psychologist_name: $('#psychologist_name').val(),
                        note_what_happened: tinymce.get('note_what_happened').getContent(),
                        note_what_i_said: tinymce.get('note_what_i_said').getContent(),
                        note_what_suggested: tinymce.get('note_what_suggested').getContent(),
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
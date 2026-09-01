<?php
$layout = getenv('LAYOUT_FILE_OFFICE');
$layout = (!empty($layout) ? $layout : 'system/_layout_office');
$this->extend($layout);
?>
<?= $this->section('content') ?>
<?php $session = session(); ?>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400..700&family=Noto+Sans+Thai&family=Noto+Sans:ital,wght@0,400..700;1,400..700&family=Noto+Serif+Thai:wght@400..700&family=Noto+Serif:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">
    <div class="pagetitle">
        <h1><?= $page_title ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/dashboard') ?>"><?= lang('System.dashboard.page_title') ?></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/fiction') ?>">Fiction</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/fiction/view-entries/' . $title_row['fiction_slug']) ?>"><?= $title_row['fiction_title'] ?></a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div id="main-panel" class="col-md-8 col-lg-9">
                <div class="card">
                    <div class="card-body pt-3">
                        <a id="toc-btn-show"><i class="fa-solid fa-chevron-left"></i> Table of Contents</a>
                        <a id="toc-btn-hide"><i class="fa-solid fa-chevron-right"></i> Table of Contents</a>
                        <h5 class="card-title"><?= $page_title ?></h5>
                        <h6>Title: <?= $title_row['fiction_title'] ?></h6>
                        <p>By <?= $title_row['pen_name'] ?> | <?= $title_row['fiction_genre'] ?></p>
                        <?php
                        $fields = [
                            'parent_entry_id',
                            'entry_position',
                            'entry_title',
                            'entry_type',
                            'entry_note',
                            'entry_short_note',
                            'entry_content',
                            'entry_status',
                            'footnote_section'
                        ];
                        foreach ($fields as $field) {
                            if (!empty($field)) {
                                generate_form_field($field, $configurations[$field], @$entry_row[$field]);
                            }
                        }
                        ?>
                        <div class="text-end">
                            <button class="btn btn-primary btn-sm" id="btn-save"><i class="fa-solid fa-save"></i> Save</button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="toc-panel" class="col-md-4 col-lg-3">
                <ul style="padding-left: 0.5rem;">
                    <?php
                    function renderKey($key, $type): string
                    {
                        $pieces = explode('.', $key);
                        if ('00' == $pieces[1]) {
                            if ('front-matter' == $type) {
                                return '';
                            } else if ('chapter' == $type) {
                                return '<i class="fa-solid fa-book-open"></i>';
                            }
                            return '<i class="fa-solid fa-folder-open"></i>';
                        }
                        return '<i class="fa-solid fa-folder-tree"></i>';
                    }
                    ?>
                    <?php foreach ($toc['entries'] as $key => $value) : ?>
                        <?php if (@$entry_row['id'] == $value['id']) : ?>
                            <li><?= renderKey($key, $value['entry_type']) ?> <?= @$value['entry_title'] ?></li>
                        <?php else: ?>
                            <li><a href="<?= base_url($session->locale . '/office/fiction/edit-entry/' . ($value['id'] * $toc_nonce)) ?>"><?= renderKey($key, $value['entry_type']) ?> <?= @$value['entry_title'] ?></a></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let autosaveTimer;
            const autosaveDelay = 15000; // 15 seconds
            let lastContent = "";
            // INIT
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
                                    url: '<?= base_url($session->locale . '/office/fiction/autosave-entry') ?>',
                                    type: 'post',
                                    data: {
                                        id: <?= $real_entry_id ?>,
                                        entry_content: currentContent
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
                content_style: "body { font-family: 'Noto Serif Thai'; }",
                font_formats: "Noto Sans; Noto Serif; Noto Sans Thai; Noto Serif Thai; Noto Sans JP",
                toolbar: 'undo redo | bold italic | alignleft aligncenter | bullist numlist | removeformat'
            });
            // LISTENER
            $('#entry_type').change(function (e) {
                e.preventDefault();
                let entry_type = $(this).val();
                if ('chapter' === entry_type || 'folder' === entry_type) {
                    $('#entry_content-block').hide();
                } else {
                    $('#entry_content-block').show();
                }
            });
            if ('chapter' === $('#entry_type').val() || 'folder' === $('#entry_type').val()) {
                $('#entry_content-block').hide();
            } else {
                $('#entry_content-block').show();
            }
            $('#btn-save').click(function (e) {
                e.preventDefault();
                let ids = ['entry_position', 'entry_title', 'entry_type', 'entry_short_note', 'entry_status'];
                for (let i = 0; i < ids.length; i++) {
                    if ('' === $('#' + ids[i]).val()) {
                        toastr.warning('Please ensure all mandatory fields are filled.');
                        $('#' + ids[i]).focus();
                        return;
                    }
                }
                $(this).prop('disabled', true);
                $.ajax({
                    url: '<?= base_url('en/office/fiction/edit-entry') ?>',
                    type: 'post',
                    data: {
                        mode: '<?= $mode ?>',
                        id: <?= $real_entry_id ?>,
                        fiction_title_id: <?= $real_title_id ?>,
                        parent_entry_id: $('#parent_entry_id').val(),
                        entry_content: tinymce.get('entry_content').getContent(),
                        entry_position: $('#entry_position').val(),
                        entry_title: $('#entry_title').val(),
                        entry_type: $('#entry_type').val(),
                        entry_note: $('#entry_note').val(),
                        entry_short_note: $('#entry_short_note').val(),
                        entry_status: $('#entry_status').val(),
                        footnote_section: $('#footnote_section').val(),
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
            $('#toc-btn-show').on('click', function () {
                $('#toc-panel').fadeIn();
                $('#main-panel').removeClass('col-12').addClass('col-md-8 col-lg-9');
                $('#toc-btn-show').hide();
                $('#toc-btn-hide').show();
            }).hide();
            $('#toc-btn-hide').on('click', function () {
                $('#toc-panel').fadeOut();
                $('#main-panel').removeClass('col-md-8 col-lg-9').addClass('col-12');
                $('#toc-btn-hide').hide();
                $('#toc-btn-show').show();
            });
        });
    </script>
<?php $this->endSection() ?>
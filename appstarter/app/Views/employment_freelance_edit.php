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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/employment/freelance') ?>">Freelance</a></li>
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
                            'company_id',
                            'project_title',
                            'project_slug',
                            'project_start_date',
                            'project_end_date',
                            'client_name',
                            'freelance_client_id',
                        ];
                        foreach ($fields as $field) {
                            generate_form_field($field, $config[$field], @$project[$field]);
                        }
                        ?>
                        <div class="text-end">
                            <button class="btn btn-primary btn-sm" id="btn-save-project"><i class="fa-solid fa-save"></i> Save</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ('edit' == $mode) : ?>
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Project Details</h5>
                            <table class="table table-sm table-borderless">
                                <?php foreach ($fields as $field) : ?>
                                    <tr>
                                        <td class="text-end"><?= $config[$field]['label'] ?></td>
                                        <td>
                                            <?php
                                            echo match ($field) {
                                                'company_id' => $config[$field]['options'][$project[$field]],
                                                'project_start_date', 'project_end_date' => (empty($project[$field]) || '0000-00-00' == $project[$field] ? '-' : date(DATE_FORMAT_UI, strtotime($project[$field]))),
                                                default => $project[$field],
                                            };
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#project_title').change(function () {
                let project_title = (($(this).val()).trim()).replace(/\s{2,}/g, ' '); // TRIM and REPLACE SPACES
                $(this).val(project_title);
                $('#project_slug').val(project_title.toLowerCase().replace(/\s/g, '-').replace(/[^a-zA-Z0-9\-]/g, ''));
            });
            $('#btn-save-project').click(function (e) {
                e.preventDefault();
                let ids = ['company_id', 'project_title', 'project_slug', 'project_start_date', 'client_name'];
                for (let i = 0; i < ids.length; i++) {
                    if ('' === $('#' + ids[i]).val()) {
                        toastr.warning('Please ensure all mandatory fields are filled.');
                        $('#' + ids[i]).focus();
                        return;
                    }
                }
                $(this).prop('disabled', true);
                $.ajax({
                    url: '<?= base_url('en/office/employment/freelance/edit') ?>',
                    type: 'post',
                    data: {
                        mode: '<?= $mode ?>',
                        id: <?= $project['id'] ?? '0' ?>,
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
                            $('#btn-save-project').prop('disabled', false);
                        }
                    },
                    error: function (xhr, status, error) {
                        let response = JSON.parse(xhr.responseText);
                        let error_message = (response.toast ?? 'Sorry! Something went wrong. Please try again.');
                        $('#btn-save-project').prop('disabled', false);
                        toastr.error(error_message);
                    }
                });
            });
        });
    </script>
<?php $this->endSection() ?>
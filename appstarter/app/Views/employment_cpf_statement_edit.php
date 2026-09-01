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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/employment/cpf') ?>">CPF</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/employment/cpf/statement') ?>">CPF Statement</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12 col-md-10 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= $page_title ?></h5>
                        <?php
                        $fields = [
                            'statement_year',
                            'google_drive_url'
                        ];
                        if ('new' == $mode) {
                            foreach ($fields as $field) {
                                generate_form_field($field, $config[$field], @$statement[$field]);
                            }
                            echo '<div class="text-end"><button class="btn btn-primary btn-sm" id="btn-save-cpf-statement"><i class="fa-solid fa-save"></i> Save</button></div>';
                        } else {
                            echo '<table class="table table-sm table-borderless">';
                            foreach ($fields as $field) {
                                echo '<tr>';
                                echo '<td style="width:50%;" class="text-end">' . $config[$field]['label'] . '</td>';
                                echo '<td style="width:50%;">' . ('statement_year' == $field ? $statement[$field] : '<a href="' . $statement[$field] . '" class="btn btn-outline-primary btn-sm" target="_blank"><i class="fa-solid fa-file-pdf"></i></a>') . '</td>';
                                echo '</tr>';
                            }
                            echo '</table>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php if ('new' == $mode) : ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#btn-save-cpf-statement').click(function (e) {
                e.preventDefault();
                let statement_year = $('#statement_year').val(),
                    google_drive_url = $('#google_drive_url').val();
                if ('' === statement_year) {
                    toastr.warning('Please ensure statement year is filled.');
                    $('#statement_year').focus();
                    return;
                }
                if ('' === google_drive_url) {
                    toastr.warning('Please ensure Google Drive URL is filled.');
                    $('#google_drive_url').focus();
                    return;
                } else if ('https://drive.google.com/' !== google_drive_url.substring(0, 25)) {
                    toastr.warning('Please ensure Google Drive URL is really Google Drive URL.');
                    $('#google_drive_url').val('https://drive.google.com/').focus();
                    return;
                }
                $(this).prop('disabled', true);
                $.ajax({
                    url: '<?= base_url('en/office/employment/cpf/statement/edit') ?>',
                    type: 'post',
                    data: {
                        statement_year: statement_year,
                        google_drive_url: google_drive_url,
                    },
                    success: function (response) {
                        if ('success' === response.status) {
                            toastr.success(response.toast);
                            setTimeout(function () {window.location.href = response.redirect;}, 5000);
                        } else {
                            let message = (response.toast ?? 'Sorry! Something went wrong. Please try again.');
                            toastr.error(message);
                            $('#btn-save-cpf-statement').prop('disabled', false);
                        }
                    },
                    error: function (xhr, status, error) {
                        let response = JSON.parse(xhr.responseText);
                        let error_message = (response.toast ?? 'Sorry! Something went wrong. Please try again.');
                        $('#btn-save-cpf-statement').prop('disabled', false);
                        toastr.error(error_message);
                    }
                });
            });
        });
    </script>
    <?php endif; ?>
<?php $this->endSection() ?>
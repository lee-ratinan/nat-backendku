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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/journey/operator') ?>">Operator</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= $page_title ?></h5>
                        <?php
                        generate_form_field('mode_of_transport', $config['mode_of_transport'], @$operator['mode_of_transport']);
                        generate_form_field('operator_code_1', $config['operator_code_1'], @$operator['operator_code_1']);
                        generate_form_field('operator_code_2', $config['operator_code_2'], @$operator['operator_code_2']);
                        generate_form_field('operator_callsign', $config['operator_callsign'], @$operator['operator_callsign']);
                        generate_form_field('operator_name', $config['operator_name'], @$operator['operator_name']);
                        generate_form_field('operator_logo_file_name', $config['operator_logo_file_name'], @$operator['operator_logo_file_name']);
                        ?>
                        <div class="text-end">
                            <hr>
                            <button class="btn btn-sm btn-outline-primary" id="btn-save-journey-operator"><i class="fa-solid fa-save fa-fw me-2"></i> Save Operator</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#btn-save-journey-operator').click(function (e) {
                e.preventDefault();
                $.ajax({
                    url: '<?= base_url($session->locale . '/office/journey/operator/edit') ?>',
                    type: 'post',
                    data: {
                        id: '<?= $operator['id'] ?? 0 ?>',
                        mode: '<?= $mode ?>',
                        mode_of_transport: $('#mode_of_transport').val(),
                        operator_code_1: $('#operator_code_1').val(),
                        operator_code_2: $('#operator_code_2').val(),
                        operator_callsign: $('#operator_callsign').val(),
                        operator_name: $('#operator_name').val(),
                        operator_logo_file_name: $('#operator_logo_file_name').val(),
                    },
                    success: function (response) {
                        if ('success' === response.status) {
                            toastr.success(response.toast);
                            setTimeout(function () {
                                window.location.href = response.url;
                            }, 1000);
                        } else {
                            toastr.error(response.toast ?? 'Failed to save transport.');
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
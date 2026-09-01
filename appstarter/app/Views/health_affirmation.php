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
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fa-solid fa-list"></i> <?= $page_title ?></h5>
                        <h6 id="form_mode_label">New Affirmation Message</h6>
                        <?php generate_form_field('affirmation_message', [
                            'type'        => 'text',
                            'label'       => 'Affirmation Message',
                            'required'    => true,
                            'placeholder' => 'Affirmation Message',
                        ], ''); ?>
                        <?php generate_form_field('affirmation_id', [
                            'type'        => 'number',
                            'label'       => 'Affirmation ID',
                            'required'    => true,
                            'readonly'    => true,
                            'placeholder' => 'Affirmation ID',
                        ], '0'); ?>
                        <div class="text-end">
                            <button class="btn btn-outline-danger" id="cancel_affirmation_message">Cancel</button>
                            <button class="btn btn-primary" id="save_affirmation_message"><i class="fa-solid fa-save"></i> Save</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover" id="main_table">
                                <thead>
                                <tr>
                                    <th>Message</th>
                                    <th>Edit</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let toNewMode = function () {
                $('#affirmation_message').val('');
                $('#affirmation_id').val('0');
                $('#form_mode_label').html('New Affirmation Message');
            };
            let toEditMode = function (id, message) {
                $('#affirmation_message').val(message);
                $('#affirmation_id').val(id);
                $('#form_mode_label').html('Edit Affirmation Message');
            };
            const table = $('table').DataTable({
                processing: true,
                serverSide: true,
                fixedHeader: true,
                searching: true,
                pageLength: 25,
                ajax: {
                    url: '<?= base_url($session->locale . '/office/health/affirmation') ?>',
                    type: 'POST'
                },
                order: [[0, 'asc']],
            });
            // click edit, change form to edit mode
            $('body').on('click', '.btn-edit-message', function () {
                let message = $(this).data('message'),
                    id = $(this).data('id');
                toEditMode(id, message);
            })
            // click cancel, always default to new mode
            $('#cancel_affirmation_message').click(function (e) {
                e.preventDefault();
                toNewMode();
            });
            // click save
            $('#save_affirmation_message').click(function (e) {
                e.preventDefault();
                let message = $('#affirmation_message').val();
                let id = $('#affirmation_id').val();
                if (message.trim() === '') {
                    $('#affirmation_message').focus();
                    return;
                } else if (id === '') {
                    $('#affirmation_id').focus();
                    return;
                }
                $('#save_affirmation_message').prop('disabled', true);
                $.ajax({
                    url: '<?= base_url('en/office/health/affirmation/edit') ?>',
                    type: 'post',
                    data: {
                        id: id,
                        affirmation_message: message
                    },
                    success: function (response) {
                        if ('success' === response.status) {
                            // success, default form to new mode
                            toastr.success(response.toast);
                            setTimeout(function () {
                                toNewMode();
                                table.draw();
                                $('#save_affirmation_message').prop('disabled', false);
                            }, 5000);
                        } else {
                            // failed...
                            let message = (response.toast ?? 'Sorry! Something went wrong. Please try again.');
                            toastr.error(message);
                            $('#save_affirmation_message').prop('disabled', false);
                        }
                    },
                    error: function (xhr, status, error) {
                        let response = JSON.parse(xhr.responseText);
                        let error_message = (response.toast ?? 'Sorry! Something went wrong. Please try again.');
                        $('#save_affirmation_message').prop('disabled', false);
                        toastr.error(error_message);
                    }
                });
            })
        });
    </script>
<?php $this->endSection() ?>
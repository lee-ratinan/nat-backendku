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
                            'id',
                            'period_id',
                            'scheduled_start',
                            'scheduled_end',
                            'scheduled_hours',
                            'scheduled_break',
                            'work_location',
                        ];
                        foreach ($fields as $field) {
                            generate_form_field($field, $config[$field], @$row[$field]);
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
    <input type="hidden" id="total_hours" />
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#scheduled_start, #scheduled_end').change(function () {
                let start_ts = $('#scheduled_start').val(),
                    end_ts = $('#scheduled_end').val();
                if (start_ts && end_ts) {
                    const start_date = new Date(start_ts);
                    const end_date = new Date(end_ts);
                    const diffMs = end_date - start_date;
                    if (diffMs < 0) {
                        toastr.error('End time must be greater than start time.');
                        $('#scheduled_end').val('');
                        return;
                    }
                    const totalHours = diffMs / (1000 * 60 * 60);
                    const formattedHours = Number(totalHours.toFixed(2));
                    $('#total_hours').val(formattedHours);
                    $('#scheduled_hours').val('');
                    $('#scheduled_break').val('');
                }
            });
            $('#scheduled_hours').change(function () {
                let work = $('#scheduled_hours').val(),
                    total = $('#total_hours').val(),
                    diff = total - work;
                $('#scheduled_hours').val(Number(work).toFixed(2));
                $('#scheduled_break').val(Number(diff).toFixed(2));
            });
            $('#scheduled_break').change(function () {
                let break_time = $('#scheduled_break').val(),
                    total = $('#total_hours').val(),
                    diff = total - break_time;
                $('#scheduled_break').val(Number(break_time).toFixed(2));
                $('#scheduled_hours').val(Number(diff).toFixed(2));
            });
           $('#btn-save').click(function (e) {
               e.preventDefault();
               let ids = ['period_id', 'scheduled_start', 'scheduled_end', 'scheduled_hours', 'scheduled_break', 'work_location'];
               for (let i = 0; i < ids.length; i++) {
                   if ('' === $('#' + ids[i]).val()) {
                       toastr.warning('Please ensure all mandatory fields are filled.');
                       $('#' + ids[i]).focus();
                       return;
                   }
               }
               $(this).prop('disabled', true);
               $.ajax({
                   url: '<?= base_url('en/office/employment/part-time/edit') ?>',
                   type: 'post',
                   data: {
                       'id': '<?= $id ?>',
                       'period_id': $('#period_id').val(),
                       'scheduled_start': $('#scheduled_start').val(),
                       'scheduled_end': $('#scheduled_end').val(),
                       'scheduled_hours': $('#scheduled_hours').val(),
                       'scheduled_break': $('#scheduled_break').val(),
                       'work_location': $('#work_location').val(),
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
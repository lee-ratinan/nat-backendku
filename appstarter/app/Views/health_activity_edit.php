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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/health/activity') ?>">Activity</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body pt-3">
                        <div class="text-end mb-3">
                            <?php foreach ($record_cate as $key => $type) : ?>
                                <a class="btn btn-<?= ($record_type == $key ? '' : 'outline-') ?>primary btn-sm" href="<?= base_url($session->locale . '/office/health/activity/new/' . $key) ?>"><i class="fa-solid fa-plus-circle"></i> <?= $type ?></a>
                            <?php endforeach; ?>
                        </div>
                        <?php
                        $fields = [
                            'id',
                            'record_type',
                            'event_type',
                            'WHEN',
                            'journey_id',
                            'time_start_utc',
                            'time_end_utc',
                            'event_timezone',
                            'event_duration',
                            'duration_from_prev_ejac',
                            'is_ejac',
                            'SPA-INFORMATION',
                            'spa_name',
                            'spa_type',
                            'currency_code',
                            'price_amount',
                            'price_tip',
                            'NOTES',
                            'event_notes',
                            'event_location',
                            'previous_ejac_time_utc'
                        ];
                        $configuration['record_type']['options'] = [$record_type => $record_cate[$record_type]];
                        $record['record_type']                   = $record_type;
                        $configuration['event_type']['options']  = $record_types[$record_type];
                        $record['event_type']                    = array_keys($record_types[$record_type])[0];
                        $record['event_timezone']                = 'Asia/Singapore';
                        $record['event_duration']                = 0;
                        $record['duration_from_prev_ejac']       = 0;
                        $record['is_ejac']                       = ('ejac' == $record_type ? 'Y' : '');
                        if ('spa' != $record_type) {
                            $configuration['spa_name']['type']      = 'hidden';
                            $configuration['spa_type']['type']      = 'hidden';
                            $configuration['currency_code']['type'] = 'hidden';
                            $configuration['price_amount']['type']  = 'hidden';
                            $configuration['price_tip']['type']     = 'hidden';
                            $record['spa_name']                     = '';
                            $record['spa_type']                     = '';
                            $record['currency_code']                = '';
                            $record['price_amount']                 = 0;
                            $record['price_tip']                    = 0;
                        }
                        if ('chastity-end' == $mode || 'enlarge-end' == $mode) {
                            $start_time = '';
                            try {
                                $start_time = new DateTime($prev['time_start_utc'], new DateTimeZone('UTC'));
                                $start_time->setTimezone(new DateTimeZone($prev['event_timezone']));
                            } catch (Exception $e) {
                            }
                            $record['id']             = $prev['id'];
                            $record['time_start_utc'] = $start_time->format('Y-m-d H:i:s');
                            $record['event_timezone'] = $prev['event_timezone'];
                            $record['event_notes']    = $prev['event_notes'];
                            $record['event_location'] = $prev['event_location'];
                            $configuration['time_start_utc']['readonly'] = true;
                            $timezone_value           = $configuration['event_timezone']['options'][$prev['event_timezone']];
                            unset($configuration['event_timezone']['options']);
                            $configuration['event_timezone']['options'][$prev['event_timezone']] = $timezone_value;
                        }
                        $configuration['previous_ejac_time_utc']['type'] = 'hidden';
                        $record['previous_ejac_time_utc']                = $prev['time_end_utc'];
                        foreach ($fields as $field) {
                            if (in_array($field, ['NOTES', 'WHEN'])) {
                                echo '<h6 id="header-' . strtolower($field) . '">' . $field . '</h6>';
                            } else if ('SPA-INFORMATION' == $field) {
                                if ('spa' == $record_type) {
                                    echo '<h6 id="header-spa-information">SPA INFORMATION</h6>';
                                }
                            } else {
                                generate_form_field($field, $configuration[$field], @$record[$field]);
                            }
                        }
                        ?>
                        <div class="text-end">
                            <button class="btn btn-primary btn-sm" id="btn-save"><i class="fa-solid fa-save"></i> Save</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h6>Calculation Sheet</h6>
                        <div id="calculation-sheet"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let append_calculation = function (new_message) {
                $('#calculation-sheet').html($('#calculation-sheet').html() + '<br>' + new_message);
            };
            let calculate_different_in_minutes = function (time_start_utc, time_end_utc) {
                append_calculation('calculate_different_in_minutes');
                append_calculation(time_start_utc.toISO());
                append_calculation(time_end_utc.toISO());
                let diff_object = time_end_utc.diff(time_start_utc, ['minutes']).toString();
                diff_object = diff_object.replace('PT', '').replace('M', '');
                return diff_object;
            };
            let required_fields = [];
            // SPLIT
            <?php if ('ejac' == $record_type) : ?>
            required_fields = ['record_type', 'event_type', 'time_start_utc', 'time_end_utc', 'event_timezone', 'duration_from_prev_ejac', 'is_ejac'];
            $('#event_duration-block, #time_end_utc, #header-spa-information, #spa_name-block, #spa_type-block, #currency_code-block, #price_amount-block, #price_tip-block').hide();
            $('#is_ejac').val('Y');
            $('#time_start_utc').change(function () {
                $('#time_end_utc').val($(this).val());
            });
            $('#time_start_utc, #event_timezone').change(function () {
                let previous_time = DateTime.fromISO('<?= str_replace(' ', 'T', $prev['time_end_utc']) ?>', {zone: 'UTC'}),
                    this_time     = DateTime.fromISO($('#time_start_utc').val(), {zone: $('#event_timezone').val()}),
                    diff          = calculate_different_in_minutes(previous_time, this_time);
                append_calculation('Difference (min): ' + diff);
                $('#duration_from_prev_ejac').val(diff);
            });
            <?php elseif ('chastity-end' == $mode || 'enlarge-end' == $mode) : ?>
            required_fields = ['time_start_utc', 'time_end_utc', 'event_duration'];
            $('#record_type-block, #event_type-block, #journey_id-block, #duration_from_prev_ejac-block, #is_ejac-block, #header-spa-information, #spa_name-block, #spa_type-block, #currency_code-block, #price_amount-block, #price_tip-block').hide();
            $('#time_end_utc').change(function () {
                let this_start_time = DateTime.fromISO($('#time_start_utc').val(), {zone: $('#event_timezone').val()}),
                    this_end_time   = DateTime.fromISO($('#time_end_utc').val(), {zone: $('#event_timezone').val()}),
                    event_duration  = calculate_different_in_minutes(this_start_time, this_end_time);
                $('#event_duration').val(event_duration);
                append_calculation('Duration (min): ' + event_duration);
            });
            ////////////////////////////////////////////////////////////////////////////////////////////////////
            <?php elseif ('chastity' == $record_type || 'enlarge' == $record_type) : ?>
            required_fields = ['record_type', 'event_type', 'time_start_utc', 'event_timezone', 'is_ejac'];
            $('#time_end_utc, #event_duration, #duration_from_prev_ejac, #header-spa-information, #spa_name-block, #spa_type-block, #currency_code-block, #price_amount-block, #price_tip-block').hide();
            $('#is_ejac').val('N');
            $('#time_start_utc').change(function () {
                $('#time_end_utc').val($(this).val());
            });
            ////////////////////////////////////////////////////////////////////////////////////////////////////
            <?php elseif ('spa' == $record_type) : ?>
            required_fields = ['record_type', 'event_type', 'time_start_utc', 'time_end_utc', 'event_timezone', 'event_duration', 'is_ejac', 'spa_name', 'spa_type', 'currency_code', 'price_amount', 'price_tip'];
            $('#event_type').change(function () {
                let event_type = $(this).val();
                if ('clean' === event_type) {
                    $('#is_ejac').val('N');
                    $('#duration_from_prev_ejac').val('0');
                } else {
                    $('#is_ejac').val('Y');
                }
            });
            $('#time_start_utc, #time_end_utc, #event_timezone').change(function () {
                let previous_time   = DateTime.fromISO('<?= str_replace(' ', 'T', $prev['time_end_utc']) ?>', {zone: 'UTC'}),
                    this_start_time = DateTime.fromISO($('#time_start_utc').val(), {zone: $('#event_timezone').val()}),
                    this_end_time   = DateTime.fromISO($('#time_end_utc').val(), {zone: $('#event_timezone').val()}),
                    event_duration  = calculate_different_in_minutes(this_start_time, this_end_time),
                    from_prev       = calculate_different_in_minutes(previous_time, this_end_time);
                $('#event_duration').val(event_duration);
                if ('Y' === $('#is_ejac').val()) {
                    $('#duration_from_prev_ejac').val(from_prev);
                }
                append_calculation('Duration (min): ' + event_duration);
                append_calculation('From prev (min): ' + from_prev);
            });
            <?php endif; ?>
            // MERGED
            $('#btn-save').click(function (e) {
                e.preventDefault();
                let looping_field = '';
                for (let i = 0; i < required_fields.length; i++) {
                    looping_field = '#' + required_fields[i];
                    if ('' === $(looping_field).val()) {
                        toastr.warning('Please ensure all mandatory fields are filled.');
                        $(looping_field).focus();
                        return;
                    }
                }
                let time_start = $('#time_start_utc').val(),
                    time_end   = $('#time_end_utc').val(),
                    time_start_utc = new Date(time_start).toISOString(),
                    time_end_utc = new Date(time_end).toISOString();
                $(this).prop('disabled', true);
                $.ajax({
                    url: '<?= base_url('en/office/health/activity/edit') ?>',
                    type: 'post',
                    data: {
                        mode: '<?= $mode ?>',
                        <?php foreach ($fields as $field) : ?>
                        <?php
                        if (in_array($field, ['NOTES', 'WHEN', 'SPA-INFORMATION', 'previous_ejac_time_utc'])) {
                            continue;
                        } else if (in_array($field, ['time_start_utc', 'time_end_utc'])) {
                            echo "{$field}: {$field}, ";
                        } else {
                            echo "{$field}: $('#{$field}').val(), ";
                        }
                        ?>
                        <?php endforeach; ?>
                    },
                    success: function (response) {
                        if ('success' === response.status) {
                            toastr.success(response.toast);
                            setTimeout(function () {window.location.href = '<?= base_url('en/office/health/activity') ?>';}, 5000);
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
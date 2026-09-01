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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/journey/trip') ?>">Trip</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fa-solid fa-passport fa-fw me-3"></i> Master
                            <?php if ('edit' == $mode) : ?>
                                <span class="flag-icon flag-icon-<?= strtolower($trip_data['master_data']['country_code']) ?>"></span> ID: <?= $trip_data['master_data']['id'] ?>
                            <?php endif; ?>
                        </h5>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <?php
                                $visited_states = [];
                                if (isset($trip_data['master_data']['visited_states']) && !empty($trip_data['master_data']['visited_states'])) {
                                    $visited_states = explode(',', $trip_data['master_data']['visited_states']);
                                }
                                $all_states = [];
                                if (isset($trip_data['master_data']['country_code']) && !empty($trip_data['master_data']['country_code'])) {
                                    $states = lang('ListCountries.' . $trip_data['master_data']['country_code']);
                                    if (is_array($states)) {
                                        $all_states = $states;
                                        asort($all_states);
                                    }
                                }
                                $master_config['visited_states']['options'] = $all_states;
                                generate_form_field('country_code', $master_config['country_code'], @$trip_data['master_data']['country_code']);
                                if (!empty($all_states)) {
                                    generate_form_field('visited_states', $master_config['visited_states'], $visited_states);
                                }
                                generate_form_field('date_entry', $master_config['date_entry'], @$trip_data['master_data']['date_entry']);
                                generate_form_field('date_exit', $master_config['date_exit'], @$trip_data['master_data']['date_exit']);
                                generate_form_field('day_count', $master_config['day_count'], $trip_data['master_data']['day_count'] ?? '0');
                                generate_form_field('entry_port_id', $master_config['entry_port_id'], @$trip_data['master_data']['entry_port_id']);
                                generate_form_field('exit_port_id', $master_config['exit_port_id'], @$trip_data['master_data']['exit_port_id']);
                                ?>
                            </div>
                            <div class="col-12 col-md-6">
                                <?php
                                generate_form_field('trip_code', $master_config['trip_code'], @$trip_data['master_data']['trip_code']);
                                generate_form_field('visa_info', $master_config['visa_info'], @$trip_data['master_data']['visa_info']);
                                generate_form_field('trip_tags', $master_config['trip_tags'], @$trip_data['master_data']['trip_tags']);
                                generate_form_field('journey_details', $master_config['journey_details'], @$trip_data['master_data']['journey_details']);
                                generate_form_field('journey_status', $master_config['journey_status'], @$trip_data['master_data']['journey_status']);
                                ?>
                            </div>
                        </div>
                        <div class="text-end">
                            <hr>
                            <button class="btn btn-sm btn-outline-primary" id="btn-save-journey-master"><i class="fa-solid fa-save fa-fw me-2"></i> Save Trip</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ('edit' == $mode) : ?>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <a href="<?= base_url($session->locale . '/office/journey/transport/create/' . ($trip_data['master_data']['id'] * $nonces['transport'])) ?>" class="btn btn-sm btn-outline-primary float-end" target="_blank"><i class="fa-solid fa-plus-circle fa-fw me-2"></i> Add Transport</a>
                            <h5 class="card-title"><i class="fa-solid fa-person-walking-luggage fa-fw me-3"></i> Transport</h5>
                            <?php if (!empty($trip_data['transport_data'])) : ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-striped">
                                    <?php foreach ($trip_data['transport_data'] as $row) : ?>
                                        <?php $new_id = $row['id'] * $nonces['transport'] ?>
                                        <tr>
                                            <td style="min-width:50px">
                                                <a class="btn btn-sm btn-outline-primary" href="<?= base_url($session->locale . '/office/journey/transport/edit/' . $new_id) ?>" title="Edit"><i class="fa-solid fa-edit"></i></a>
                                            </td>
                                            <td style="min-width:60px">
                                                <img style="height:2.5rem" class="img-thumbnail" src="<?= base_url('file/operator-' . $row['operator_logo_file_name'] . '.png') ?>" alt="<?= $row['flight_number'] ?>"><br>
                                                <b><?= $row['flight_number'] ?></b>
                                            </td>
                                            <td style="min-width:150px;">
                                                <?= $modes_of_transport[$row['mode_of_transport']] ?><br>
                                                <?= $row['craft_type'] ?>
                                            </td>
                                            <td style="min-width:200px;">
                                                <b><?= $row['departure_port_name'] ?><?= !empty($row['departure_port_code']) ? ' [' . $row['departure_port_code'] . ']' : ''  ?></b><br>
                                                <?php if ('Y' == $row['is_time_known']) : ?>
                                                    <?= date(DATETIME_FORMAT_UI, strtotime($row['departure_date_time'])) ?>
                                                <?php else: ?>
                                                    <?= date(DATE_FORMAT_UI, strtotime($row['departure_date_time'])) ?>
                                                <?php endif; ?>
                                            </td>
                                            <td style="min-width:200px;">
                                                <b><?= $row['arrival_port_name'] ?><?= !empty($row['arrival_port_code']) ? ' [' . $row['arrival_port_code'] . ']' : ''  ?></b><br>
                                                <?php if ('Y' == $row['is_time_known']) : ?>
                                                    <?= date(DATETIME_FORMAT_UI, strtotime($row['arrival_date_time'])) ?>
                                                <?php else: ?>
                                                    <?= date(DATE_FORMAT_UI, strtotime($row['arrival_date_time'])) ?>
                                                <?php endif; ?>
                                            </td>
                                            <td style="min-width:150px;"><?= $row['journey_details'] ?></td>
                                            <td style="min-width:150px;"><span class="badge bg-<?= ('as_planned' == $row['journey_status'] ? 'success' : 'danger') ?>"><?= ucwords(strtolower(str_replace('_', ' ', $row['journey_status']))) ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                            <?php else: ?>
                                <p>No transport data found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <a href="<?= base_url($session->locale . '/office/journey/accommodation/create/' . ($trip_data['master_data']['id'] * $nonces['accommodation'])) ?>" class="btn btn-sm btn-outline-primary float-end" target="_blank"><i class="fa-solid fa-plus-circle fa-fw me-2"></i> Add Accommodation</a>
                            <h5 class="card-title"><i class="fa-solid fa-bed fa-fw me-3"></i> Accommodation</h5>
                            <?php if (!empty($trip_data['accommodation_data'])) : ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-striped">
                                    <?php foreach ($trip_data['accommodation_data'] as $row) : ?>
                                        <?php $new_id = $row['id'] * $nonces['accommodation'] ?>
                                        <tr>
                                            <td style="min-width:50px">
                                                <a class="btn btn-sm btn-outline-primary" href="<?= base_url($session->locale . '/office/journey/accommodation/edit/' . $new_id) ?>" title="Edit"><i class="fa-solid fa-edit"></i></a>
                                            </td>
                                            <td style="min-width:150px">
                                                <b><?= $row['hotel_name'] ?></b><br>
                                                [<?= $row['night_count'] ?> nights]
                                                <?= $row['room_type'] ?>
                                            </td>
                                            <td style="min-width:200px">
                                                Check-in:<br>
                                                <?php if (empty($row['accommodation_timezone'])) : ?>
                                                    <?= date(DATE_FORMAT_UI, strtotime($row['check_in_date'])) ?>
                                                <?php else: ?>
                                                    <?= date(DATETIME_FORMAT_UI, strtotime($row['check_in_date'])) ?>
                                                <?php endif; ?>
                                            </td>
                                            <td style="min-width:200px">
                                                Check-out:<br>
                                                <?php if (empty($row['accommodation_timezone'])) : ?>
                                                    <?= date(DATE_FORMAT_UI, strtotime($row['check_out_date'])) ?>
                                                <?php else: ?>
                                                    <?= date(DATETIME_FORMAT_UI, strtotime($row['check_out_date'])) ?>
                                                <?php endif; ?>
                                            </td>
                                            <td style="min-width:150px;"><span class="badge bg-<?= ('as_planned' == $row['journey_status'] ? 'success' : 'danger') ?>"><?= ucwords(strtolower(str_replace('_', ' ', $row['journey_status']))) ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                            <?php else: ?>
                                <p>No accommodation data found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <a href="<?= base_url($session->locale . '/office/journey/attraction/create/' . ($trip_data['master_data']['id'] * $nonces['attraction'])) ?>" class="btn btn-sm btn-outline-primary float-end" target="_blank"><i class="fa-solid fa-plus-circle fa-fw me-2"></i> Add Attraction</a>
                            <h5 class="card-title"><i class="fa-solid fa-ticket fa-fw me-3"></i> Attraction</h5>
                            <?php if (!empty($trip_data['attraction_data'])) : ?>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover table-striped">
                                        <?php foreach ($trip_data['attraction_data'] as $row) : ?>
                                            <?php $new_id = $row['id'] * $nonces['attraction'] ?>
                                            <tr>
                                                <td style="min-width:50px">
                                                    <a class="btn btn-sm btn-outline-primary" href="<?= base_url($session->locale . '/office/journey/attraction/edit/' . $new_id) ?>" title="Edit"><i class="fa-solid fa-edit"></i></a>
                                                </td>
                                                <td style="min-width:150px">
                                                    <b><?= $row['attraction_title'] ?></b><br>
                                                    <?= $row['attraction_type'] ?>
                                                </td>
                                                <td style="min-width:150px">
                                                    <?php
                                                    $date   = $row['attraction_date'];
                                                    $time   = substr($date, -8);
                                                    $format = DATETIME_FORMAT_UI;
                                                    if ('00:00:00' == $time) {
                                                        $format = DATE_FORMAT_UI;
                                                    }
                                                    echo date($format, strtotime($date));
                                                    ?>
                                                </td>
                                                <td style="min-width:150px;"><span class="badge bg-<?= ('as_planned' == $row['journey_status'] ? 'success' : 'danger') ?>"><?= ucwords(strtolower(str_replace('_', ' ', $row['journey_status']))) ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p>No attraction data found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fa-solid fa-mars fa-fw me-3"></i> Leisure</h5>
                            <?php if (!empty($trip_data['leisure_data'])) : ?>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover table-striped">
                                        <?php foreach ($trip_data['leisure_data'] as $row) : ?>
                                            <tr>
                                                <td style="min-width:225px;">
                                                    <?php
                                                    if ('enlarge' == $row['record_type']) {
                                                        echo 'Enlargement: ' . number_format($row['event_type']/10, 1) . 'cm';
                                                    } else {
                                                        echo $health_records[$row['record_type']][$row['event_type']];
                                                    }
                                                    ?>
                                                </td>
                                                <td style="min-width:175px;">
                                                    <?php
                                                    $local_tz   = new \DateTimeZone($row['event_timezone']);
                                                    $date_start = new \DateTime($row['time_start_utc'], new \DateTimeZone('UTC'));
                                                    $date_end   = $date_start;
                                                    $show_end   = false;
                                                    if ($row['time_start_utc'] != $row['time_end_utc']) {
                                                        $date_end = new \DateTime($row['time_end_utc'], new \DateTimeZone('UTC'));
                                                        $show_end = true;
                                                    }
                                                    echo $date_start->setTimezone($local_tz)->format(DATETIME_FORMAT_UI);
                                                    if ($show_end) {
                                                        echo '<br>to ' . $date_end->setTimezone($local_tz)->format(DATETIME_FORMAT_UI);
                                                    }
                                                    echo '<br><small>' . lang('ListTimeZones.timezones.' . $row['event_timezone'] . '.label') . '</small>'
                                                    ?>
                                                </td>
                                                <td style="min-width:175px;">
                                                    <?php
                                                    if (0 < $row['event_duration']) {
                                                        echo '<small>Duration:</small> ' . minute_format($row['event_duration']) . '<br>';
                                                    }
                                                    if (0 < $row['duration_from_prev_ejac']) {
                                                        echo minute_format($row['duration_from_prev_ejac']) . ' <em class="small">from previous</em><br>';
                                                    }
                                                    ?>
                                                </td>
                                                <td style="min-width:225px;">
                                                    <?php
                                                    if (!empty($row['spa_name']) || !empty($row['spa_type'])) {
                                                        echo '<b>' . $row['spa_name'] . '</b><br><small>' . $row['spa_type'] . '</small>';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p>No leisure data found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#date_entry, #date_exit').change(function () {
                let date_entry = $('#date_entry').val(),
                    date_exit = $('#date_exit').val();
                if ('' !== date_entry && '' !== date_exit) {
                    let date_entry_obj = new Date(date_entry),
                        date_exit_obj = new Date(date_exit),
                        day_count = Math.ceil((date_exit_obj - date_entry_obj) / (1000 * 60 * 60 * 24))+1;
                    $('#day_count').val(day_count);
                } else if ('' === date_exit) {
                    $('#day_count').val('0');
                }
            });
            $('#btn-save-journey-master').click(function (e) {
                e.preventDefault();
                const checked_states = $('input[name="visited_states[]"]:checked').map(function() {
                    return $(this).val();
                }).get();
                console.log(checked_states);
                let id = '<?= $trip_data['master_data']['id'] ?? 0 ?>',
                    mode = '<?= $mode ?>',
                    country_code = $('#country_code').val(),
                    visited_states = checked_states,
                    date_entry = $('#date_entry').val(),
                    date_exit = $('#date_exit').val(),
                    day_count = $('#day_count').val(),
                    entry_port_id = $('#entry_port_id').val(),
                    exit_port_id = $('#exit_port_id').val(),
                    trip_code = $('#trip_code').val(),
                    visa_info = $('#visa_info').val(),
                    trip_tags = $('#trip_tags').val(),
                    journey_details = $('#journey_details').val(),
                    journey_status = $('#journey_status').val();
                if ('' === country_code) {
                    toastr.info('Please select a country.');
                    return false;
                } else if ('' === date_entry) {
                    toastr.info('Please select the entry date.');
                    return false;
                } else if ('' === day_count) {
                    toastr.info('Please select the day count.');
                    return false;
                } else if ('' === visa_info) {
                    toastr.info('Please enter the visa information.');
                    return false;
                } else if ('' === journey_status) {
                    toastr.info('Please select the journey status.');
                    return false;
                }
                $.ajax({
                    url: '<?= base_url($session->locale . '/office/journey/trip/edit') ?>',
                    type: 'post',
                    data: {
                        id: id,
                        mode: mode,
                        country_code: country_code,
                        visited_states: visited_states,
                        date_entry: date_entry,
                        date_exit: date_exit,
                        day_count: day_count,
                        entry_port_id: entry_port_id,
                        exit_port_id: exit_port_id,
                        trip_code: trip_code,
                        visa_info: visa_info,
                        trip_tags: trip_tags,
                        journey_details: journey_details,
                        journey_status: journey_status
                    },
                    success: function (response) {
                        if ('success' === response.status) {
                            toastr.success(response.toast);
                            setTimeout(function () {
                                window.location.href = response.url;
                            }, 1000);
                        } else {
                            toastr.error(response.toast ?? 'Failed to save trip.');
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
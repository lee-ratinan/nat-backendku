<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Export Journey</title>
    <link rel="shortcut icon" href="<?= base_url('file/favicon.jpg') ?>" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu+Mono:wght@400..700&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/flag-icon/css/flag-icon.min.css') ?>" rel="stylesheet">
    <style>
        body {
            font-family: 'Ubuntu Mono', monospace;
            font-size: 14px !important;
            margin: 1rem auto;
        }
        h1 {font-size: 16px;}
        td, tr {font-size: 14px !important;}
    </style>
</head>
<?php
function print_hotel_time($datetime) {
    $split = explode(' ', $datetime);
    if ('00:00:00' != $split[1]) {
        return date('d M Y h:i A', strtotime($datetime));
    }
    return date('d M Y', strtotime($split[0]));
}
?>
<body>
<div class="container">
    <h1>Journey</h1>
    <table class="table table-hover table-borderless">
        <thead>
        <tr>
            <th>Country</th>
            <th>Details</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($journey as $journey_master_id => $data) : ?>
            <tr class="border-bottom">
                <td rowspan="4"><span class="flag-icon flag-icon-<?= strtolower($data['master']['country_code']) ?>"></span> <?= strtoupper($countries[$data['master']['country_code']]['common_name']) ?></td>
                <td>
                    <table class="table table-borderless mb-0 p-0">
                        <tr>
                            <td style="width:20%" class="p-0">
                                ENTRY : <?= strtoupper(date('d M Y', strtotime($data['master']['date_entry']))) ?><br>
                                EXIT &nbsp;: <?= strtoupper(date('d M Y', strtotime($data['master']['date_exit']))) ?><br>
                                DAYS &nbsp;: <?= number_format($data['master']['day_count']) ?>
                            </td>
                            <td style="width:40%" class="p-0">
                                <?= $data['master']['entry_port_name'] ? $data['master']['entry_port_code'] . ' ' . strtoupper($data['master']['entry_port_name']) : '-' ?><br>
                                <?= $data['master']['exit_port_name'] ?  $data['master']['exit_port_code']  . ' ' . strtoupper($data['master']['exit_port_name'])  : '-' ?><br>
                            </td>
                            <td style="width:40%" class="p-0">
                                STATUS &nbsp;: <?= ('as_planned' == $data['master']['journey_status'] ? 'CONFIRMED' : 'CANCELED') ?><br>
                                VISA&nbsp; &nbsp; : <?= strtoupper($data['master']['visa_info']) ?><br>
                                DETAILS : <?= strtoupper($data['master']['journey_details']) ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="border-bottom">
                <td>
                    <b>TRANSPORTATIONS</b><br>
                    <?php if (empty($data['transport'])) : ?>
                        == NO DATA ==
                    <?php else: ?>
                        <table class="table table-borderless mb-0 p-0">
                            <?php foreach ($data['transport'] as $transport) : ?>
                                <tr>
                                    <td rowspan="3" class="p-0" style="width:20px">+&nbsp;</td>
                                    <td colspan="2" class="p-0">
                                        OPER : <?= strtoupper($transport['operator_name']) ?>
                                        <?= ($transport['flight_number'] ? ' / ' . strtoupper($transport['flight_number']) : '') ?>
                                        <?= ($transport['pnr_number'] ? ' PNR : ' . strtoupper($transport['pnr_number']) : '') ?>
                                    </td>
                                    <td class="p-0" style="width:250px">
                                        STATUS &nbsp;: <?= ('as_planned' == $transport['journey_status'] ? 'CONFIRMED' : 'CANCELED') ?>
                                        <?php if (!empty($transport['google_drive_link'])) : ?>
                                            <a href="<?= $transport['google_drive_link'] ?>" target="_blank">[DL]</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="p-0">
                                        DEP &nbsp;: <?= $transport['departure_port_code'] . ' ' . strtoupper($transport['departure_port_name']) ?><br>
                                        ARR &nbsp;: <?= $transport['arrival_port_code'] . ' ' . strtoupper($transport['arrival_port_name']) ?><br>
                                        DIST : <?= number_format($transport['distance_traveled']) ?>KM
                                    </td>
                                    <td class="p-0">
                                        <?php if ('Y' == $transport['is_time_known']) : ?>
                                            <?= date('d M Y h:i A', strtotime($transport['departure_date_time'])) ?> / <?= strtoupper($transport['departure_timezone']) ?><br>
                                            <?= date('d M Y h:i A', strtotime($transport['arrival_date_time'])) ?> / <?= strtoupper($transport['arrival_timezone']) ?><br>
                                            DUR : <?= floor($transport['trip_duration'] / 60) ?>H <?= $transport['trip_duration'] % 60 ?>M
                                        <?php else : ?>
                                            <?= date('d M Y', strtotime($transport['departure_date_time'])) ?> UNKNOWN TIME
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-0">
                                        <?= strtoupper($transport['mode_of_transport']) ?> <?= $transport['craft_type'] ? ' / ' . strtoupper($transport['craft_type']) : '' ?><br>
                                        <?= 0 < $transport['price_amount'] ? 'PRICE &nbsp; : ' . $transport['price_currency_code'] . ' ' . number_format($transport['price_amount'], 2) . '<br>' : '' ?>
                                        <?= 0 < $transport['charged_amount'] ? 'CHARGED : ' . $transport['charged_currency_code'] . ' ' . number_format($transport['charged_amount'], 2) . '<br>' : '' ?>
                                    </td>
                                </tr>
                                <tr class="border-bottom">
                                    <td colspan="3" class="p-0">
                                        <?= $transport['journey_details'] ? 'DETS : ' . strtoupper($transport['journey_details']) . '<br>' : '' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                        TOTAL : <?= count($data['transport']) ?> RECORDS
                    <?php endif; ?>
                </td>
            </tr>
            <tr class="border-bottom">
                <td>
                    <b>ACCOMMODATIONS</b><br>
                    <?php if (empty($data['accommodation'])) : ?>
                        == NO DATA ==
                    <?php else: ?>
                        <table class="table table-borderless mb-0 p-0">
                            <?php foreach ($data['accommodation'] as $accommodation) : ?>
                                <tr>
                                    <td rowspan="3" class="p-0" style="width:20px">+&nbsp;</td>
                                    <td colspan="2" class="p-0">
                                        <?= strtoupper($accommodation['hotel_name']) ?><br>
                                        <?= $accommodation['hotel_address'] ? 'ADDR : ' . strtoupper($accommodation['hotel_address']) : '' ?>
                                    </td>
                                    <td class="p-0" style="width:250px">
                                        STATUS &nbsp;: <?= ('as_planned' == $accommodation['journey_status'] ? 'CONFIRMED' : 'CANCELED') ?>
                                        <?php if (!empty($accommodation['google_drive_link'])) : ?>
                                            <a href="<?= $accommodation['google_drive_link'] ?>" target="_blank">[DL]</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="p-0">
                                        CHK-IN &nbsp;: <?= print_hotel_time($accommodation['check_in_date']) ?><br>
                                        CHK-OUT : <?= print_hotel_time($accommodation['check_out_date']) ?><br>
                                        NIGHTS &nbsp;: <?= $accommodation['night_count'] ?>N
                                    </td>
                                    <td class="p-0">
                                        CHNL : <?= $accommodation['booking_channel'] ? strtoupper($accommodation['booking_channel']) : '-' ?><br>
                                        ROOM : <?= $accommodation['room_type'] ? strtoupper($accommodation['room_type']) : '-' ?><br>
                                        <?= 'Y' == $accommodation['breakfast_included'] ? 'BREAKFAST INCL.' : '' ?>
                                    </td>
                                    <td class="p-0">
                                        <?= 0 < $accommodation['price_amount'] ? 'PRICE &nbsp; : ' . $accommodation['price_currency_code'] . ' ' . number_format($accommodation['price_amount'], 2) . '<br>' : '' ?>
                                        <?= 0 < $accommodation['charged_amount'] ? 'CHARGED : ' . $accommodation['charged_currency_code'] . ' ' . number_format($accommodation['charged_amount'], 2) . '<br>' : '' ?>
                                    </td>
                                </tr>
                                <tr class="border-bottom">
                                    <td colspan="3" class="p-0">
                                        <?= $accommodation['journey_details'] ? 'DETS : ' . strtoupper($accommodation['journey_details']) . '<br>' : '' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                        TOTAL : <?= count($data['accommodation']) ?> RECORDS
                    <?php endif; ?>
                </td>
            </tr>
            <tr class="border-bottom">
                <td>
                    <b>ATTRACTIONS</b><br>
                    <?php if (empty($data['attraction'])) : ?>
                        == NO DATA ==
                    <?php else: ?>
                        <table class="table table-borderless mb-0 p-0">
                            <?php foreach ($data['attraction'] as $attraction) : ?>
                                <tr>
                                    <td rowspan="3" class="p-0" style="width:20px">+&nbsp;</td>
                                    <td class="p-0">
                                        <?= strtoupper($attraction['attraction_title']) ?>
                                    </td>
                                    <td class="p-0" style="width:250px">
                                        STATUS &nbsp;: <?= ('as_planned' == $attraction['journey_status'] ? 'CONFIRMED' : 'CANCELED') ?>
                                        <?php if (!empty($attraction['google_drive_link'])) : ?>
                                            <a href="<?= $attraction['google_drive_link'] ?>" target="_blank">[DL]</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="p-0">
                                        DATE : <?= date('d M Y', strtotime($attraction['attraction_date'])) ?><br>
                                        TYPE : <?= $attraction['attraction_type'] ? strtoupper($attraction['attraction_type']) : '-' ?>
                                    </td>
                                    <td class="p-0">
                                        <?= 0 < $attraction['price_amount'] ? 'PRICE &nbsp; : ' . $attraction['price_currency_code'] . ' ' . number_format($attraction['price_amount'], 2) . '<br>' : '' ?>
                                        <?= 0 < $attraction['charged_amount'] ? 'CHARGED : ' . $attraction['charged_currency_code'] . ' ' . number_format($attraction['charged_amount'], 2) . '<br>' : '' ?>
                                    </td>
                                </tr>
                                <tr class="border-bottom">
                                    <td colspan="2" class="p-0">
                                        <?= $attraction['journey_details'] ? 'DETS : ' . strtoupper($attraction['journey_details']) . '<br>' : '' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                        TOTAL : <?= count($data['attraction']) ?> RECORDS
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
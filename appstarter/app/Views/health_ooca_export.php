<html lang="th">
<head>
    <title>Ooca Visit Log Export</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="<?= base_url('appstack/css/app.css') ?>" rel="stylesheet"/>
    <link href="<?= base_url('file/favicon.jpg') ?>" rel="icon">
    <style>
        .container { max-width: 750px; }
        div, p { color: #222!important; }
        @media print {
            body, .container { background-color:#fff; color: #000; }
            .print-page-break { page-break-before: always; break-before: page; }
            body::before {
                content: "CONFIDENTIAL";
                position: fixed;top: 40%;left: 5%;transform: rotate(-45deg);
                font-size: 3em;color: rgba(0, 0, 0, 0.1);z-index: 9999;
                pointer-events: none;width: 100%;text-align: center;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col">
            <h1 class="my-3">บันทึกการพบจิตแพทย์ ประจำปี <?= $year ?></h1>
            <?php
            $fields = [
                'visit_date'          => '<i class="fa-solid fa-calendar-check"></i> วันที่',
                'psychologist_name'   => '<i class="fa-solid fa-user"></i> ชื่อผู้ให้คำปรึกษา',
                'note_what_happened'  => '<i class="fa-solid fa-question-circle"></i> อาการสำคัญ',
                'note_what_i_said'    => '<i class="fa-solid fa-comment"></i> สิ่งที่คุณพูด',
                'note_what_suggested' => '<i class="fa-solid fa-lightbulb"></i> สิ่งที่ผู้ให้คำปรึกษาแนะนำ',
            ];
            ?>
            <?php if (empty($records)) : ?>
                <p>ไม่มีรายการ</p>
            <?php else: ?>
                <?php foreach ($records as $record) : ?>
                    <?php
                    foreach ($fields as $field => $label) {
                        echo '<div class="mb-3"><b>' . $label . ':</b><div class="ms-3">';
                        if ('visit_date' == $field) {
                            echo date(DATE_FORMAT_UI, strtotime($record[$field]));
                        } else {
                            echo $record[$field];
                        }
                        echo '</div></div>';
                    }
                    ?>
                    <hr/>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
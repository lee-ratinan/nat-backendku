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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/health/ooca') ?>">OOCA Visit Log</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <?php
                        $fields = [
                            'visit_date'          => '<i class="fa-solid fa-calendar-check"></i> วันที่',
                            'psychologist_name'   => '<i class="fa-solid fa-user"></i> ชื่อผู้ให้คำปรึกษา',
                            'note_what_happened'  => '<i class="fa-solid fa-question-circle"></i> อาการสำคัญ',
                            'note_what_i_said'    => '<i class="fa-solid fa-comment"></i> สิ่งที่คุณพูด',
                            'note_what_suggested' => '<i class="fa-solid fa-lightbulb"></i> สิ่งที่ผู้ให้คำปรึกษาแนะนำ',
                        ];
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
                        <div class="text-end">
                            <a href="<?= base_url($session->locale . '/office/health/ooca/edit/' . ($record['id'] * $nonce)) ?>" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-edit"></i> Edit</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {});
    </script>
<?php $this->endSection() ?>
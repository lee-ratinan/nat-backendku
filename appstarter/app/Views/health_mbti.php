<?php
$layout = getenv('LAYOUT_FILE_OFFICE');
$layout = (!empty($layout) ? $layout : 'system/_layout_office');
$this->extend($layout);
function printCell($score, $high_label, $low_label, $color_class) {
    $low_score  = 100 - $score;
    $high_class = 'bg-' . $color_class;
    $low_class  = 'bg-secondary';
    $label      = '<div class="float-end"><small>'.$low_score.'</small></div> <b>'.$high_label.' '.$score.'</b>';
    if (51 > $score) { // low win
        $high_class = 'bg-secondary';
        $low_class  = 'bg-' . $color_class;
        $label      = '<div class="float-end"><b>'.$low_score.' '.$low_label.'</b></div> <small>'.$score.'</small>';
    }
    echo $label.'<div class="progress-stacked">
  <div class="progress" role="progressbar" aria-label="'.$high_label.'" aria-valuenow="'.$score.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$score.'%">
    <div class="progress-bar '.$high_class.'"></div>
  </div>
  <div class="progress" role="progressbar" aria-label="'.$low_label.'" aria-valuenow="'.$low_score.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$low_score.'%">
    <div class="progress-bar '.$low_class.'"></div>
  </div>
</div>';
}
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
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="text-center">
                                <tr>
                                    <th rowspan="2">Date</th>
                                    <th>Energy</th>
                                    <th>Mind</th>
                                    <th>Nature</th>
                                    <th>Tactics</th>
                                    <th>Identity</th>
                                    <th rowspan="2">Type</th>
                                </tr>
                                <tr>
                                    <th class="small">Introvert | Extrovert</th>
                                    <th class="small">Intuitive | Observant</th>
                                    <th class="small">Feeling | Thinking</th>
                                    <th class="small">Prospecting | Judging</th>
                                    <th class="small">Turbulent | Assertive</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($records as $record) : ?>
                                <tr>
                                    <td><?= date(DATE_FORMAT_UI, strtotime($record['date_taken'])) ?></td>
                                    <td><?php printCell($record['energy_introvert'], 'Introvert', 'Extrovert', 'info'); ?></td>
                                    <td><?php printCell($record['mind_intuitive'], 'Intuitive', 'Observant', 'warning'); ?></td>
                                    <td><?php printCell($record['nature_feeling'], 'Feeling', 'Thinking', 'success'); ?></td>
                                    <td><?php printCell($record['tactics_prospecting'], 'Prospecting', 'Judging', 'primary'); ?></td>
                                    <td><?php printCell($record['identity_turbulent'], 'Turbulent', 'Assertive', 'danger'); ?></td>
                                    <td><b style="font-size:1.2em"><?= $record['personality_code'] ?></b><br><?= $record['personality_type'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
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
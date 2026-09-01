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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/fiction') ?>">Fiction</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body pt-3">
                        <div class="row">
                            <div class="col">
                                <h2><?= $title['fiction_title'] ?></h2>
                                <p>
                                    by <?= $title['pen_name'] ?> |
                                    <?= $title['fiction_genre'] ?> |
                                    Last updated: <span class="utc-to-local"><?= date(DATETIME_FORMAT_LUXON, strtotime($title['updated_at'])) ?></span><br>
                                    Word count: <?= number_format($word_count) ?> |
                                    Character count: <?= number_format($char_count) ?>
                                </p>
                                <a class="btn btn-outline-primary" href="<?= base_url($session->locale . '/office/fiction/edit/' . $title_id) ?>">Edit</a>
                                <hr />
                                <div class="row">
                                    <div class="col"><a class="btn btn-outline-primary btn-sm" href="<?= base_url($session->locale . '/office/fiction/new-entry/' . $title_id) ?>"><i class="fa-solid fa-circle-plus"></i> New Entry</a></div>
                                    <div class="col text-end">
                                        <a class="btn btn-outline-primary btn-sm mb-2" href="<?= base_url($session->locale . '/office/fiction/export-pdf/' . $title['fiction_slug']) ?>" target="_blank"><i class="fa-solid fa-file-pdf"></i> Export Manuscript</a>
                                        <a class="btn btn-outline-primary btn-sm mb-2" href="<?= base_url($session->locale . '/office/fiction/export-research/' . $title['fiction_slug']) ?>" target="_blank"><i class="fa-solid fa-file-pdf"></i> Export Research</a>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover table-striped">
                                        <thead>
                                        <tr>
                                            <th rowspan="2" style="min-width:120px" class="text-center">Type</th>
                                            <th rowspan="2" style="min-width:50px" class="text-center">#</th>
                                            <th rowspan="2" style="min-width:225px" class="text-center">Title</th>
                                            <th colspan="2" style="min-width:200px" class="text-center">Count</th>
                                            <th rowspan="2" style="min-width:120px" class="text-center">Status</th>
                                            <th rowspan="2" style="min-width:250px" class="text-center">Short Note</th>
                                        </tr>
                                        <tr>
                                            <th class="text-end">Word</th>
                                            <th class="text-end">Character</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($entries as $position => $entry) : ?>
                                            <tr>
                                                <td><?= $types[$entry['entry_type']] ?></td>
                                                <td><?= $position ?></td>
                                                <td><?= (in_array($entry['entry_type'], ['scene', 'research']) ? '<i class="fa-solid fa-angles-right"></i> &nbsp; ' : '') ?><a href="<?= base_url($session->locale . '/office/fiction/edit-entry/' . ($entry['id']*$nonce)) ?>"><?= $entry['entry_title'] ?></a></td>
                                                <td><?= (0 < $entry['word_count'] ? number_format($entry['word_count']) : '-') ?></td>
                                                <td><?= (0 < $entry['char_count'] ? number_format($entry['char_count']) : '-') ?></td>
                                                <td><?= $statuses[$entry['entry_status']] ?></td>
                                                <td><?= $entry['entry_short_note'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <a class="btn btn-outline-primary btn-sm mb-3" href="<?= base_url($session->locale . '/office/fiction/new-entry/' . $title_id) ?>"><i class="fa-solid fa-circle-plus"></i> New Entry</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('table').DataTable({
                fixedHeader: true,
                searching: true,
                paging: false,
                order: [[1, 'asc']],
            });
            $('.utc-to-local').each(function (i, e) {let utc = $(this).text();$(this).text(utcToLocal(utc));});
        });
    </script>
<?php $this->endSection() ?>
<?php
$layout = getenv('LAYOUT_FILE_OFFICE');
$layout = (!empty($layout) ? $layout : 'system/_layout_office');
$this->extend($layout);
?>
<?= $this->section('content') ?>
<?php $session = session(); ?>
    <script src="<?= base_url('assets/vendor/amcharts5/index.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/amcharts5/xy.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/amcharts5/themes/Animated.js') ?>"></script>
    <div class="pagetitle">
        <h1><?= $page_title ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/dashboard') ?>"><?= lang('System.dashboard.page_title') ?></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/employment/cpf') ?>">CPF</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body pt-3">
                        <h2>CPF Growth</h2>
                        <div class="input-group flex-nowrap mb-3">
                            <span class="input-group-text" id="filter-menu">Filter</span>
                            <select id="filter-menu-options" class="form-select" aria-label="Filter" aria-describedby="filter-menu">
                                <optgroup label="Account">
                                    <option <?= ($current_filter == 'account/all' ? 'selected' : '') ?> value="account/all">Account / All</option>
                                    <option <?= ($current_filter == 'account/ordinary' ? 'selected' : '') ?> value="account/ordinary">Account / Ordinary</option>
                                    <option <?= ($current_filter == 'account/special' ? 'selected' : '') ?> value="account/special">Account / Special</option>
                                    <option <?= ($current_filter == 'account/medisave' ? 'selected' : '') ?> value="account/medisave">Account / MediSave</option>
                                </optgroup>
                                <optgroup label="Contributor">
                                    <option <?= ($current_filter == 'contributor/all' ? 'selected' : '') ?> value="contributor/all">Contributor / All</option>
                                    <option <?= ($current_filter == 'contributor/company' ? 'selected' : '') ?> value="contributor/company">Contributor / Company</option>
                                    <option <?= ($current_filter == 'contributor/staff' ? 'selected' : '') ?> value="contributor/staff">Contributor / Staff</option>
                                </optgroup>
                                <optgroup label="TC">
                                    <?php foreach ($tc_list as $tc) : ?>
                                        <option <?= ($current_filter == 'tc/' . $tc ? 'selected' : '') ?> value="tc/<?= $tc ?>">TC / <?= $tc ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                            </select>
                        </div>
                        <script>
                            <?php echo generate_line_chart_script($chart_data, 'main-chart', 'date', 'value');?>
                        </script>
                        <div id="main-chart" style="width:100%;height:500px"></div>
                        <div class="table-responsive mt-3">
                            <table class="table table-striped table-hover table-borderless table-sm mt-3">
                                <thead>
                                <tr>
                                    <th class="text-start">Date</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-end">Balance</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($chart_data as $row) : ?>
                                    <tr>
                                        <td class="text-start" data-sort="<?= $row['dt_str'] ?>"><?= date(DATE_FORMAT_UI, strtotime($row['dt_str'])) ?></td>
                                        <td class="text-end" data-sort="<?= $row['current'] ?>"><?= currency_format('SGD', $row['current']) ?></td>
                                        <td class="text-end" data-sort="<?= $row['value'] ?>"><?= currency_format('SGD', $row['value']) ?></td>
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
        document.addEventListener("DOMContentLoaded", function () {
            $('#filter-menu-options').change(function (e) {
                e.preventDefault();
                let filter = $(this).val();
                window.location.href = '<?= base_url($session->locale . '/office/employment/cpf/growth/') ?>' + filter;
            });
            $('table').DataTable({
                searching: false
            });
        });
    </script>
<?php $this->endSection() ?>
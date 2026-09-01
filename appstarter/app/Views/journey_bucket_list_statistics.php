<?php
$layout = getenv('LAYOUT_FILE_OFFICE');
$layout = (!empty($layout) ? $layout : 'system/_layout_office');
$this->extend($layout);
?>
<?= $this->section('content') ?>
<?php $session = session(); ?>
    <script src="<?= base_url('assets/vendor/amcharts5/index.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/amcharts5/xy.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/amcharts5/percent.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/amcharts5/themes/Animated.js') ?>"></script>
    <div class="pagetitle">
        <h1><?= $page_title ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/dashboard') ?>"><?= lang('System.dashboard.page_title') ?></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/journey/bucket-list') ?>">Bucket List</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2>By Category</h2>
                        <script>
                            <?php
                            echo generate_pie_chart_script($category_count, 'by-category', 'category', 'count');
                            ?>
                        </script>
                        <div id="by-category" style="height:500px"></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2>By Completion</h2>
                        <script>
                            <?php
                            echo generate_nested_pie_chart_script($completed_count, 'by-completion', 'category', 'count', 'status');
                            ?>
                        </script>
                        <div id="by-completion" style="height:500px"></div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h2>By Year</h2>
                        <script>
                            <?php
                            $height = (count($year_accomplishment_count) * 30) + 100;
                            echo generate_bar_chart_script($year_accomplishment_count, 'by-year', 'year', ['count' => 'Count'], $height . 'px');
                            ?>
                        </script>
                        <div id="by-year"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $this->endSection() ?>
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
    <style>#chartdiv {width: 100%;height: 500px;}</style>
    <div class="pagetitle">
        <h1><?= $page_title ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/dashboard') ?>"><?= lang('System.dashboard.page_title') ?></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/tax') ?>">Tax</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tax Comparison</h5>
                        <form method="GET">
                            <table class="table table-sm table-hover">
                                <tr>
                                    <td><label for="usd_from">FROM</label></td>
                                    <td><input class="form-control form-control-sm" type="number" id="usd_from" name="usd_from" value="<?= $usd_from ?>" /></td>
                                    <td><label for="usd_to">TO</label></td>
                                    <td><input class="form-control form-control-sm" type="number" id="usd_to" name="usd_to" value="<?= $usd_to ?>" /></td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="thbusd">THB RATE</label><br>
                                        <input class="form-control form-control-sm" type="number" id="thbusd" name="thbusd" value="<?= $rate['thb'] ?>" />
                                    </td>
                                    <td>
                                        <label for="sgdusd">SGD RATE</label><br>
                                        <input class="form-control form-control-sm" type="number" id="sgdusd" name="sgdusd" value="<?= $rate['sgd'] ?>" />
                                    </td>
                                    <td>
                                        <label for="audusd">AUD RATE</label><br>
                                        <input class="form-control form-control-sm" type="number" id="audusd" name="audusd" value="<?= $rate['aud'] ?>" />
                                    </td>
                                    <td class="text-end">
                                        <input class="btn btn-outline-primary btn-sm" type="submit" value="Calculate" />
                                    </td>
                                </tr>
                            </table>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hover">
                                <thead>
                                <tr class="text-center">
                                    <th>USD</th>
                                    <th colspan="3">Thailand</th>
                                    <th colspan="3">Singapore</th>
                                    <th colspan="3">Australia</th>
                                </tr>
                                <tr class="text-end">
                                    <th>Income</th>
                                    <th>Income</th>
                                    <th>%</th>
                                    <th>Tax</th>
                                    <th>Income</th>
                                    <th>Income</th>
                                    <th>%</th>
                                    <th>Tax</th>
                                    <th>%</th>
                                    <th>Tax</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $keys = ['thb', 'sgd', 'aud'];
                                ?>
                                <?php foreach ($data as $row) : ?>
                                <tr>
                                    <td class="text-end">
                                        <?= currency_format('USD', $row['usd']) ?><br>
                                        <span class="badge bg-primary rounded-pill">A</span>
                                        <small><?= currency_format('USD', $row['usd_annual']) ?></small>
                                    </td>
                                    <?php foreach ($keys as $currency_code) : ?>
                                        <?php
                                        $class = ($row[$currency_code]['in_market_rate'] ? 'text-success' : '');
                                        ?>
                                        <?php $upper_code = strtoupper($currency_code); ?>
                                        <td class="text-end <?= $class ?>">
                                            <?= currency_format($upper_code, $row[$currency_code]['monthly_income']) ?>
                                            <br>
                                            <span class="badge bg-primary rounded-pill">A</span>
                                            <small><?= currency_format($upper_code, $row[$currency_code]['annual_income']) ?></small>
                                        </td>
                                        <td class="text-end <?= $class ?>"><?= $row[$currency_code]['max_rate'] ?></td>
                                        <td class="text-end <?= $class ?>">
                                            <?= currency_format($upper_code, $row[$currency_code]['total_tax']) ?><br>
                                            <span class="badge bg-primary rounded-pill">USD</span>
                                            <?= currency_format('USD', $row[$currency_code]['total_tax_usd']) ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div id="chartdiv"></div>
                        <script>
                            am5.ready(function() {
                                let root = am5.Root.new("chartdiv");
                                root.setThemes([am5themes_Animated.new(root)]);
                                let chart = root.container.children.push(am5xy.XYChart.new(root, {
                                    panX: false,
                                    panY: false,
                                    paddingLeft: 0,
                                    wheelX: "panX",
                                    wheelY: "zoomX",
                                    layout: root.verticalLayout
                                }));
                                let legend = chart.children.push(
                                    am5.Legend.new(root, {
                                        centerX: am5.p50,
                                        x: am5.p50
                                    })
                                );
                                let data = <?= json_encode($chart) ?>;
                                let xRenderer = am5xy.AxisRendererX.new(root, {
                                    cellStartLocation: 0.1,
                                    cellEndLocation: 0.9,
                                    minorGridEnabled: true
                                })
                                let xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
                                    categoryField: "x",
                                    renderer: xRenderer,
                                    tooltip: am5.Tooltip.new(root, {})
                                }));
                                xRenderer.grid.template.setAll({location: 1});
                                xAxis.data.setAll(data);
                                let yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
                                    renderer: am5xy.AxisRendererY.new(root, {strokeOpacity: 0.1})
                                }));
                                function makeSeries(name, fieldName) {
                                    let series = chart.series.push(am5xy.ColumnSeries.new(root, {
                                        name: name,
                                        xAxis: xAxis,
                                        yAxis: yAxis,
                                        valueYField: fieldName,
                                        categoryXField: "x"
                                    }));
                                    series.columns.template.setAll({
                                        tooltipText: "{name}, {categoryX}: {valueY}",
                                        width: am5.percent(90),
                                        tooltipY: 0,
                                        strokeOpacity: 0
                                    });
                                    series.data.setAll(data);
                                    series.appear();
                                    series.bullets.push(function () {
                                        return am5.Bullet.new(root, {
                                            locationY: 0,
                                            sprite: am5.Label.new(root, {
                                                text: "{valueY}",
                                                fill: root.interfaceColors.get("alternativeText"),
                                                centerY: 0,
                                                centerX: am5.p50,
                                                populateText: true
                                            })
                                        });
                                    });
                                    legend.data.push(series);
                                }
                                makeSeries("Thailand", "y1");
                                makeSeries("Singapore", "y2");
                                makeSeries("Australia", "y3");
                                chart.appear(1000, 100);
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $this->endSection() ?>
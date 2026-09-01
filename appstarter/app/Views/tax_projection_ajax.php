<div class="table-responsive">
    <table class="table table-sm table-striped table-hover">
        <thead>
        <tr>
            <th>Monthly Income</th>
            <th>Annual Income</th>
            <th>Deduction</th>
            <th>Taxable Income</th>
            <th>Max %</th>
            <th>Total Tax</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($tax_details as $row) : ?>
            <?php
            $market_rate = '';
            if ($green_range[0] <= $row['monthly_income'] && $row['monthly_income'] <= $green_range[1]) {
                $market_rate = 'text-success';
            }
            $last_line = end($row['lines'])
            ?>
        <tr>
            <td class="text-end <?= $market_rate ?>"><?= number_format($row['monthly_income'], 2) ?></td>
            <td class="text-end <?= $market_rate ?>"><?= number_format($row['annual_income'], 2) ?></td>
            <td class="text-end <?= $market_rate ?>"><?= number_format($row['deduction'], 2) ?></td>
            <td class="text-end <?= $market_rate ?>"><?= number_format($row['taxable_income'], 2) ?></td>
            <td class="text-end"><?= number_format($last_line['rate'], 2) ?>%</td>
            <td class="text-end <?= $market_rate ?>"><?= number_format($row['total_tax'], 2) ?></td>
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
            panX: true,
            panY: true,
            wheelX: "panX",
            wheelY: "zoomX",
            pinchZoomX: true,
            paddingLeft:0,
            paddingRight:1
        }));
        let cursor = chart.set("cursor", am5xy.XYCursor.new(root, {}));
        cursor.lineY.set("visible", false);
        let xRenderer = am5xy.AxisRendererX.new(root, {
            minGridDistance: 30,
            minorGridEnabled: true
        });

        xRenderer.labels.template.setAll({
            rotation: -90,
            centerY: am5.p50,
            centerX: am5.p100,
            paddingRight: 15
        });

        xRenderer.grid.template.setAll({
            location: 1
        })

        let xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
            maxDeviation: 0.3,
            categoryField: "x",
            renderer: xRenderer,
            tooltip: am5.Tooltip.new(root, {})
        }));

        let yRenderer = am5xy.AxisRendererY.new(root, {
            strokeOpacity: 0.1
        })

        let yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
            maxDeviation: 0.3,
            renderer: yRenderer
        }));
        let series = chart.series.push(am5xy.ColumnSeries.new(root, {
            name: "Tax",
            xAxis: xAxis,
            yAxis: yAxis,
            valueYField: "y",
            sequencedInterpolation: true,
            categoryXField: "x",
            tooltip: am5.Tooltip.new(root, {labelText: "{l}"})
        }));
        series.columns.template.setAll({ cornerRadiusTL: 5, cornerRadiusTR: 5, strokeOpacity: 0 });
        series.columns.template.adapters.add("fill", function (fill, target) {
            return chart.get("colors").getIndex(series.columns.indexOf(target));
        });
        series.columns.template.adapters.add("stroke", function (stroke, target) {
            return chart.get("colors").getIndex(series.columns.indexOf(target));
        });
        let data = <?= json_encode($graph); ?>;
        xAxis.data.setAll(data);
        series.data.setAll(data);
        series.appear(1000);
        chart.appear(1000, 100);
    });
</script>
<?php
$layout = getenv('LAYOUT_FILE_OFFICE');
$layout = (!empty($layout) ? $layout : 'system/_layout_office');
$this->extend($layout);
?>
<?php $session = session(); ?>
<?= $this->section('content') ?>
    <style>
        #world-div, #country-div { width:90%;height:80vh;margin:10px auto; }
    </style>
    <script src="<?= base_url('assets/vendor/amcharts5/index.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/amcharts5/map.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/amcharts5/geodata/worldLow.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/amcharts5/geodata/countries2.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/amcharts5/themes/Animated.js') ?>"></script>
    <div class="pagetitle">
        <h1><?= $page_title ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                            href="<?= base_url($session->locale . '/office/dashboard') ?>"><?= lang('System.dashboard.page_title') ?></a>
                </li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div id="world-div"></div>
                    <div id="country-name" class="text-center"><div id="country-name-label"></div><a class='btn btn-outline-success mb-3' id='btn-back-world'>Show World Map</a></div>
                    <div id="country-div"></div>
                </div>
                <p class="small">Wishlist and banned countries are hardcoded, but the visited list came from the <code>trip_master</code> table.</p>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#country-div, #country-name').hide();
            am5.ready(function() {
                let groupData = <?= json_encode($visited_countries) ?>;
                let root = am5.Root.new("world-div");
                // Set themes
                root.setThemes([am5themes_Animated.new(root)]);
                // Create chart
                let chart = root.container.children.push(am5map.MapChart.new(root, {panX: "rotateX", panY: "translateY", projection: am5map.geoMercator(), homeGeoPoint: {latitude: 20, longitude: 0}}));
                // Button
                let cont = chart.children.push(am5.Container.new(root, {layout: root.horizontalLayout, x: 20, y: 40}));
                cont.children.push(am5.Label.new(root, {centerY: am5.p50, text: "Map"}));
                let switchButton = cont.children.push(am5.Button.new(root, {themeTags: ["switch"], centerY: am5.p50, icon: am5.Circle.new(root, {themeTags: ["icon"]})}));
                switchButton.on("active", function() {
                    if (!switchButton.get("active")) {
                        chart.set("projection", am5map.geoMercator());
                        chart.set("panY", "translateY");
                        chart.set("rotationY", 0);
                        // backgroundSeries.mapPolygons.template.set("fillOpacity", 0);
                    } else {
                        chart.set("projection", am5map.geoOrthographic());
                        chart.set("panY", "rotateY")
                        // backgroundSeries.mapPolygons.template.set("fillOpacity", 0.1);
                    }
                });
                cont.children.push(am5.Label.new(root, {centerY: am5.p50, text: "Globe"}));
                // Create world polygon series
                let worldSeries = chart.series.push(am5map.MapPolygonSeries.new(root, {
                    geoJSON: am5geodata_worldLow,
                    exclude: ['AQ']
                }));
                worldSeries.mapPolygons.template.setAll({fill: am5.color(0xcccccc)});
                worldSeries.events.on("datavalidated", () => {chart.goHome();});
                // Add legend
                let legend = chart.children.push(am5.Legend.new(root, {
                    useDefaultMarker: true, centerX: am5.p50, x: am5.p50, centerY: am5.p100, y: am5.p100, dy: -20,
                    background: am5.RoundedRectangle.new(root, {fill: am5.color(0xffffff), fillOpacity: 0.2})
                }));
                legend.valueLabels.template.set("forceHidden", true)
                // Create series for each group
                am5.array.each(groupData, function(group) {
                    let countries = [];
                    let color = am5.color(group.color);
                    am5.array.each(group.data, function(country) {countries.push(country.id)});
                    let polygonSeries = chart.series.push(am5map.MapPolygonSeries.new(root, {
                        geoJSON: am5geodata_worldLow, include: countries,
                        name: group.name, fill: color
                    }));
                    polygonSeries.mapPolygons.template.setAll({
                        tooltipText: "[bold]{name}[/]\n{detail}", interactive: true,
                        fill: color, strokeWidth: 0.5
                    });
                    polygonSeries.mapPolygons.template.states.create("hover", {fill: am5.Color.brighten(color, -0.3)});
                    polygonSeries.mapPolygons.template.events.on("click", function(ev) {
                        if ("Banned" !== ev.target.dataItem.dataContext.detail && "Wishlist" !== ev.target.dataItem.dataContext.detail) {
                            drawCountryMap(ev.target.dataItem.dataContext.id);
                        }
                    });
                    polygonSeries.mapPolygons.template.events.on("pointerover", function(ev) {
                        ev.target.series.mapPolygons.each(function(polygon) { polygon.states.applyAnimate("hover"); });
                    });
                    polygonSeries.mapPolygons.template.events.on("pointerout", function(ev) {
                        ev.target.series.mapPolygons.each(function(polygon) { polygon.states.applyAnimate("default"); });
                    });
                    polygonSeries.data.setAll(group.data);
                    legend.data.push(polygonSeries);
                });
            });
            let visitedSubdivisions = <?= json_encode($visited_states) ?>;
            function drawCountryMap(countryCode) {
                // Check if the country exists in the JSON
                if (!visitedSubdivisions[countryCode]) { console.log("No data available for country:", countryCode); return; }
                // Clear the previous map
                am5.array.each(am5.registry.rootElements, function(root) { if (root.dom.id === "country-div") { root.dispose(); } });
                // Create root element for the new chart
                let root = am5.Root.new("country-div");
                root.setThemes([am5themes_Animated.new(root)]);
                let chart = root.container.children.push(am5map.MapChart.new(root, {projection: am5map.geoMercator()}));
                // Dynamically load the geodata file for the selected country
                let currentMap = "usaLow";
                let title = "United States";
                if (am5geodata_data_countries2[countryCode] !== undefined) {
                    currentMap = am5geodata_data_countries2[countryCode]["maps"][0];
                    if (am5geodata_data_countries2[countryCode]["country"]) {
                        title = am5geodata_data_countries2[countryCode]["country"];
                    }
                }
                $('#country-name-label').html("<h3 class='text-center my-3'>"+title+"</h3>");
                let chart_data_url = "<?= base_url('assets/vendor/amcharts5/geodata/json/') ?>" + currentMap + ".json";
                console.log(chart_data_url);
                am5.net.load(chart_data_url, chart).then(function (result) {
                    let geodata = am5.JSONParser.parse(result.response);
                    let polygonSeries = chart.series.push(am5map.MapPolygonSeries.new(root, {geoJSON: geodata}));
                    polygonSeries.mapPolygons.template.setAll({
                        fill: am5.color("#ccc"),
                        stroke: am5.color("#000"),
                        tooltipText: "{name}"
                    });
                    polygonSeries.events.on("datavalidated", function() {
                        polygonSeries.mapPolygons.each(function(polygon) {
                            let id = polygon.dataItem.dataContext.id;
                            if (visitedSubdivisions[countryCode].includes(id)) {
                                polygon.set("fill", am5.color("#7ddd68"));
                            }
                        });
                    });
                });
                $('#country-div, #country-name').show();
                $('#world-div').hide();
            }
            $('body').on('click', '#btn-back-world', function (e) {
                e.preventDefault();
                $('#country-div, #country-name').hide();
                $('#world-div').show();
            });
        });
    </script>
<?php $this->endSection() ?>
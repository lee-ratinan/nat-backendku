<?php

/**
 * *********************************************************************
 * THIS FILE IS SYSTEM HELPER, PLEASE REFRAIN FROM MAKING
 * ANY CHANGES TO THIS FILE UNLESS YOU KNOW WHAT YOU ARE DOING.
 * *********************************************************************
 */

use App\Models\HealthAffirmationModel;

/**
 * Generate any form field with floating label
 * @param string $id
 * @param array $configuration
 * @param int|string|array|null $current_value (optional)
 * @return void
 */
function generate_form_field(string $id, array $configuration, int|string|array $current_value = null): void
{
    $input_type = $configuration['type'];
    $required   = (@$configuration['required'] ? 'required' : '');
    $readonly   = (@$configuration['readonly'] ? 'readonly' : '');
    $disabled   = (@$configuration['disabled'] ? 'disabled' : '');
    $min        = (@$configuration['min'] ? "min='{$configuration['min']}'" : '');
    $max        = (@$configuration['max'] ? "max='{$configuration['max']}'" : '');
    $minlength  = (@$configuration['minlength'] ? "minlength='{$configuration['minlength']}'" : '');
    $maxlength  = (@$configuration['maxlength'] ? "maxlength='{$configuration['maxlength']}'" : '');
    $label      = (isset($configuration['label_key']) ? lang($configuration['label_key']) : @$configuration['label']);
    if (in_array($input_type, ['text', 'email', 'password', 'number', 'date', 'time', 'datetime-local', 'month', 'week', 'url', 'search', 'color'])) {
        $placeholder = @$configuration['placeholder'] ?? '';
        $value = (!is_null($current_value) && '0000-00-00' != $current_value ? "value='{$current_value}'" : (!empty($configuration['default']) ? "value='{$configuration['default']}'" : ''));
        echo "<div class='form-floating mb-3' id='{$id}-block'><input type='{$input_type}' class='form-control' id='{$id}' name='{$id}' placeholder='{$placeholder}' $value $required $readonly $disabled $min $minlength $max $maxlength><label for='{$id}'>" . $label . "</label>";
        if (!empty($configuration['details'])) {
            echo "<small class='form-text text-muted small'>" . lang($configuration['details']) . "</small>";
        }
        if (!empty($configuration['copy-to-field'])) {
            generate_link_for_copy_to_field($configuration['copy-to-field'], $id);
        }
        echo "</div>";
    } else if ('tel' == $input_type) {
        $country_codes = lang('ListCallingCode.codes');
        echo "<div class='input-group mb-3' id='{$id}-block'><span class='input-group-text'>+</span>";
        echo "<div class='form-floating'><select class='form-select' id='{$configuration['country_code_field']}' name='{$configuration['country_code_field']}' $required $readonly $disabled>";
        echo "<option value=''></option>";
        foreach ($country_codes as $codes) {
            echo '<option value="' . $codes['code'] . '" ' . ($current_value[0] == $codes['code'] ? 'selected' : '') . '>' . $codes['label'] . ', ' . $codes['code_label'] . '</option>';
        }
        echo "</select><label for='{$configuration['country_code_field']}'>" . lang($configuration['country_code_label']) . "</label></div>";
        echo "<div class='form-floating'><input type='tel' class='form-control' id='{$configuration['phone_number_field']}' name='{$configuration['phone_number_field']}' placeholder='{$configuration['placeholder']}' value='{$current_value[1]}' $required $readonly $disabled $min $minlength $max $maxlength>";
        echo "<label for='{$configuration['phone_number_field']}'>" . lang($configuration['phone_number_label']) . "</label>";
        echo "</div></div>";
    } else if ('hidden' == $input_type) {
        echo '<input type="hidden" id="' . $id . '" name="' . $id . '" value="' . @$current_value . '">';
    } else if ('select' == $input_type) {
        $options = $configuration['options'];
        echo "<div class='form-floating mb-3' id='{$id}-block'><select class='form-select' id='{$id}' name='{$id}' $required $readonly $disabled>";
        echo "<option value=''></option>";
        foreach ($options as $key => $value) {
            $selected  = ($current_value == $key ? 'selected' : '');
            $str_value = lang($value);
            echo "<option value='{$key}' $selected>" . $str_value . "</option>";
        }
        echo "</select><label for='{$id}'>" . $label . "</label></div>";
    } else if ('textarea' == $input_type) {
        $placeholder = @$configuration['placeholder'] ?? '';
        echo "<div class='form-floating mb-3' id='{$id}-block'><textarea class='form-control' id='{$id}' name='{$id}' placeholder='{$placeholder}' $required $readonly $disabled style='height:100px'>{$current_value}</textarea><label for='{$id}'>" . $label . "</label>";
        if (!empty($configuration['details'])) {
            echo "<small class='form-text text-muted small'>" . lang($configuration['details']) . "</small>";
        }
        echo "</div>";
    } else if ('tinymce' == $input_type) {
        $placeholder = @$configuration['placeholder'] ?? '';
        echo "<div class='mb-3' id='{$id}-block'><label class='mb-1' for='{$id}'>" . $label . "</label>";
        echo "<button class='btn btn-link' id='{$id}-expand-btn' onclick='expandTinyMceArea(\"{$id}\")'><i class='fa-solid fa-up-right-and-down-left-from-center'></i> Expand</button>";
        echo "<button class='btn btn-link' id='{$id}-shrink-btn' onclick='shrinkTinyMceArea(\"{$id}\")'style='display:none'><i class='fa-solid fa-down-left-and-up-right-to-center'></i> Shrink</button>";
        echo "<br><textarea class='form-control tinymce' id='{$id}' name='{$id}' placeholder='{$placeholder}' $required $readonly $disabled>{$current_value}</textarea>";
        if (!empty($configuration['details'])) {
            echo "<small class='form-text text-muted small'>" . lang($configuration['details']) . "</small>";
        }
        echo "</div>";
    } else if ('multiple-checkbox' == $input_type) {
        $options = $configuration['options'];
        $height  = 250;
        $count   = count($options);
        if ($count < 10) {
            $height = 100;
        } else if ($count < 30) {
            $height = 150;
        } else if ($count < 50) {
            $height = 200;
        }
        echo "<div class='form-floating mb-3 px-2' style='height:{$height}px;overflow:auto;' id='{$id}-block'>";
        echo '<div class="row"><div class="col-12"><label for="' . $id . '_' . array_key_first($options) . '">' . $configuration['label'] . '</label></div>';
        foreach ($options as $key => $value) {
            $checked = (in_array($key, $current_value) ? 'checked' : '');
            echo "<div class='col-6 form-check'>";
            echo "<input class='form-check-input ms-1 me-2' type='checkbox' id='{$id}_{$key}' name='{$id}[]' value='{$key}' $checked>";
            echo "<label class='form-check-label' for='{$id}_{$key}'>{$value}</label>";
            echo "</div>";
        }
        echo '</div></div>';
    }
}

function generate_link_for_copy_to_field (array $items_to_copy, string $target_field_id): void
{
    if (!empty($items_to_copy)) {
        echo '<div class="form-text text-muted small">';
        foreach ($items_to_copy as $value) {
            echo '<a href="#" class="copy-to-field me-3" data-target-id="' . $target_field_id . '" data-str-to-copy="' . $value . '">' . $value . '</a>';
        }
        echo '</div>';
    }
}

/**
 * Generate the label with value in a .col div
 * @param string $label
 * @param string $value
 * @param string $type Either 'text' or 'datetime' for now
 * @return void
 */
function generate_label_column_from_field(string $label, string $value, string $type = 'text'): void
{
    echo "<div class='col'><small>{$label}</small><br>";
    if ('datetime' == $type) {
        if (empty(trim($value))) {
            echo '-';
        } else {
            $value = str_replace(' ', 'T', $value) . 'Z';
            echo "<span class='utc-to-local-time'>{$value}</span>";
        }
    } else {
        echo (empty(trim($value)) ? '-' : trim($value));
    }
    echo "</div>";
}

/**
 * Retrieve app logo or the styled app name
 * @param string $app_name
 * @return string
 */
function retrieve_app_logo(string $app_name): string
{
    $clean_name = preg_replace('/[^a-z0-9]/i', '', strtolower($app_name));
    $file_url   = base_url('file/logo_' . $clean_name . '.png');
    $file_path  = WRITEPATH . 'uploads/logo_' . $clean_name . '.png';
    if (file_exists($file_path)) {
        return '<img class="img-fluid" src="' . $file_url . '" alt="' . $app_name . '" />';
    }
    return '<span class="app-logo-text">' . $app_name . '</span>';
}

/**
 * Create avatar
 * @param string $email_address
 * @param string $first_name
 * @param string $last_name
 * @return string
 */
function retrieve_avatars(string $email_address, string $first_name, string $last_name): string
{
    $email_address  = preg_replace('/[^a-z0-9]/i', '', strtolower($email_address));
    $file_url       = base_url('file/profile_picture_' . $email_address . '.jpg');
    $file_path      = WRITEPATH . 'uploads/profile_pictures/profile_' . $email_address . '.jpg';
    if (file_exists($file_path)) {
        return "<img src='" . $file_url . "' class='avatar-img' title='$first_name $last_name' data-bs-toggle='tooltip' data-bs-placement='top'>";
    }
    $hash = hash('md5', $email_address . $first_name . $last_name);
    $color = '#' . substr($hash, 0, 6);
    $r = hexdec(substr($hash, 0, 2));
    $g = hexdec(substr($hash, 2, 2));
    $b = hexdec(substr($hash, 4, 2));
    $avg = (($r/255*100) + ($g/255*100) + ($b/255*100))/3;
    $text_color = '#fff';
    if ($avg > 50) {
        $text_color = '#000';
    }
    $initials = strtoupper(substr($first_name, 0, 1) . substr($last_name, 0, 1));
    return "<div class='avatar-txt' style='background-color:$color;color:$text_color' title='$first_name $last_name' data-bs-toggle='tooltip' data-bs-placement='top'>$initials</div>";
}

/**
 * Retrieve common password
 * @return string
 */
function retrieve_common_password(): string
{
    return '123|password|qwerty|111|letmein|1q2w3e|aaa|football|iloveyou|admin|princess|dragon|welcome|hello|world|master';
}

/**
 * Format phone numbers for some supported countries
 * @param string $country_code
 * @param string $phone_number
 * @return string
 */
function format_phone_number(string $country_code, string $phone_number): string
{
    if ('+1' == $country_code) {
        // United States
        return '+1 (' . substr($phone_number, 0, 3) . ') ' . substr($phone_number, 3, 3) . ' ' . substr($phone_number, 6);
    } else if ('+66' == $country_code) {
        // Thailand
        if (str_starts_with($phone_number, '0')) {
            $phone_number = substr($phone_number, 1);
        }
        if (8 == strlen($phone_number)) {
            return '+66-' . substr($phone_number, 0, 1) . '-' . substr($phone_number, 1, 3) . '-' . substr($phone_number, 4);
        }
        return '+66-' . substr($phone_number, 0, 2) . '-' . substr($phone_number, 2, 3) . '-' . substr($phone_number, 5);
    } else if ('+65' == $country_code) {
        // Singapore
        return '+65 ' . substr($phone_number, 0, 4) . ' ' . substr($phone_number, 4);
    }
    return $country_code . $phone_number;
}

/**
 * Status of the journey
 * @param string $status
 * @param string $start
 * @param string $end
 * @param string $today (optional)
 * @return string
 */
function translate_journey_status(string $status, string $start, string $end, string $today = ''): string
{
    if ('canceled' == $status) {
        return '<span class="badge bg-danger">Canceled</span>';
    }
    if (empty($today)) {
        $today = date('Y-m-d');
    }
    if ($today < $start) {
        return '<span class="badge bg-info">Upcoming</span>';
    } elseif (!empty($end) && $today > $end) {
        return '<span class="badge bg-success">Completed</span>';
    }
    return '<span class="badge bg-warning">Ongoing</span>';
}

/**
 * @param float $number
 * @return string
 */
function number_format_india(float $number): string
{
// Check negative
    $negative = FALSE;
    if (0 > $number)
    {
        $negative = TRUE;
    }
    $number = abs($number);
    // Check decimal point in $number
    $number_in_cents = $number * 100;
    $cents           = $number_in_cents % 100;
    // Make $number an integer then convert it to string
    $number     = round($number);
    $number     = (string) $number;
    // Get the last group of 3
    $last_group = substr($number, -3);
    // For the rest of the number, split them into the group of 2. If the length is odd, the first group will have 1 digit
    $rest       = substr($number, 0, -3);
    // Check $rest's length
    $rest_len   = strlen($rest);
    $str_amt    = $last_group . '.' . $cents;
    if (1 == $rest_len)
    {
        $str_amt = $rest . ',' . $str_amt;
    } else if (1 < $rest_len)
    {
        if (0 == $rest_len % 2)
        {
            $rest_len_split = str_split($rest, 2);
            $str_amt        = implode(',', $rest_len_split) . ',' . $str_amt;
        } else
        {
            $first_group    = substr($rest, 0, 1);
            $middle_group   = substr($rest, 1);
            $middle_group   = str_split($middle_group, 2);
            $str_amt        = $first_group . ',' . implode(',', $middle_group) . ',' . $str_amt;
        }
    }
    if ($negative)
    {
        return '-' . $str_amt;
    }
    return $str_amt;
}

/**
 * @param string $currency_code
 * @param float $amount
 * @return string
 */
function currency_format(string $currency_code, float|null $amount = 0.0): string
{
    $currency_format = [
        // Americas
        'CAD' => 'C$ ###',
        'MXN' => 'Mex$ ###',
        'USD' => '$ ###',
        // East Asia
        'JPY' => '### 円',
        'KRW' => '### 원',
        'TWD' => '### 元',
        // SEA
        'BND' => 'B$ ###',
        'IDR' => 'Rp ###',
        'KHR' => '៛ ###',
        'LAK' => '₭ ###',
        'MMK' => 'K ###',
        'MYR' => '<small>RM</small> ###',
        'PHP' => '₱ ###',
        'SGD' => 'S$ ###',
        'THB' => '### บ.',
        'VND' => '### ₫',
        // Europe
        'CHF' => 'fr. ###',
        'EUR' => '€ ###',
        'GBP' => '£ ###',
        // Oceania
        'AUD' => 'A$ ###',
        'FJD' => 'FJ$ ###',
        'NZD' => 'NZ$ ###',
        'WST' => 'ST ###',
        // South Asia
        'BDT' => '৳ ###',
        'BTN' => 'Nu. ###',
        'INR' => '₹ ###',
        'LKR' => 'රු. ###',
        'MVR' => 'Rf ###',
        'NPR' => 'रू ###',
        'PKR' => 'Rs ###',
    ];
    // Check negative
    $negative = FALSE;
    if (0 > $amount)
    {
        $negative = TRUE;
    }
    $amount = abs($amount);
    // Check for support
    if ( ! isset($currency_format[$currency_code]))
    {
        // Not supported: $currency_code . ' ' . number_format($amount, 2)
        if ($negative)
        {
            return '<span class="text-danger">-' . $currency_code . ' ' . number_format($amount, 2) . '</span>';
        }
        return $currency_code . ' ' . number_format($amount, 2);
    }
    // Use another function for India
    if ('INR' == $currency_code)
    {
        $str_amount = number_format_india($amount);
        if ($negative)
        {
            return '<span class="text-danger">-' . str_replace('###', $str_amount, $currency_format[$currency_code]) . '</span>';
        }
        return str_replace('###', $str_amount, $currency_format[$currency_code]);
    }
    // Separators
    $currency_with_swap_dots = ['IDR', 'VND', 'EUR', 'CHF'];
    $thousand_separator = ',';
    $decimal_separator  = '.';
    if (in_array($currency_code, $currency_with_swap_dots))
    {
        $thousand_separator = '.';
        $decimal_separator  = ',';
    }
    // Decimals
    $decimals = 2;
    if (in_array($currency_code, ['IDR', 'JPY', 'KRW', 'VND']))
    {
        $decimals = 0;
    }
    $formatted_amount = '-';
    if (0 != $amount)
    {
        $formatted_amount = number_format($amount, $decimals, $decimal_separator, $thousand_separator);
    }
    if ($negative)
    {
        return '<span class="text-danger">-' . str_replace('###', $formatted_amount, $currency_format[$currency_code]) . '</span>';
    }
    return str_replace('###', $formatted_amount, $currency_format[$currency_code]);
}

/**
 * @param int $minutes
 * @return string
 */
function minute_format(int $minutes): string
{
    // split into days, hours, minutes
    $d = floor($minutes / 1440);
    $h = floor(($minutes - $d * 1440) / 60);
    $m = $minutes % 60;
    return ($d > 0 ? $d . 'd ' : '') . ($h > 0 ? $h . 'h ' : '') . ($m > 0 ? $m . 'm' : '');
}

/**
 * Get role icons
 * @param string $role
 * @param bool $show_role_name
 * @return string|array
 */
function get_role_icons(string $role = '', bool $show_role_name = FALSE): string|array
{
    $roles = [
        'super-admin'  => ['<i class="fa-solid fa-screwdriver-wrench fa-2x"></i>', 'Super Admin'],
        'master-admin' => ['<i class="fa-brands fa-black-tie fa-2x"></i>', 'Master Admin'],
        'finance'      => ['<i class="fa-solid fa-hand-holding-dollar fa-2x"></i>', 'Finance'],
        'health'       => ['<i class="fa-solid fa-heart-pulse fa-2x"></i>', 'Health'],
        'journey'      => ['<i class="fa-solid fa-person-walking-luggage fa-2x"></i>', 'Journey'],
        'migrate'      => ['<i class="fa-solid fa-plane fa-2x fa-rotate-by" style="--fa-rotate-angle: -45deg;"></i>', 'Migrate'],
        'profile'      => ['<i class="fa-regular fa-id-badge fa-2x"></i>', 'Profile'],
    ];
    if (empty($role)) {
        return $roles;
    }
    if ($show_role_name) {
        return ($roles[$role] ? $roles[$role][0] . '<br>' . $roles[$role][1] : $role);
    }
    return $roles[$role][0] ?? $role;
}

/**
 * Generate bar chart script - AmCharts5
 * @param array $chart_data
 * @param string $div_id
 * @param string $category_field
 * @param array $series_array ["field in JSON": "label"]
 * @param string $height e.g. "500px"
 * @param string $new_tooltip
 * @param string $new_bar_text
 * @param string $new_above_bar_text
 * @param array $fill
 * @return string
 */
function generate_bar_chart_script(array $chart_data, string $div_id, string $category_field, array $series_array, string $height = '', string $new_tooltip = '', string $new_bar_text = '', string $new_above_bar_text = '', array $fill = []): string
{
    $series = '';
    foreach ($series_array as $key => $value) {
        if (isset($fill[$key])) {
            $series .= 'createSeries("' . $key . '", "' . $value . '", ' . $fill[$key] . ');';
        } else {
            $series .= 'createSeries("' . $key . '", "' . $value . '");';
        }
    }
    $tooltip = '[bold]{categoryY}[/]\n{valueX}';
    if (!empty($new_tooltip)) {
        $tooltip = $new_tooltip;
    }
    $bar_text = '{valueX}';
    if (!empty($new_bar_text)) {
        $bar_text = $new_bar_text;
    }
    $above_bar_text = '';
    if (!empty($new_above_bar_text)) {
        $above_bar_text = $new_above_bar_text;
    }
    if (empty($height)) {
        $height = '500px';
    }
    return 'document.addEventListener("DOMContentLoaded", function () {
    am5.forceUseCanvas = true;
    am5.ready(function() {
    let root = am5.Root.new("' . $div_id . '");
    root.dom.style.width = "100%";
    root.dom.style.height = "'.$height.'";
    root.setThemes([am5themes_Animated.new(root)]);
    let chart = root.container.children.push(am5xy.XYChart.new(root, {panX: false,panY: false,wheelX: "panX",wheelY: "zoomX",paddingLeft:5, layout: root.verticalLayout}));
    let legend = chart.children.push(am5.Legend.new(root, {centerX: am5.p50, x: am5.p50}));
    let data = ' . json_encode($chart_data) . ';
    let yAxis = chart.yAxes.push(am5xy.CategoryAxis.new(root, {categoryField: "'.$category_field.'",renderer: am5xy.AxisRendererY.new(root, {inversed: true,cellStartLocation: 0.1,cellEndLocation: 0.9,minorGridEnabled: true})}));
    yAxis.data.setAll(data);
    let xAxis = chart.xAxes.push(am5xy.ValueAxis.new(root, {renderer: am5xy.AxisRendererX.new(root, {strokeOpacity: 0.1, minGridDistance: 50}), min: 0}));
    function createSeries(field, name, fill = "") {
        let series = chart.series.push(am5xy.ColumnSeries.new(root, {name: name,xAxis: xAxis,yAxis: yAxis,valueXField: field,categoryYField: "'.$category_field.'",sequencedInterpolation: true,tooltip: am5.Tooltip.new(root, {pointerOrientation: "horizontal",labelText: "'.$tooltip.'"})}));
        series.columns.template.setAll({height: am5.p100, strokeOpacity: 0, cornerRadiusTR: 5, cornerRadiusBR: 5});
        series.bullets.push(function () {return am5.Bullet.new(root, {locationX: 1,locationY: 0.5,sprite: am5.Label.new(root, {centerY: am5.p50,text: "'.$above_bar_text.'",populateText: true})});});
        series.bullets.push(function () {return am5.Bullet.new(root, {locationX: 1,locationY: 0.5,sprite: am5.Label.new(root, {centerX: am5.p100,centerY: am5.p50,text: "'.$bar_text.'",fill: am5.color(0xffffff),populateText: true})});});
        series.data.setAll(data);
        if (""!=fill) {series.set("fill", am5.color(fill));}
        series.appear();
        return series;
    }
    '.$series.'
    legend.data.setAll(chart.series.values);
    let cursor = chart.set("cursor", am5xy.XYCursor.new(root, {behavior: "zoomY"}));
    cursor.lineY.set("forceHidden", true);
    cursor.lineX.set("forceHidden", true);
    chart.appear(1000, 100);
    });
    });';
}

/**
 * Generate pie chart script - AmCharts5
 * @param array $chart_data
 * @param string $div_id
 * @param string $category_field
 * @param string $value_field
 * @return string
 */
function generate_pie_chart_script(array $chart_data, string $div_id, string $category_field, string $value_field): string
{
    return 'document.addEventListener("DOMContentLoaded", function () {
    am5.forceUseCanvas = true;
    am5.ready(function() {
    let root = am5.Root.new("'.$div_id.'");
    root.setThemes([am5themes_Animated.new(root)]);
    let chart = root.container.children.push(am5percent.PieChart.new(root, {endAngle: 270}));
    let series = chart.series.push(am5percent.PieSeries.new(root, {valueField: "'.$value_field.'",categoryField: "'.$category_field.'",endAngle: 270}));
    series.states.create("hidden", {endAngle: -90});
    series.data.setAll(' . json_encode($chart_data) . ');
    series.appear(1000, 100);
    });
    });';
}

/**
 * @param array $chart_data
 * @param string $div_id
 * @param string $series_0_category_field
 * @param string $series_0_value_field
 * @param string $series_1_category_field
 * @return string
 */
function generate_nested_pie_chart_script(array $chart_data, string $div_id, string $series_0_category_field, string $series_0_value_field, string $series_1_category_field): string
{
    return 'document.addEventListener("DOMContentLoaded", function () {
    function convertAm5Colors(r){if(Array.isArray(r))return r.map(convertAm5Colors);if("object"==typeof r&&null!==r){let o={};for(let t in r)o[t]=convertAm5Colors(r[t]);return o}if("string"!=typeof r)return r;{let e=r.match(/^am5\.color\((0x[0-9a-fA-F]+)\)$/);return e?am5.color(Number(e[1])):r}}
    const parsedData = convertAm5Colors('.json_encode($chart_data).');
    am5.forceUseCanvas = true;
    am5.ready(function() {
    let root = am5.Root.new("' . $div_id . '");
    root.setThemes([am5themes_Animated.new(root)]);
    let chart = root.container.children.push(am5percent.PieChart.new(root, {layout: root.verticalLayout,radius: am5.percent(100)}));
    let series0 = chart.series.push(am5percent.PieSeries.new(root, {
      valueField: "' . $series_0_value_field . '",categoryField: "' . $series_0_category_field . '",
      alignLabels: false,radius: am5.percent(70),innerRadius: am5.percent(50)}));
    let bgColor = root.interfaceColors.get("background");
    series0.ticks.template.setAll({ forceHidden: true });
    series0.labels.template.setAll({radius: -15,text: "{category}",textType: "radial",centerX: am5.percent(100)});
    series0.slices.template.setAll({stroke: bgColor,strokeWidth: 1,tooltipText: "{category}: {valuePercentTotal.formatNumber(\'0.00\')}% ({value})"});
    series0.slices.template.states.create("hover", { scale: 0.8 });
    let series1 = chart.series.push(am5percent.PieSeries.new(root, {
      valueField: "'.$series_0_value_field.'",categoryField: "'.$series_1_category_field.'",
      alignLabels: true,innerRadius: am5.percent(70),radius: am5.percent(90)}));
    series1.slices.template.setAll({stroke: bgColor,strokeWidth: 1,templateField: "settings"});
    series1.labels.template.setAll({text: "{category}"});
    let innerData = [];
    let outerData = [];
    am5.object.each(parsedData, function(category_value, rows) {
      let the_value = 0;
      am5.array.each(rows, function(row) {the_value += row.'.$series_0_value_field.';outerData.push(row);});
      innerData.push({'.$series_0_category_field.': category_value,'.$series_0_value_field.': the_value});});
    series0.data.setAll(innerData);
    series1.data.setAll(outerData);
    series0.appear(1000, 100);
    series1.appear(1000, 100);
    });});';
}

/**
 * Generate line chart script - AmCharts5
 * @param array $chart_data
 * @param string $div_id
 * @param string $category_field
 * @param string $value_field
 * @return string
 */
function generate_line_chart_script(array $chart_data, string $div_id, string $category_field, string $value_field): string
{
    return 'document.addEventListener("DOMContentLoaded", function () {
    am5.forceUseCanvas = true;
    am5.ready(function() {
    let root = am5.Root.new("' . $div_id . '");
    root.setThemes([am5themes_Animated.new(root)]);
    let chart = root.container.children.push(am5xy.XYChart.new(root, {panX: true,panY: true,wheelX: "panX",wheelY: "zoomX",pinchZoomX:true,paddingLeft: 0}));
    let cursor = chart.set("cursor", am5xy.XYCursor.new(root, {behavior: "none"}));
    cursor.lineY.set("visible", false);
    let xAxis = chart.xAxes.push(am5xy.DateAxis.new(root, {maxDeviation: 0.2,baseInterval: {timeUnit: "day",count: 1},renderer: am5xy.AxisRendererX.new(root, {minorGridEnabled:true}),tooltip: am5.Tooltip.new(root, {})}));
    let yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {renderer: am5xy.AxisRendererY.new(root, {pan:"zoom"})}));
    let series = chart.series.push(am5xy.LineSeries.new(root, {name: "Series",xAxis: xAxis,yAxis: yAxis,valueYField: "'.$value_field.'",valueXField: "'.$category_field.'",tooltip: am5.Tooltip.new(root, {labelText: "SGD {valueY}"})}));
    chart.set("scrollbarX", am5.Scrollbar.new(root, {orientation: "horizontal"}));
    let data = ' . json_encode($chart_data) . ';
    series.data.setAll(data);
    series.appear(1000);
    chart.appear(1000, 100);
    });
    });';
}

/**
 * Generate line chart script - AmCharts5
 *
 * @param array        $chart_data
 * @param string       $div_id
 * @param string       $category_field
 * @param array|string $value_fields   e.g. ['total', 'tax'] or ['total' => 'Total Sales', 'tax' => 'Tax Amount']
 * @return string
 */
function generate_lines_chart_script(array $chart_data, string $div_id, string $category_field, array|string $value_fields): string
{
    // Normalize string to array or keep key-value mappings
    $fields = [];
    if (is_string($value_fields)) {
        $fields[$value_fields] = ucfirst($value_fields);
    } else {
        foreach ($value_fields as $key => $val) {
            if (is_numeric($key)) {
                $fields[$val] = ucfirst($val);
            } else {
                $fields[$key] = $val;
            }
        }
    }

    // Build JavaScript series generation dynamically
    $series_js = '';
    foreach ($fields as $field_key => $field_label) {
        $series_js .= '
        let series_' . $field_key . ' = chart.series.push(am5xy.LineSeries.new(root, {
            name: ' . json_encode($field_label) . ',
            xAxis: xAxis,
            yAxis: yAxis,
            valueYField: ' . json_encode($field_key) . ',
            valueXField: ' . json_encode($category_field) . ',
            tooltip: am5.Tooltip.new(root, { labelText: "{name}: SGD {valueY}" })
        }));
        series_' . $field_key . '.data.setAll(data);
        series_' . $field_key . '.appear(1000);
        legend.data.push(series_' . $field_key . ');
        ';
    }

    return 'document.addEventListener("DOMContentLoaded", function () {
    am5.forceUseCanvas = true;
    am5.ready(function() {
    let root = am5.Root.new(' . json_encode($div_id) . ');
    root.setThemes([am5themes_Animated.new(root)]);
    
    // Automatic date format handling for DateAxis strings
    root.dateFormatter.setAll({
        dateFormat: "MMM yyyy",
        dateFields: ["valueX"]
    });

    let chart = root.container.children.push(am5xy.XYChart.new(root, {
        panX: true,
        panY: true,
        wheelX: "panX",
        wheelY: "zoomX",
        pinchZoomX: true,
        paddingLeft: 0
    }));

    let cursor = chart.set("cursor", am5xy.XYCursor.new(root, { behavior: "none" }));
    cursor.lineY.set("visible", false);

    let xAxis = chart.xAxes.push(am5xy.DateAxis.new(root, {
        maxDeviation: 0.2,
        baseInterval: { timeUnit: "month", count: 1 },
        renderer: am5xy.AxisRendererX.new(root, { minorGridEnabled: true }),
        tooltip: am5.Tooltip.new(root, {})
    }));

    let yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
        renderer: am5xy.AxisRendererY.new(root, { pan: "zoom" })
    }));

    let legend = chart.children.push(am5.Legend.new(root, {
        centerX: am5.p50,
        x: am5.p50
    }));

    let data = ' . json_encode($chart_data) . ';

    ' . $series_js . '

    chart.set("scrollbarX", am5.Scrollbar.new(root, { orientation: "horizontal" }));
    chart.appear(1000, 100);
    });
    });';
}

/**
 * @param string $text
 * @return array
 */
function smart_multilang_word_count(string $text): array
{
    $text = strip_tags($text);
    // Thai characters
    preg_match_all('/[\x{0E00}-\x{0E7F}]/u', $text, $thaiChars);
    $thaiWords = count($thaiChars[0]) / 4.5;
    // Accented Latin + basic words (café, façade, etc.)
    preg_match_all('/[\p{L}\p{M}]+(?:[-\']?[\p{L}\p{M}]+)*/u', $text, $latinWords);
    $latinWordCount = count($latinWords[0]);
    // Chinese characters
    preg_match_all('/[\x{4E00}-\x{9FFF}]/u', $text, $chineseChars);
    $chineseWords = count($chineseChars[0]) / 1.5;
    // Japanese kana
    preg_match_all('/[\x{3040}-\x{30FF}]/u', $text, $japaneseKana);
    $japaneseWords = count($japaneseKana[0]) / 2;
    // Korean Hangul (optional, if used)
    preg_match_all('/[\x{AC00}-\x{D7AF}]/u', $text, $koreanSyllables);
    $koreanWords = count($koreanSyllables[0]) / 2;
    return [
        'word_count' => round($thaiWords + $latinWordCount + $chineseWords + $japaneseWords + $koreanWords),
        'char_count' => mb_strlen($text, 'UTF-8')
    ];
}

if (!function_exists('get_daily_affirmation')) {
    /**
     * Retrieves a cached affirmation message or fetches a new one if expired.
     *
     * @param int $ttl Time-to-live in seconds. Defaults to 600 (10 minutes).
     * @return string
     */
    function get_daily_affirmation(int $ttl = 600): string
    {
        $cacheKey      = 'global_affirmation_message';
        $cachedMessage = cache($cacheKey);
        if ($cachedMessage !== null) {
            return $cachedMessage;
        }
        $model      = model(HealthAffirmationModel::class);
        $newMessage = $model->retrieveRandomMessage();
        cache()->save($cacheKey, $newMessage, $ttl);
        return $newMessage;
    }
}
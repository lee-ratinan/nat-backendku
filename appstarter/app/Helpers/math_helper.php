<?php

/**
 * @param float $lat1
 * @param float $lon1
 * @param float $lat2
 * @param float $lon2
 * @param int $earthRadius
 * @return float|int
 */
function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2, int $earthRadius = 6371): float|int
{
    // Convert degrees to radians
    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);
    // Haversine formula
    $deltaLat = $lat2 - $lat1;
    $deltaLon = $lon2 - $lon1;
    $a = sin($deltaLat / 2) ** 2 +
        cos($lat1) * cos($lat2) * sin($deltaLon / 2) ** 2;
    $c = 2 * asin(sqrt($a));
    // Distance in the specified unit (default is kilometers)
    return $earthRadius * $c;
}

/**
 * @param float|int $distance
 * @return string
 */
function calculateHaul(float|int $distance): string
{
    if (!is_int($distance)) {
        $distance = intval($distance);
    }
    if (1 > $distance) {
        return '';
    } else if (1500 > $distance) {
        return 'S'; // Short
    } else if (4000 > $distance) {
        return 'M'; // Medium
    } else if (9000 > $distance) {
        return 'L'; // Long
    }
    return 'U'; // Ultra-Long
}

/**
 * Generate 6-digit unique identifier using Crockford’s Alphabet
 * @return string
 */
function uniqueIdentifier(): string
{
    $alphabet = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';
    $rand     = intval(microtime(true) * 1000);
    $id       = '';
    for ($i = 0; $i < 6; $i++) {
        $id   = $id . $alphabet[$rand % 32];
        $rand = intval($rand / 32) * rand(5, 20);
    }
    return $id;
}

/**
 * @param float $km
 * @return float
 */
function kmToMiles(float $km): float
{
    return $km / 1.609344;
}

/**
 * Transcribe Shavian
 * @param string $input
 * @param string $required_file
 * @return string
 */
function transcribeShavian(string $input, string $required_file): string
{
    if (!in_array($required_file, ['dictionary/sh_to_en.php', 'dictionary/en_to_sh.php'])) {
        $required_file = 'dictionary/en_to_sh.php';
    }
    require_once $required_file;
    $alphabet_only = '/[^a-z\'\-]/';
    if ($required_file == 'dictionary/sh_to_en.php') {
        $alphabet_only = '/[^𐑐𐑑𐑒𐑓𐑔𐑕𐑖𐑗𐑘𐑙𐑚𐑛𐑜𐑝𐑞𐑟𐑠𐑡𐑢𐑣𐑤𐑮𐑥𐑯𐑦𐑰𐑧𐑱𐑨𐑲𐑩𐑳𐑪𐑴𐑫𐑵𐑬𐑶𐑭𐑷𐑸𐑹𐑺𐑻𐑼𐑽𐑾𐑿\'\-]/';
    }
    $lowercase_input   = strtolower($input);
    $words             = explode(' ', $lowercase_input);
    $transcribed_array = [];
    foreach ($words as $word) {
        $transcribed_word = '';
        $clean_word       = str_replace(['’', '‘'], ['\'', '\''], $word);
        $stripped_word    = preg_replace($alphabet_only, '', $clean_word);
        if (empty($stripped_word)) {
            // Empty, only contains special characters, so return it as it is
            $transcribed_word = $word;
        } else if (isset($dataArray[$stripped_word])) {
            if (count($dataArray[$stripped_word]) > 1) {
                // More than one equivalent, return all of them:
                $transcribed_word = '<span class="text-danger">[' . implode(', ', $dataArray[$stripped_word]) . ']</span>';
            } else {
                // Only one equivalent, so return it
                $transcribed_word = $dataArray[$stripped_word][0];
            }
            $transcribed_word = str_replace($stripped_word, $transcribed_word, $word);
        } else {
            // Not found in the dictionary, so return it as it is, plus the # sign
            $transcribed_word = $word . '#';
        }
        $transcribed_array[] = $transcribed_word;
    }
    return implode(' ', $transcribed_array);
}
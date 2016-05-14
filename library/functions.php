<?php
/**
 * File contains special function
 *
 * @author Bohdan Zhuravel
 * @author Fedor Petryk
 */

function imageFromText($text, $width, $height)
{
    $im = imagecreate($width, $height);

// White background and blue text
    $bg = imagecolorallocate($im, 149, 149, 149);

    imagesavealpha($im, true);
    imagealphablending($im, false);

    $white = imagecolorallocatealpha($im, 255, 0, 255, 127);
    imagefill($im, 0, 0, $white);

// Write the string at the top left
    imagestring($im, 5, 0, 0, $text, $bg);
    ob_start(); // buffers future output
    imagepng($im); // writes to output/buffer
    $b64 = base64_encode(ob_get_contents()); // returns output
    ob_end_clean(); // clears buffered output
    imagedestroy($im);

    return $b64;
}

/**
 * Insert into html website settings
 *
 * @param $data
 * @param $key
 *
 * @return mixed
 */
function insert($data, $key)
{
    if (!is_array($data)) {
        return $key;
    }
    echo array_key_exists($key, $data) ? $data[$key] : $key;
}

function arr_val($data, $key)
{
    if (!is_array($data)) {
        return $key;
    }

    return array_key_exists($key, $data) ? $data[$key] : $key;
}

/**
 * Get translated string by code
 *
 * @param  string $code
 *
 * @return string
 */
function __($code)
{
    if (Zend_Registry::isRegistered('translation')) {
        return Zend_Registry::get('translation')->get($code);
    } else {
        return $code;
    }
}

/**
 * Return array converted to object
 * Using __FUNCTION__ (Magic constant) for recursive call
 *
 * Example:
 *   $a = array('one' => array('two' => 'three'));
 *   $b = array_to_object($a);
 *   $a['one']['two'] == $b->one->two == 'three';
 *
 * @param  array $array
 *
 * @return stdClass
 */
function array_to_object($array)
{
    return is_array($array) ? (object)array_map(__FUNCTION__, $array) : $array;
}

if (!function_exists('mb_ucfirst')) {

    /**
     * @param  string $text
     * @param  string $encoding
     *
     * @return string
     */
    function mb_ucfirst($text, $encoding = 'UTF-8')
    {
        return
            mb_strtoupper(mb_substr($text, 0, 1, $encoding), $encoding) .
            mb_substr($text, 1, mb_strlen($text, $encoding), $encoding);
    }
}

if (!function_exists('mb_lcfirst')) {

    /**
     * @param  string $text
     * @param  string $encoding
     *
     * @return string
     */
    function mb_lcfirst($text, $encoding = 'UTF-8')
    {
        return
            mb_strtolower(mb_substr($text, 0, 1, $encoding), $encoding) .
            mb_substr($text, 1, mb_strlen($text, $encoding), $encoding);
    }
}

function real_date_diff($date1, $date2 = null)
{
    $diff = [];

    //Если вторая дата не задана принимаем ее как текущую
    if (!$date2) {
        $cd    = getdate();
        $date2 = $cd['year'] . '-' . $cd['mon'] . '-' . $cd['mday'] . ' ' . $cd['hours'] . ':' . $cd['minutes'] . ':'
            . $cd['seconds'];
    }

    //Преобразуем даты в массив
    $pattern = '/(\d+)-(\d+)-(\d+)(\s+(\d+):(\d+):(\d+))?/';
    preg_match($pattern, $date1, $matches);
    $d1 = [
        (int)$matches[1],
        (int)$matches[2],
        (int)$matches[3],
        (int)$matches[5],
        (int)$matches[6],
        (int)$matches[7]
    ];
    preg_match($pattern, $date2, $matches);
    $d2 = [
        (int)$matches[1],
        (int)$matches[2],
        (int)$matches[3],
        (int)$matches[5],
        (int)$matches[6],
        (int)$matches[7]
    ];

    //Если вторая дата меньше чем первая, меняем их местами
    for ($i = 0; $i < count($d2); $i++) {
        if ($d2[$i] > $d1[$i]) {
            break;
        }
        if ($d2[$i] < $d1[$i]) {
            $t  = $d1;
            $d1 = $d2;
            $d2 = $t;
            break;
        }
    }

    //Вычисляем разность между датами (как в столбик)
    $md1   = [31, $d1[0] % 4 ? 28 : 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    $md2   = [31, $d2[0] % 4 ? 28 : 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    $min_v = [null, 1, 1, 0, 0, 0];
    $max_v = [null, 12, $d2[1] == 1 ? $md2[11] : $md2[$d2[1] - 2], 23, 59, 59];
    for ($i = 5; $i >= 0; $i--) {
        if ($d2[$i] < $min_v[$i]) {
            $d2[$i - 1]--;
            $d2[$i] = $max_v[$i];
        }
        $diff[$i] = $d2[$i] - $d1[$i];
        if ($diff[$i] < 0) {
            $d2[$i - 1]--;
            $i == 2 ? $diff[$i] += $md1[$d1[1] - 1] : $diff[$i] += $max_v[$i] - $min_v[$i] + 1;
        }
    }

    //Возвращаем результат
    return $diff;
}

function pcgbasename($param, $suffix = null)
{
    if ($suffix) {
        $tmpstr = ltrim(substr($param, strrpos($param, DIRECTORY_SEPARATOR)), DIRECTORY_SEPARATOR);
        if ((strpos($param, $suffix) + strlen($suffix)) == strlen($param)) {
            return str_ireplace($suffix, '', $tmpstr);
        } else {
            return ltrim(substr($param, strrpos($param, DIRECTORY_SEPARATOR)), DIRECTORY_SEPARATOR);
        }
    } else {
        return ltrim(substr($param, strrpos($param, DIRECTORY_SEPARATOR)), DIRECTORY_SEPARATOR);
    }
}

function multi_implode($glue = ',', $multiDimensionalArray)
{
    $resString = '';

    if (!isset($multiDimensionalArray) || !is_array($multiDimensionalArray)
        || count($multiDimensionalArray) < 1
    ) {
        return false;
    }

    foreach ($multiDimensionalArray as $key => $mda) {
        if (is_array($mda)) {
            $resString = $key . '-' . multi_implode($glue, $mda) . ';';
        } else {
            $resString = $key . '-' . $mda;
        }
    }

    return $resString;
}
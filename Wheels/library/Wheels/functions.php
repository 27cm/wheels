<?php

if (function_exists('mb_substr_replace') === false) {
    /**
     * Заменяет часть строки.
     *
     * @param string $string        Входная строка.
     * @param string $replacement   Строка замены.
     * @param string $start         Если start положителен, замена начинается с символа с порядковым номером start строки string.
     *                              Если start отрицателен, замена начинается с символа с порядковым номером start, считая от конца строки string.
     * @param type   $length        Если аргумент положителен, то он представляет собой длину заменяемой подстроки в строке string.
     *                              Если этот аргумент отрицательный, он определяет количество символов от конца строки string, на которых заканчивается замена.
     * @param type   $encoding      Кодировка.
     *
     * @return string
     */
    function mb_substr_replace($string, $replacement, $start, $length = null, $encoding = null)
    {
        if (extension_loaded('mbstring') === false) {
            return (is_null($length) === true)
                ? substr_replace($string, $replacement, $start)
                : substr_replace(
                    $string, $replacement, $start, $length
                );
        }

        $string_length = (is_null($encoding) === true) ? mb_strlen($string) : mb_strlen($string, $encoding);

        if ($start < 0) {
            $start = max(0, $string_length + $start);
        } else {
            if ($start > $string_length) {
                $start = $string_length;
            }
        }

        if ($length < 0) {
            $length = max(0, $string_length - $start + $length);
        } else {
            if ((is_null($length) === true) || ($length > $string_length)) {
                $length = $string_length;
            }
        }

        if (($start + $length) > $string_length) {
            $length = $string_length - $start;
        }

        if (is_null($encoding) === true) {
            return mb_substr($string, 0, $start) . $replacement . mb_substr(
                $string, $start + $length, $string_length - $start - $length
            );
        }

        return mb_substr($string, 0, $start, $encoding) . $replacement . mb_substr(
            $string, $start + $length, $string_length - $start - $length, $encoding
        );
    }
}

/**
 * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
 * keys to arrays rather than overwriting the value in the first array with the duplicate
 * value in the second array, as array_merge does. I.e., with array_merge_recursive,
 * this happens (documented behavior):
 *
 * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
 *     => array('key' => array('org value', 'new value'));
 *
 * array_merge_recursive_distinct does not change the datatypes of the values in the arrays.
 * Matching keys' values in the second array overwrite those in the first array, as is the
 * case with array_merge, i.e.:
 *
 * array_merge_recursive_distinct(array('key' => 'org value'), array('key' => 'new value'));
 *     => array('key' => array('new value'));
 *
 * Parameters are passed by reference, though only for performance reasons. They're not
 * altered by this function.
 *
 * @param array $array1
 * @param array $array2
 *
 * @return array
 * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
 * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
 */
function array_merge_recursive_distinct(array $array1, array $array2)
{
    $merged = $array1;

    foreach ($array2 as $key => &$value) {
        if (is_array($value) && isset ($merged [$key]) && is_array($merged [$key])) {
            $merged [$key] = array_merge_recursive_distinct($merged [$key], $value);
        } else {
            if (is_string($key)) {
                $merged [$key] = $value;
            } else {
                $merged [] = $value;
            }
        }
    }

    return $merged;
}
<?php

 /*
 * phpMumbleAdmin (PMA), web php administration tool for murmur (mumble server daemon).
 * Copyright (C) 2010 - 2015  Dadon David. PMA@ipnoz.net
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if (! defined('PMA_STARTED')) { die('ILLEGAL: You cannot call this script directly !'); }

/**
* @return boolean
*/
function checkPort($port)
{
    if (! is_int($port)) {
        if (is_string($port) && ctype_digit($port)) {
            (int)$port;
        } else {
            return false;
        }
    }
    if ($port >= 0 && $port <= 65535) {
        return true;
    }
    return false;
}

// function check_email($str)
// {
//     $regex_email = '/^[a-z0-9A-Z._-]+@[a-z0-9A-Z.-]{2,}[.][a-zA-Z]{2,4}$/';
//     if (preg_match($regex_email, $str) === 1) {
//         return true;
//     }
//     return false;
// }

/**
* Replace end of line by a custom string.
* By default, a space
*
* @return string
*/
function replaceEOL($str, $replace = ' ')
{
    return str_replace(array("\n\r", "\r\n", "\n", "\r", "\0" ), $replace, $str);
}

/**
 * Transforme a size into human readable.
 *
 * @return string
 */
function convertSize($size, $unit = 'bit')
{
    $base = 1024;
    switch ($unit) {
        case 'bit':
            $symbole = 'b';
            break;
        case 'byte':
            $symbole = 'B';
            break;
        case 'octet':
            $symbole = 'o';
            break;
    }
    $coef = array('', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y');
    if ($size > 0) {
        return @round($size / pow($base, ($i = floor(log($size, $base)))), 2).' '.$coef[$i].$symbole;
    } else {
        return '0';
    }
}

/**
 * Convert an array of decimal to it's character value.
 *
 * @return string
 */
function decimalArrayToChars($array)
{
    $str = '';
    foreach ($array as $dec) {
        $str .= chr($dec);
    }
    return $str;
}

/**
* Retrun php memory_limit option in integer octet.
*
 * @return integer - memory limit.
 */
function getIntegerMemoryLimit()
{
    $str = ini_get('memory_limit');

    if (ctype_digit($str)) {
        $int = (int)$str;
    } elseif ($str === '-1') {
        // -1 mean no limit, return 4T
        $int = 4*1024*1000*1000*1000;
    } else {
        $int = (int)substr($str, 0, -1);
        $abbr = strToUpper(substr($str, -1));
        switch($abbr) {
            case 'K':
                $int = 1024*$int;
                break;
            case 'M':
                $int = 1024*1000*$int;
                break;
            case 'G':
                $int = 1024*1000*1000*$int;
                break;
        }
    }
    return $int;
}

/**
 * Generate random characters
 *
 * @param int $len
 * @param bool $alphaNumOnly
 *
 * @return string
 */
function genRandomChars($len, $alphaNumOnly = true)
{
    // Initialize the random generator with a seed
    srand((double) microtime() * 1000000);
    $len = (int)$len;
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    if ($alphaNumOnly === false) {
        $chars .= '!&@][=}{+$*%?';
    }
    $str = '';
    for ($i = 0; $i < $len; ++$i) {
        $str .= $chars[ rand() % strlen($chars )];
    }
    return $str;
}

/**
* Setup timezone helper
*
* date_default_timezone_set() comes with PHP 5.1 ( like date_default_timezone_get() )
*
* @param string $tz - valid php timezone
*/
function setTimezone($tz)
{
    if (function_exists('date_default_timezone_set')) {
        @date_default_timezone_set($tz);
    }
}

/**
* htmlentities helper
*
* @param $str - string to convert
* @return string
*/
function htEnc($str)
{
    return htmlEntities($str, ENT_QUOTES, 'UTF-8');
}

/**
* htmlentities with space HTML replacement.
*
* @param $str - string to convert
* @return string
*/
function htEncSpace($str)
{
    $str = htEnc($str);
    return str_replace(' ', '&nbsp;', $str);
}

/**
* Cut too long string.
*
* @return string
*/
function cutLongString($str, $maxlen, $cutStr = '...')
{
    $strlen = strlen($str);
    if ($strlen > $maxlen) {
        $sub = $maxlen - $strlen;
        $str = substr($str, 0, $sub).$cutStr;
    }
    return $str;
}

/**
* array_diff() compare with the (string) type.
* We require our -strict- function.
*
* @return array - all difference betwin the two array, and preserve value type.
*/
function arrayDiffStrict(array $original, array $compare)
{
    $diff = array();
    foreach ($original as $key => $value) {
        if (! isset($compare[$key])) {
            $diff[$key] = $value;
            continue;
        }
        if ($compare[$key] !== $value) {
            $diff[$key] = $value;
        }
    }
    return $diff;
}

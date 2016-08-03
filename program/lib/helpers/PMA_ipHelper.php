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

class PMA_ipHelper
{
    /**
    * Check for a valid IPv4.
    */
    public static function isIPv4($str)
    {
        $regexIPv4 = '/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/';
        return (preg_match($regexIPv4, $str) === 1);
    }

    /**
    * Check for a valid IPv6.
    */
    public static function isIPv6($str)
    {
        $regexIPv6 = '/^((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))$/';
        return ($str === '::' OR preg_match($regexIPv6, $str) === 1);
    }

    /**
    * Add multiple 0 at the begining of a string.
    *
    * @param $limit - max lenght of the string.
    */
    public static function zeroPad($str, $limit)
    {
        $len = strlen($str);
        if ($len < $limit) {
            $str = str_repeat(0, $limit - $len).$str;
        }
        return $str;
    }

    /**
    * Transform ipv4 & ipv6 decimal array to string
    */
    public static function decimalTostring(array $array)
    {
        if (count($array) !== 16) {
            return 'Invalid IP';
        }
        // ipv4
        if (
            $array[0] === 0 && $array[1] === 0 && $array[2] === 0
            && $array[3] === 0 && $array[4] === 0 && $array[5] === 0
            && $array[6] === 0 && $array[7] === 0 && $array[8] === 0
            && $array[9] === 0 && $array[10] == 255 && $array[11] === 255
        ) {
            $retval['type'] = 'ipv4';
            $retval['ip'] = $array[12].'.'.$array[13].'.'.$array[14].'.'.$array[15];
        // ipv6
        } else {
            $retval['type'] = 'ipv6';
            $i = 0;
            $hex = '';
            $ipv6 = array();
            foreach ($array as $dec) {
                // Add missing zeros.
                $hex .= self::zeroPad( dechex($dec ), 2);
                ++$i;
                if ($i === 2) {
                    $ipv6[] = $hex;
                    // Reset
                    $i = 0;
                    $hex = '';
                }
            }
            $str = join(':', $ipv6);
            $retval['ip'] = self::compressIPv6String($str);
        }
        return $retval;
    }

    /**
    * Transform IPv4 mask to IPv6.
    * IPv4 range is 1-32, so in a IPv6 format, the mask can be only 96-128.
    */
    public static function mask4To6($bits)
    {
        return 128 - (32 - $bits);
    }

    /**
    * Transform IPv6 mask to IPv4.
    */
    public static function mask6To4($bits)
    {
        return 32 - (128 - $bits);
    }

    /**
    * Transform an IPv4 string address into a decimal array.
    */
    public static function stringToDecimalIPv4($str)
    {
        $e = explode('.', $str);
        return array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 255, 255, (int)$e[0], (int)$e[1], (int)$e[2], (int)$e[3]);
    }

    /**
    * Transform an IPv6 string address into a decimal array.
    */
    public static function stringToDecimalIPv6($str)
    {
        $ip = array();

        if ($str === '::') {
            $str = '0:0:0:0:0:0:0:0';
        }
        /**
        * Uncompress ipv6 (ie: fe::1 => fe:0000:0000:0000:0000:0000:0000:1)
        */
        if (false !== strpos($str, '::')) {
            list($start, $end) = explode('::', $str);
            $c = 8 - count(explode(':', $start)) - count(explode(':', $end));
            $str = $start.':'.str_repeat('0000:', $c).$end;
        }
        /**
        * 1. Add missing zeros: ( ie: 1 => 0001,  20 => 0020, 300 => 0300).
        * 2. Split each segments into 2 other segments.
        * 3. Hexadecimal to decimal.
        */
        $array = explode(':', $str);
        foreach ($array as $segment) {
            $segment = self::zeroPad($segment, 4);
            list($a, $b) = str_split($segment, 2);
            $ip[] = hexdec($a);
            $ip[] = hexdec($b);
        }
        return $ip;
    }

    /**
    * IPv6 string address compression.
    */
    public static function compressIPv6String($str)
    {
        // Already compressed
        if (false !== strpos($str, '::')) {
            return $str;
        }
        $str = ':'.$str.':';
        // remove zeros: 0001 => 1,  0020 => 20, 0300 => 300.
        $str = preg_replace('/:00/', ':', $str);
        $str = preg_replace('/:0/', ':', $str);

        preg_match_all('/(:0)+/', $str, $matchs);

        if (count($matchs[0]) > 0) {
            foreach ($matchs[0] as $match) {
                // ":0:0:0:0" to "::"
                if (strlen($match) >= 4) {
                    $str = str_replace($match, ':', $str);
                    // One compression authorized (RFC).
                    break;
                }
            }
        }
        // Remove first ":" if it's not a compression (example "::1")
        if (substr($str, 0, 2) !== '::') {
            $str = substr($str, 1);
        }
        // Remove last ":" if it's not a compression (example "Fe80::")
        if (substr($str, -2) !== '::') {
            $str = substr($str, 0, -1);
        }
        return $str;
    }
}

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

class PMA_datesHelper
{
    /**
    * Contruct uptime from an unix timestamp
    *
    * @param $ts - unix timestamp.
    * @param $format - use a custom uptime format instead of the cookie.
    *
    * @return string
    */
    public static function uptime($ts, $format = null)
    {
        global $TEXT;
        // $format = 1 : 250 days 23:59:59
        // $format = 2 : 250 days 23:59
        // $format = 3 : 250 days
        if ($format === null) {
            $format = PMA_core::getInstance()->cookie->get('uptime');
        }
        $str = '';
        $days = (int) floor($ts / 86400);
        $ts %= 86400;
        $hours = sprintf('%02d', floor($ts / 3600));
        $ts %= 3600;
        $mins = sprintf('%02d', floor($ts / 60));
        $secs = $ts % 60;
        $secs = sprintf('%02d', $secs);
        if ($days > 0) {
            if ($days === 1) {
                $str = $days.' '.$TEXT['day'].' ';
            } else {
                $str = $days.' '.$TEXT['days'].' ';
            }
        }
        if ($format === 1) {
            $str .= $hours.'h'.$mins.'m'.$secs.'s';
        } elseif ($format === 2 OR ($format === 3 && $days === 0)) {
            $str .= $hours.'h'.$mins.'m';
        }
        return strToLower($str);
    }

    /**
    * Transform a datetime (time format for database like mysql, sqlite) to unix timestamp.
    *
    * @param $str - datetime string
    * @return integer - unix timestamp
    */
    public static function datetimeToTimestamp($str)
    {
        // Default value to return on error.
        $timestamp = 0;

        // ie: 2012-04-07T17:25:03
        $regex_date1 =  '/^[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}$/';
        // ie: 2012-04-07
        $regex_date2 =  '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/';
        // Transformation:
        if (preg_match($regex_date1, $str) === 1) {
            $str = str_replace('T', ' ', $str);
        } elseif (preg_match($regex_date2, $str) === 1) {
            $str .= ' 00:00:00';
        }
        // ie: 2012-04-07 17:25:03
        $regexDatetime =  '/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/';
        if (preg_match($regexDatetime, $str) === 1) {
            list($date, $time) = explode(' ', $str);
            list($year, $month, $day) = explode('-', $date);
            list($hour, $minute, $second) = explode(':', $time);
            $timestamp = gmmktime(
                (int)$hour, (int)$minute, (int)$second,
                (int)$month, (int)$day, (int)$year
            );
        }
        return $timestamp;
    }
}

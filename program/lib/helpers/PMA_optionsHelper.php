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

class PMA_optionsHelper
{
    /*
    * Timestamp 946684799 = 31-12-1999 at 23H59:59 - GMT / UTC
    */
    const TIMESTAMP = 946684799;

    /**
    * @return array
    */
    public static function getLanguages($select = null)
    {
        $languages = array();
        $scan = scanDir(PMA_DIR_LANGUAGES);
        foreach ($scan as $entry) {
            $path = PMA_DIR_LANGUAGES.$entry;
            if (is_file($file = $path.'/common.loc.php') && is_readable($file)) {
                $name = $entry;
                $flag = 'foobar';
                // Set localized values
                if (is_file($file = $path.'/_LOCALE_CONFIG.php')) {
                    include $file;
                    if (isset($localeConf['name']) && $localeConf['name'] !== '') {
                        $name = $localeConf['name'];
                    }
                    if (isset($localeConf['localized']) && $localeConf['localized'] !== '') {
                        $name .= ' ('.$localeConf['localized'].' )';
                    }
                    if (isset($localeConf['flag']) && $localeConf['flag'] !== '') {
                        $flag = $localeConf['flag'];
                    }
                    unset($localeConf);
                }
                $lang = array();
                $lang['name'] = $name;
                $lang['dir'] = $entry;
                $lang['flag'] = $flag;
                $lang['select'] = ($select === $entry);
                $languages[] = $lang;
            }
        }
        sortArrayBy($languages, 'name');
        return $languages;
    }

    /**
    * @return array
    */
    public static function getSkins($select = null)
    {
        $skins = array();
        $scan = scanDir(PMA_DIR_CSS.'themes/');
        foreach ($scan as $entry) {
            if (substr($entry, -4) === '.css') {
                $skin = array();
                $skin['name'] = substr($entry, 0, -4);
                $skin['file'] = $entry;
                $skin['select'] = ($select === $entry);
                $skins[] = $skin;
            }
        }
        return $skins;
    }

    /**
    * @return array
    */
    public static function getTimezones($select = null)
    {
        $array = array();
        // PHP 5.2
        if (function_exists('timezone_identifiers_list')) {
            $timezones = timezone_identifiers_list();
            $continents = array(
                'Africa','America', 'Antarctica', 'Arctic', 'Asia',
                'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific'
            );
            foreach ($timezones as $timezone) {
                // Return 2 or 3 value
                $explode = explode('/', $timezone);
                $continent = $explode[0];
                $zone = new stdClass();
                $zone->tz = $timezone;
                $zone->city = $continent.' / '.str_replace('_', ' ', end($explode));
                $zone->select = ($select === $timezone);
                if (in_array($continent, $continents, true)) {
                    $array[$continent][] = $zone;
                } else {
                    $array['Other'][] = $zone;
                }
            }
        }
        return $array;
    }

    /**
    * @return array
    */
    public static function getTimeFormats($select = null)
    {
        // Memo option %P doesn't work on windows.
        $options[] = '%I:%M:%S %p'; // 11:59:59 PM
        $options[] = '%I:%M %p'; // 11:59 PM
        $options[] = '%H:%M:%S'; // 23:59:59
        $options[] = '%H:%M'; // 23:59
        $array = array();
        foreach ($options as $value) {
            $array[] = array(
                'option' => $value,
                'desc' => gmstrftime($value, self::TIMESTAMP),
                'select' => ($value === $select)
            );
        }
        return $array;
    }

    /**
    * @return array
    */
    public static function getDateFormats($select = null)
    {
        $options[] = '%d %b %Y'; // 31 Dec 1999
        $options[] = '%d %B %Y'; // 31 December 1999
        $options[] = '%m-%d-%Y'; // 12-31-1999
        $options[] = '%d-%m-%Y'; // 31-12-1999
        $options[] = '%Y-%m-%d'; // 1999-12-31
        $options[] = '%Y-%d-%m'; // 1999-31-12
        $array = array();
        foreach ($options as $value) {
            $array[] = array(
                'option' => $value,
                'desc' => gmstrftime($value, self::TIMESTAMP),
                'select' => ($value === $select)
            );
        }
        return $array;
    }

    /**
    * Get all system locales of the host.
    *
    * @return array
    */
    public static function getSystemLocales($select = null)
    {
        if (PMA_OS === 'linux') {
            exec('locale -a', $systemLocales);
        } else {
            $systemLocales = array();
        }
        $array = array();
        foreach ($systemLocales as $value) {
            if ($value !== 'C' && $value !== 'POSIX') {
                $array[] = array(
                    'locale' => $value,
                    'desc' => $value,
                    'select' => ($value === $select)
                );
            }
        }
        return $array;
    }

    /**
    * Get all system locales profiles.
    *
    * @return array
    */
    public static function getSystemLocalesProfiles(array $profiles, $select = null)
    {
        $array = array();
        foreach ($profiles as $key => $value) {
            $opt = new stdClass();
            $opt->key = $key;
            $opt->val = $value;
            $opt->select = ($key === $select);
            $array[] = $opt;
        }
        return $array;
    }
}

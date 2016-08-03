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

class PMA_updates
{
    const CHECK_UPDATE_FILE_URL = 'http://phpmumbleadmin.sourceforge.net/CURRENT_VERSION';
    const CHECK_DEBUG_UPDATE_FILE_URL = 'http://phpmumbleadmin.sourceforge.net/CURRENT_VERSION_DEBUG';
    const DOWNLOAD_URL = 'http://sourceforge.net/projects/phpmumbleadmin/';

    /**
    * Enable debug mode flag.
    * Get CHECK_DEBUG_UPDATE_FILE_URL instead of the orignal.
    */
    private $debug = false;
    /**
    * Fetch error flag.
    */
    private $fetchError = false;
    /**
    * Fetch error string text.
    */
    private $errstr = '';

    // Last check timestamp
    private $last_chk = 1;
    // Is a new version available ?
    private $new_version = false;
    // Current version string
    private $current_version = '0';

    private function fetchError($text)
    {
        $this->fetchError = true;
        $this->errstr = $text;
    }

    public function setDebugMode()
    {
        $this->debug = true;
    }

    public function setCacheParameters($cache)
    {
        if (is_array($cache)) {
            foreach ($cache as $key => $value) {
                switch ($key) {
                    case 'last_chk';
                    case 'new_version';
                    case 'current_version';
                        $this->$key = $value;
                }
            }
        }
    }

    public function getCacheParameters()
    {
        $array['last_chk'] = $this->last_chk;
        $array['new_version'] = $this->new_version;
        $array['current_version'] = $this->current_version;
        // Keep the last error string text in the cache.
        $array['last_chk_error'] = $this->errstr;
        return $array;
    }

    public function get($key)
    {
        return $this->$key;
    }

    /**
    * Check if an autoCheck is required.
    *
    * @return boolean.
    */
    public function isAutoCheckRequired($days)
    {
        if ($days > 0) {
            return (time() > ($days * 86400 + $this->last_chk));
        }
        return false;
    }

    /**
    * Get PMA current version file infos.
    */
    private function fetchUpdateFile()
    {
        if (ini_get('allow_url_fopen') !== '1') {
            $this->fetchError('php "allow_url_fopen" parameter is off. Failed to check for update');
            return;
        }
        // Enable debug mode
        if ($this->debug) {
            $url = self::CHECK_DEBUG_UPDATE_FILE_URL;
        } else {
            $url = self::CHECK_UPDATE_FILE_URL;
        }
        $file = @file($url);
        if ($file === false) {
            $this->fetchError('Failed to get '.$url);
            return;
        }
        // Check for a valid update file
        if (substr($file[0], 0, 6) !== 'INT = ' OR substr($file[1], 0, 6) !== 'STR = ') {
            $this->fetchError($url.': file is invalid');
            return;
        }
        $array['int'] = (int) str_replace('INT = ', '', $file[0]);
        $array['str'] = str_replace('STR = ', '', $file[1]);
        return $array;
    }

    /**
    * Check for PMA updates.
    */
    public function check()
    {
        // Update the last check
        $this->last_chk = time();
        // Get current version
        $fetch = $this->fetchUpdateFile();
        if (! $this->fetchError) {
            $this->current_version = $fetch['str'];
            $this->new_version = ($fetch['int'] > PMA_VERS_INT);
        }
        return $this->new_version;
    }
}

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

class PMA_cookie
{
    /**
    * Config cookie name
    */
    const COOKIE_NAME = 'phpMumbleAdmin_conf';
    /**
    * Check cookie URL
    */
    const CHECK_URL = 'checkCookie';
    /**
    * Array of cookie properties
    */
    private $properties = array();
    /**
    * Assume by default that users dont accept cookies.
    */
    private $userAcceptCookies = false;
    /**
    * Assume by default that we don't need to update user cookie.
    */
    private $updateRequested = false;

    public function __construct()
    {
        $this->properties['profile_id'] = 0;
        $this->properties['lang'] = '';
        $this->properties['skin'] = '';
        $this->properties['timezone'] = '';
        $this->properties['time'] = '';
        $this->properties['date'] = '';
        $this->properties['installed_localeFormat'] = '';
        $this->properties['uptime'] = 0;
        $this->properties['vserver_login'] = '';
        $this->properties['logsFilters'] = 1008;
        $this->properties['replace_logs_str'] = true;
        $this->properties['highlight_logs'] = true;
        $this->properties['infoPanel'] = true;
        $this->properties['highlight_pmaLogs'] = true;
    }

    /**
    * loadCookie()
    *
    * @return boolean - return if user accept cookies
    */
    public function loadCookie()
    {
        /**
        * PMA 0.4.1:
        * User config cookie name become "phpMumbleAdmin_conf"
        * Get the old cookie variables...
        */
        $oldName = 'phpMumbleADMIN_conf';
        if (isset($_COOKIE[$oldName])) {
            $_COOKIE[self::COOKIE_NAME] = $_COOKIE[$oldName];
            $this->requestUpdate();
            // Remove the old cookie
            setcookie($oldName, '', 0, '/');
        }

        if (isset($_COOKIE[self::COOKIE_NAME])) {
            $this->userAcceptCookies = true;
            $cookie = @unserialize($_COOKIE[self::COOKIE_NAME]);

            if (is_array($cookie)) {
                // Load custom parameters
                foreach ($cookie as $key => $value) {
                    $this->set($key, $value);
                }
                // No need to update the cookie here, we have just loaded user parameters.
                $this->updateRequested = false;
            } else {
                // Invalid cookie var, keep defaults parameters, and update the cookie.
                $this->requestUpdate();
            }
        }
        return $this->userAcceptCookies;
    }

    /**
    *
    * Check for a valid value
    *
    * @return boolean
    */
    private function isValidProperty($key, $value)
    {
        $isValid = false;
        if (isset($this->properties[$key])) {
            if (getType($this->properties[$key]) === getType($value)) {
                $isValid = true;
                // this key require specific values
                if ($key === 'uptime') {
                    $isValid = ($value > 0 && $value <= 3);
                }
            }
        }
        return $isValid;
    }

    public function userAcceptCookies()
    {
        return $this->userAcceptCookies;
    }

    public function set($key, $value)
    {
        if ($this->isValidProperty($key, $value) && $this->properties[$key] !== $value) {
            $this->properties[$key] = $value;
            $this->requestUpdate();
        }
    }

    public function get($key)
    {
        if (isset($this->properties[$key])) {
            return $this->properties[$key];
        }
    }

    public function requestUpdate()
    {
        $this->updateRequested = true;
    }

    public function update()
    {
        if ($this->updateRequested) {
            if (is_array($this->properties)) {
                // 6 months duration
                $duration = time() + 180*24*3600;
                setcookie(self::COOKIE_NAME, serialize($this->properties), $duration, '/');
            }
        }
        return $this->updateRequested;
    }
}

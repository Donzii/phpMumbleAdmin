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
* Murmur connection Url helper.
*/
class PMA_MurmurUrl
{
    public $url;

    private $customLogin;
    private $defaultLogin;
    private $guestLogin = 'Guest';
    private $password;
    private $customHttpAddr;
    private $defaultHttpAddr;
    private $MurmurVersion;

    public function setCustomLogin($login)
    {
        $this->customLogin = $login;
    }

    public function setDefaultLogin($login)
    {
        $this->defaultLogin = $login;
    }

    public function setGuestLogin($login)
    {
        $this->guestLogin = $login;
    }

    public function setServerPassword($pw)
    {
        $this->password = $pw;
    }

    public function setCustomHttpAddr($addr)
    {
        $this->customHttpAddr = $addr;
    }

    public function setDefaultHttpAddr($addr)
    {
        $this->defaultHttpAddr = $addr;
    }

    public function setPort($port)
    {
        $this->port = $port;
    }

    public function setMurmurVersion($version)
    {
        $this->MurmurVersion = $version;
    }

    /**
    * Construct the server connection url, cache the result.
    *
    * @return string
    */
    public function getUrl()
    {
        if (is_null($this->url)) {
            /**
            * LOGIN
            */
            if (is_string($this->customLogin) && $this->customLogin !== '') {
                $login = $this->customLogin;
            } elseif (is_string($this->defaultLogin) && $this->defaultLogin !== '') {
                $login = $this->defaultLogin;
            } else {
                $login = $this->guestLogin;
            }
            /**
            * PASSWORD
            */
            if (is_string($this->password) && $this->password !== '') {
                $login .= ':'.$this->password;
            }
            /**
            * HOST
            */
            if (is_string($this->customHttpAddr) && $this->customHttpAddr !== '') {
                $host = $this->customHttpAddr;
            } else {
                $host = $this->defaultHttpAddr;
            }
            if (PMA_ipHelper::isIPv6($host)) {
                // HTTP IPv6 address have to be like [::1]
                $host = '['.$host.']';
            }
            /**
            * VERSION
            */
            if (is_string($this->MurmurVersion)) {
                $version = $this->MurmurVersion;
            } else {
                $version = '1.2.0';
            }
            $this->url = 'mumble://'.$login.'@'.$host.':'.$this->port.'/?version='.$version;
        }
        return $this->url;
    }

    /**
    * Construct the server connection url to connect to a particular channel.
    * example:
    * mumble://example.com/channel/Deep1Channel/Deep2Channel/etc/?version=1.2.0
    *
    * @return string
    */
    public function getChannelUrl(array $channels, $id)
    {
        $deep = '';
        while ($id > 0) {
            if (isset($channels[$id])) {
                $obj = $channels[$id];
                $id = $obj->parent;
                $deep = $obj->name.'/'.$deep;
            }
        }
        $deep = rawUrlEncode($deep);
        return str_replace('/?version=', '/'.$deep.'?version=', $this->getUrl());
    }
}

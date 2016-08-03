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
* Define users classes, sorted by importance.
*/
define('PMA_USER_SUPERADMIN', 1);
define('PMA_USER_ROOTADMIN', 2);
define('PMA_USER_HEADADMIN', 4); // For the futur ;b
/**
* OBSOLETE
* Caution, reorganize users classes from here will break admins configuration.
* define('PMA_USER_ADMIN_FULL_ACCESS', 8);
*/
define('PMA_USER_ADMIN', 16);
define('PMA_USER_SUPERUSER', 32);
define('PMA_USER_SUPERUSER_RU', 64);
define('PMA_USER_MUMBLE', 128);
define('PMA_USER_UNAUTH', 256);
define('PMA_USER_INSTALLATION', 512);
/**
* Useful combinaison of classes.
*/
define('PMA_USERS_ADMINS', PMA_USER_HEADADMIN + PMA_USER_ADMIN);
define('PMA_USERS_SUPERUSERS', PMA_USER_SUPERUSER + PMA_USER_SUPERUSER_RU);
define('PMA_USERS_LOWADMINS', PMA_USERS_ADMINS + PMA_USERS_SUPERUSERS);
define('PMA_USERS_REGISTERED', PMA_USER_SUPERUSER_RU + PMA_USER_MUMBLE);
define('PMA_USERS_MUMBLE', PMA_USERS_SUPERUSERS + PMA_USER_MUMBLE);

class PMA_user
{
    /**
    * Current profile ID of user
    */
    private $profileID;
    /**
    * User access (profiles & vservers)
    */
    private $access;
    /**
    * Common to all users classes.
    */
    private $login;
    private $class;
    /**
    * PMA Admins
    */
    private $adminID;
    /**
    * Mumble users
    */
    private $authProfileID;
    private $profile_host;
    private $profile_port;
    private $mumbleSID;
    private $mumbleUID;

    public function setup()
    {
        if (! isset($_SESSION['auth'])) {
            $this->resetAuth();
        }

        $this->login = $_SESSION['auth']['login'];
        $this->class = $_SESSION['auth']['class'];

        if (is_file('install/install.php')) {
            $this->resetAuth();
            $this->setClass(PMA_USER_INSTALLATION);
        } elseif ($this->class === PMA_USER_INSTALLATION) {
            $this->setClass(PMA_USER_UNAUTH);
        }

        switch ($this->class) {
            case PMA_USER_SUPERADMIN:
                // Acces to all profiles and vservers.
                $this->access = '*';
                break;
            case PMA_USER_ROOTADMIN:
                $this->adminID = $_SESSION['auth']['adminID'];
                // Acces to all profiles and vservers.
                $this->access = '*';
                break;
            case PMA_USER_ADMIN:
                $this->adminID = $_SESSION['auth']['adminID'];
                $this->access = array();
                break;
            case PMA_USER_SUPERUSER:
            case PMA_USER_SUPERUSER_RU:
            case PMA_USER_MUMBLE:
                $this->authProfileID = $_SESSION['auth']['profileID'];
                $this->profile_host = $_SESSION['auth']['profile_host'];
                $this->profile_port = $_SESSION['auth']['profile_port'];
                $this->mumbleSID = $_SESSION['auth']['mumbleSID'];
                $this->mumbleUID = $_SESSION['auth']['mumbleUID'];
                // Only one profile and one vserver
                $this->access = array($this->authProfileID => $this->mumbleSID);
                break;
            case PMA_USER_UNAUTH:
                // All publics profiles
                $this->access = 'publics';
                break;
        }
    }

    /**
    * Check if user have access to a profile id
    *
    * @param $id - profile id to check
    */
    public function checkProfileAccess($id, $isPublic = false)
    {
        $isValid = false;
        if (is_array($this->access)) {
            foreach ($this->access as $key => $value) {
                if ($key === $id) {
                    $isValid = true;
                    break;
                }
            }
        } else {
            if ($this->access === '*') {
                $isValid = true;
            } elseif ($this->access === 'publics' && $isPublic === true) {
                $isValid = true;
            }
        }
        return $isValid;
    }

    /**
    * Check if user have access to a server id
    *
    * @param $sid - server id to check
    * @return boolean.
    */
    public function checkServerAccess($sid)
    {
        $isValid = false;
        if ($this->access === '*') {
            $isValid = true;
        } elseif (isset($this->access[$this->profileID])) {
            $vservers = $this->access[$this->profileID];
            if ($vservers === '*') {
                $isValid = true;
            } else {
                $vservers = explode(';', $vservers);
                $isValid = (in_array((string) $sid, $vservers, true));
            }
        }
        return $isValid;
    }

    /**
    * Check if current user class is equal of the submitted $class or $bitmaskClass
    * See http://www.php.net/manual/en/language.operators.bitwise.php
    *
    * @param $class integer - class to compare with.
    * @return boolean.
    */
    public function is($class)
    {
        return (bool) ($this->class & $class);
    }

    /**
    * Check if current user class is equal or superior of the submitted $class
    *
    * @param $class integer - class to compare with.
    * @return boolean.
    */
    public function isMinimum($class)
    {
        return ($this->class <= $class);
    }

    /**
    * Is minimum AdminFullAccess helper
    *
    * @return boolean.
    */
    public function isMinimumAdminFullAccess()
    {
        return ($this->isMinimum(PMA_USER_HEADADMIN) OR $this->checkServerAccess('*'));
    }

    /**
    * Check if current user class is superior of the submitted $class
    *
    * @param $class integer - class to compare with.
    * @return boolean.
    */
    public function isSuperior($class)
    {
        return ($this->class < $class);
    }

    /**
    * Check if the user is a PMA admin.
    *
    * @return boolean.
    */
    public function isPmaAdmin()
    {
        return is_int($this->adminID);
    }

    /**
    * Check if the user is a Mumble user.
    *
    * @return boolean.
    */
    public function isMumbleUser()
    {
        return is_int($this->mumbleUID);
    }

    public function resetAuth()
    {
        $_SESSION['auth'] = array();
        $this->setClass(PMA_USER_UNAUTH);
        $this->setLogin('');
    }

    /**
    * All parameters must be accessible
    *
    * @param $id integer - profile id
    */
    public function __get($key)
    {
        return $this->$key;
    }

    public function setProfileID($id)
    {
        $this->profileID = $id;
    }

    public function setClass($class)
    {
        $this->class = $_SESSION['auth']['class'] = $class;
    }

    public function setAdminAccess($access)
    {
        $this->access = $access;
    }

    public function setLogin($login)
    {
        $this->login = $_SESSION['auth']['login'] = $login;
    }

    public function setAdminID($id)
    {
        $this->adminID = $_SESSION['auth']['adminID'] = $id;
    }

    public function setMumbleSID($id)
    {
        $this->mumbleSID = $_SESSION['auth']['mumbleSID'] = $id;
    }

    public function setMumbleUID($id)
    {
        $this->mumbleUID = $_SESSION['auth']['mumbleUID'] = $id;
    }

    public function setAuthProfileID($id)
    {
        $this->authProfileID = $_SESSION['auth']['profileID'] = $id;
    }

    public function setAuthProfileHost($host)
    {
        $this->login = $_SESSION['auth']['profile_host'] = $host;
    }

    public function setAuthProfilePort($port)
    {
        $this->login = $_SESSION['auth']['profile_port'] = $port;
    }

    public function setAuthIP($ip)
    {
        if (! isset($_SESSION['auth']['ip'])) {
            $_SESSION['auth']['ip'] = $ip;
        }
    }
}

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

class PMA_datas_admins extends PMA_datas
{
    protected $storageKey = 'config_admins';

    public function __construct()
    {
        $this->getDatasFromDB();
    }

    public function saveDatasInDB()
    {
        $this->sanity();
        parent::saveDatasInDB();
    }

    /**
    * Datas sanity.
    */
    private function sanity()
    {
        foreach ($this->datas as &$admin) {
            // PMA 0.4.4, empty 'last_conn' is no more 0, but empty string.
            if ($admin['last_conn'] === 0) {
                $admin['last_conn'] = '';
            }
            ksort($admin['access']);
        }
    }

    /**
    * Validate admin login characters
    *
    * @return boolean
    */
    public function validateLoginChars($str)
    {
        return (is_string($str) && ctype_alnum($str));
    }

    /**
    * Check if an admin login exists already
    *
    * @return boolean
    */
    public function loginExists($login)
    {
        $login = strToLower($login);
        // Check all admins
        foreach ($this->datas as $array) {
            if ($login === strToLower($array['login'])) {
                return true;
            }
        }
        return false;
    }

    public function add($login, $pw, $email, $name, $class)
    {
        $id = $this->getUniqueID();
        $this->datas[] =
            array(
                'id' => $id,
                'login' => $login,
                'pw' => PMA_passwordHelper::crypt($pw),
                'created' => time(),
                'email' => $email,
                'name' => $name,
                'class' => (int)$class,
                'last_conn' => '',
                'access' => array(),
            );
        $this->saveDatasInDB();
        return $id;
    }

    /**
    * Modify admin registration
    *
    * @param $array array - admin registration
    */
    public function modify(array $array)
    {
        if (isset($array['id'])) {
            foreach ($this->datas as &$data) {
                if ($data['id'] === $array['id']) {
                    $data = $array;
                    $this->saveDatasInDB();
                    break;
                }
            }
        }
    }

    /**
    * Authenticate an admin
    *
    * @return array - admin registration on success.
    * @return interger - 1 invalid password, 2 admin not found
    */
    public function auth($login, $pw)
    {
        $login = strToLower($login);

        foreach ($this->datas as $array) {
            if ($login === strToLower($array['login'])) {
                if (PMA_passwordHelper::check($pw, $array['pw'])) {
                    return $array;
                } else {
                    return 1;
                }
            }
        }
        return 2;
    }

    /**
    * Remove access for a profile to all admins.
    *
    * @param $profile integer - profile id
    */
    public function deleteProfileAccess($id)
    {
        $modified = false;

        foreach ($this->datas as &$admin) {
            if (isset($admin['access'][$id])) {
                unset($admin['access'][$id]);
                $modified = true;
            }
        }
        if ($modified) {
            $this->saveDatasInDB();
        }
    }

    /**
    * Remove access to a server id for a profile to all admins.
    *
    * @param $profile integer - profile id
    * @param $sid integer - server id
    */
    public function deleteServerIdsAccess($profile, $sid)
    {
        $sid = strval($sid);
        $modified = false;

        foreach ($this->datas as &$admin) {
            if (! isset($admin['access'][$profile])) {
                continue;
            }
            $servers = explode(';', $admin['access'][$profile]);

            foreach ($servers as $key => $value) {
                if ($value === $sid) {
                    unset($servers[$key]);
                    $modified = true;
                }
            }
            if (empty($servers)) {
                unset($admin['access'][$profile]);
            } else {
                $admin['access'][$profile] = join(';', $servers);
            }
        }
        if ($modified) {
            $this->saveDatasInDB();
        }
    }
}

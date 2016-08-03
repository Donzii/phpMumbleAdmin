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
* Store in DB PMA users which are online.
*
* Get the sessions vars stored in file are too complicated...
*/
class PMA_datas_whosOnline extends PMA_datas
{
    protected $storageKey = 'whosOnline';

    /**
    * Auto-logout parameter (in secondes) before remove too old activity.
    * Default: 15 minutes (900 secondes).
    */
    private $autoLogout = 900;

    public function __construct()
    {
        $this->getDatasFromDB();
    }

    public function setAutoLogout($int)
    {
        $this->autoLogout = $int;
    }

    /**
    * Delete sessid
    */
    public function delete($id)
    {
        $this->removeDatas('sessid', $id);
    }

    /**
    * Remove users with too long inactivity.
    */
    public function removeOldActivity()
    {
        if ($this->autoLogout > 0) {
            foreach ($this->datas as $online) {
                if (time() > ($online['last_activity'] + $this->autoLogout)) {
                    $this->delete($online['sessid']);
                }
            }
        }
    }

    public function updateUser($user)
    {
        /**
        * Setup $update array.
        */
        $update = array();
        $update['sessid'] = session_id();
        $update['class'] = $user->class;
        $update['classname'] = pmaGetClassName($user->class);
        $update['login'] = $user->login;
        $update['current_ip'] = $_SESSION['current_ip'];
        $update['last_activity'] = $_SESSION['last_activity'];
        $update['profile_id'] = '';
        $update['sid'] = '';
        $update['uid'] = '';
        if (! is_null($user->mumbleUID)) {
            $update['profile_id'] = $user->profileID;
            $update['sid'] = $user->mumbleSID;
            $update['uid'] = $user->mumbleUID;
        }
        if (isset($_SESSION['proxy'])) {
            $update['proxy'] = $_SESSION['proxy'];
        }
        /**
        * Check if user sessid is already stored in db.
        */
        $updated = false;
        foreach ($this->datas as &$online) {
            if ($online['sessid'] === $update['sessid']) {
                $online = $update;
                $updated = true;
                break;
            }
        }
        /**
        * If no user found, add a new entrie.
        */
        if (! $updated) {
            $this->datas[] = $update;
        }
        $this->saveDatasInDB();
    }
}

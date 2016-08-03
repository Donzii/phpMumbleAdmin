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

class PMA_table_murmurRegistrations extends PMA_table
{
    protected $datasType = 'object';

    private $prx;
    private $connectedUsers = array();
    // Search stats
    protected $searchFound = 0;

    public function setPrx($prx)
    {
        $this->prx = $prx;
    }

    public function setConnectedUsers(array $array)
    {
        $this->connectedUsers = $array;
    }

    protected function getDatasStructure()
    {
        $data = new stdClass();
        $data->status = '';
        $data->statusURL = '';
        $data->uid = '';
        $data->login = ''; // For sorting only.
        $data->loginEnc = '';
        $data->email = '';
        $data->emailEnc = '';
        $data->lastActivity = ''; // For sorting only.
        $data->lastActivityUptime = '';
        $data->lastActivityDate = '';
        $data->comment = '';
        $data->hash = '';
        $data->delete = false;
        return $data;
    }

    /**
    * Search a string in an array.
    */
    public function search($search)
    {
        foreach ($this->datas as $key => $value) {
            if (false === stripos($value, $search)) {
                unset($this->datas[$key]);
            } else {
                ++$this->searchFound;
            }
        }
    }

    private function mumbleUserIsOnline($uid)
    {
        // By default, we assume that an user is offline.
        $array['url'] = '';
        $array['status'] = 2;
        foreach ($this->connectedUsers as $user) {
            if ($user->userid === $uid) {
                $array['url'] = $user->session.'-'.$user->name;
                $array['status'] = 1;
                break;
            }
        }
        return $array;
    }

    public function contructDatas()
    {
        $time = time();
        $tmp = array();
        foreach ($this->datas as $uid => $login) {

            $registration = $this->prx->getRegistration($uid);

            $data = $this->getDatasStructure();

            $isOnline = $this->mumbleUserIsOnline($uid);
            $data->status = $isOnline['status'];
            if ($data->status === 1) {
                $data->statusURL = $isOnline['url'];
            }
            $data->uid = $uid;
            $data->login = $login;
            $data->loginEnc = $this->htEnc($login);
            if ($uid === 0 && strtolower($login) !== 'superuser') {
                $data->loginEnc .= ' <i>(SuperUser)</i>';
            }
            if (isset($registration[1]) && $registration[1] !== '') {
                $data->email = $registration[1];
                $data->emailEnc = $this->htEnc($registration[1]);
            }
            $data->comment = (isset($registration[2]) && $registration[2] !== '') ? 1:2;
            $data->hash = (isset($registration[3]) && $registration[3] !== '') ? 1:2;
            // UserLastActive come with murmur 1.2.3
            if (isset($registration[5]) && $registration[5] !== '') {
                $data->lastActivity = PMA_datesHelper::datetimeToTimestamp($registration[5]);
                $data->lastActivityUptime = $this->getUptime($time - $data->lastActivity);
                $data->lastActivityDate = $this->getDateTime($data->lastActivity);
            }
            // SuperUser can't be deleted.
            $data->delete = ($uid > 0);

            $tmp[] = $data;
        }
        $this->datas = $tmp;

        $this->sortDatas();
        $this->pagingDatas();
        $this->getMinimumLines();
    }
}

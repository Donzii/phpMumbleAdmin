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

class PMA_datas_pwRequests extends PMA_datas
{
    /**
    * Nomber of characters a new id must have.
    */
    const ID_CHARS = 50;

    protected $storageKey = 'pwRequests';

    public function __construct()
    {
        $this->getDatasFromDB();
        $this->sanity();
    }

    private function sanity()
    {
        foreach ($this->datas as $key => $request) {
            // Remove invalid request
            if (count($request) !== 10) {
                unset($this->datas[$key]);
                $this->saveDatasInDB();
                continue;
            }
            // Remove too old request
            if (time() > $request['end']) {
                $this->delete($request['id']);
                continue;
            }
            // Remove request with invalid ICE profile
            if (is_null(PMA_core::getInstance()->profiles->get($request['profile_id']))) {
                $this->delete($request['id']);
                continue;
            }
        }
    }

    /**
    * Add a request to the list
    */
    public function add(array $request)
    {
        $this->datas[] = $request;
        $this->saveDatasInDB();
    }

    /**
    * Delete identical requests found in the list.
    */
    public function deleteIdenticalRequests($profile_id, $profile_host, $profile_port, $sid, $uid)
    {
        foreach ($this->datas as $request) {
            if (
                $request['profile_id'] === $profile_id
                && $request['profile_host'] === $profile_host
                && $request['profile_port'] === $profile_port
                && $request['sid'] === $sid
                && $request['uid'] === $uid
            ) {
                $this->delete($request['id']);
            }
        }
    }

    /**
    * Return an unique ID.
    */
    public function getUniqueID()
    {
        $id = genRandomChars(self::ID_CHARS);

        if ($this->isUniqueID($id)) {
            return $id;
        }
        return $this->getUniqueID();
    }
}

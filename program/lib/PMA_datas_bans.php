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

class PMA_datas_bans extends PMA_datas
{
    protected $storageKey = 'bans';

    public function __construct()
    {
        $this->getDatasFromDB();
        $this->sanity();
    }

    private function sanity()
    {
        foreach ($this->datas as $key => $data) {
            // Remove invalid bans
            if (count($data) !== 5) {
                unset($this->datas[$key]);
                $this->saveDatasInDB();
                continue;
            }
            // Permanent ban, never remove it.
            if ($data['duration'] === 0) {
                continue;
            }
            // Remove bans which reached end of duration
            if (time() > ($data['start'] + $data['duration'])) {
                $this->delete($data['id']);
                continue;
            }
        }
    }

    /**
    * Check if current user is banned, kill script if true
    */
    public function checkIP($ip)
    {
        foreach ($this->datas as $data) {
            if ($data['ip'] === $ip) {
                return true;
            }
        }
        return false;
    }

    public function add($ip, $duration, $comment = '')
    {
        $this->datas[] = array(
            'id' => $this->getUniqueID(),
            'ip' => $ip,
            'start' => time(),
            'duration' => $duration,
            'comment' => $comment
        );
        $this->saveDatasInDB();
    }

    /**
    * Common die message
    */
    public function killPma()
    {
        die('<span style="color: red;">YOU ARE BANNED !!!</span>');
    }
}

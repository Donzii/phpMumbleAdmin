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

class PMA_datas_autoban extends PMA_datas
{
    protected $storageKey = 'autobanAttempts';

    private $userIP;
    private $attempts = 10;
    private $frame = 120;

    public function __construct($ip)
    {
        $this->userIP = $ip;
        $this->getDatasFromDB();
        $this->sanity();
    }

    private function sanity()
    {
        foreach ($this->datas as $key => $array) {
            // Remove invalid autobans
            if (count($array) !== 4) {
                unset($this->datas[$key]);
                $this->saveDatasInDB();
                continue;
            }
            // Remove too old attempts
            if (time() > $array['end']) {
                $this->deleteIP($array['ip']);
                continue;
            }
        }
    }

    /**
    * Set config
    */
    public function setConf($key, $value)
    {
        switch ($key) {
            case 'attempts':
            case 'duration':
            case 'frame':
                $this->$key = $value;
        }
    }

    /**
    * Remove datas with the key "ip"
    */
    public function deleteIP($ip)
    {
        $this->removeDatas('ip', $ip);
    }

    private function addNewAttempt()
    {
        $this->datas[] = array(
            'ip' => $this->userIP,
            'start' => time(),
            'end' => time() + $this->frame,
            'attempts' => 1
        );
        $this->saveDatasInDB();
    }

    /**
    * Autoban process
    *
    * @return boolean
    */
    public function checkIP()
    {
        $LimitAttemptReached = false;
        if ($this->attempts > 0) {
            $count = 1;
            $attemptFound = false;

            foreach ($this->datas as $key => &$array) {
                // Recent attempt found
                if ($array['ip'] === $this->userIP) {
                    // Update count.
                    $count = ++$array['attempts'];
                    $attemptFound = true;
                    break;
                }
            }

            if ($attemptFound) {
                $this->saveDatasInDB();
                // Limit attempts count reached
                if ($array['attempts'] > $this->attempts) {
                    $LimitAttemptReached = true;
                }
            } else {
                // No attempt for the current user found, add a new one.
                $this->addNewAttempt();
            }
        }
        return $LimitAttemptReached;
    }
}

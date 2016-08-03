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

class PMA_datas_vserversCache extends PMA_datas
{
    protected $storageKey = 'vservers';
    protected $compact = true;

    protected $serversList;
    protected $profileID;
    protected $profileHost;
    protected $profilePort;
    protected $autoRefreshWaitDuration;

    public function __construct()
    {
        $this->getDatasFromDB();
    }

    public function setServersList($list)
    {
        $this->serversList = $list;
    }

    public function setProfileID($id)
    {
        $this->profileID = $id;
    }

    public function setProfileHost($host)
    {
        $this->profileHost = $host;
    }

    public function setProfilePort($port)
    {
        $this->profilePort = $port;
    }

    public function setAutoRefresh($int)
    {
        $this->autoRefreshWaitDuration = $int;
    }

    public function saveDatasInDB()
    {
        ksort($this->datas);
        parent::saveDatasInDB();
    }

    /**
    * Refresh vserver list
    */
    public function refreshServersCache()
    {
        $array = array();

        if (! is_array($this->serversList)) {
            $this->debugError(__method__ .' No servers list found. Aborted.');
        } else {
            $this->debug(__method__);

            $array['cache_time'] = time();
            $array['profileID'] = $this->profileID;
            $array['profile_host'] = $this->profileHost;
            $array['profile_port'] = $this->profilePort;
            $array['vservers'] = array();

            foreach ($this->serversList as $prx) {
                $name = $this->getServerName($prx->getParameter('registername'));
                $server = array();
                $server['id'] = $prx->getSid();
                $server['name'] = $name;
                $server['access'] = ($prx->getParameter('PMA_permitConnection') === 'true');
                $array['vservers'][] = $server;
            }
        }

        if (! empty($array)) {
            $this->datas[$this->profileID] = $array;
            $this->saveDatasInDB();
        }
    }

    /**
    * Nothing special.
    *
    * @return string - server name.
    */
    protected function getServerName($name)
    {
        return $name;
    }

    public function checkForAutoRefresh()
    {
        $updateRequested = false;
        // Not cache found : refresh.
        if (! isset($this->datas[$this->profileID])) {
            $this->debug(__method__ .' No cache found.');
            $updateRequested = true;
        } else {
            $current = $this->datas[$this->profileID];

            if ($current['profile_host'] !== $this->profileHost OR $current['profile_port'] !== $this->profilePort) {
                // profile host / port don't match with the cache : refresh.
                $this->debug(__method__ .' Invalid host / port.');
                $updateRequested = true;
            } elseif ($this->autoRefreshWaitDuration > 0) {
                if (time() > ($current['cache_time'] + $this->autoRefreshWaitDuration)) {
                    // The cache is too old : refresh.
                    $this->debug(__method__ .' Auto refresh requested.');
                    $updateRequested = true;
                }
            }
        }
        return $updateRequested;
    }

    public function getServersList()
    {
        if (isset($this->datas[$this->profileID])) {
            return $this->datas[$this->profileID];
        }
    }
}

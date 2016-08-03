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

class PMA_table_overview extends PMA_table
{
    protected $datasType = 'object';

    private $bootedServers = array();

    private $showOnlineUsers = false;
    private $showUptime = false;
    private $userCanDelete = false;
    /**
    * connectionUrl helper object.
    */
    private $connectionUrl;

    private $selectedSID;

    public function setBooted(array $booted)
    {
        $this->bootedServers = $booted;
    }

    public function setSelectedSID($selectedSID)
    {
        $this->selectedSID = $selectedSID;
    }

    public function setConnectionUrl($object)
    {
        $this->connectionUrl = $object;
    }

    public function setShowOnlineUsers($status)
    {
        $this->showOnlineUsers = (bool)$status;
    }

    public function setShowUptime($status)
    {
        $this->showUptime = (bool)$status;
    }

    public function setUserCanDelete($status)
    {
        $this->userCanDelete = (bool)$status;
    }

    protected function getDatasStructure()
    {
        $data = new stdClass();
        $data->id = '';
        $data->selected = false;
        $data->serverNameEnc = ''; // Encode servername once (used 3 time).
        $data->uptime = '';
        $data->connURL = '';
        $data->onlineUsers = '';
        $data->users = 0;
        $data->max = 0;
        $data->low = 70;
        $data->high = 90;
        $data->delete = false;
        return $data;
    }

    /**
    * Add to $prx key and status values for sorting.
    */
    private function addSortValues()
    {
        foreach ($this->datas as $key => &$prx) {
            $prx->status = in_array($prx, $this->bootedServers) ? 1 : 2;
            $prx->key = $key;
        }
    }

    public function contructDatas()
    {
        $this->addSortValues();
        $this->sortDatas();
        $this->pagingDatas();

        $time = time();
        $tmp = array();
        foreach ($this->datas as $prx) {

            $isBooted = ($prx->status === 1);

            $data = $this->getDatasStructure();

            $data->id = $prx->getSid();
            $data->status = $isBooted ? 'on' : 'off';
            $data->selected = ($this->selectedSID === $data->id);
            $data->serverNameEnc = htEnc($prx->getParameter('registername'));
            $data->host = $prx->getParameter('host');
            $data->port = $prx->getParameter('port');

            if (PMA_ipHelper::isIPv6($data->host)) {
                $data->host = '['.$data->host.']';
            }
            if ($this->showUptime && $isBooted) {
                $uptime = $prx->getUptime();
                $started = $time - $uptime;
                $data->uptime = PMA_datesHelper::uptime($uptime);
                $data->date = $this->getDate($started);
                $data->time = $this->getTime($started);
                $data->dt = date('Y-m-d', $started); // DateTime for HTML5 compat.
            }
            if ($isBooted) {
                $this->connectionUrl->url = null; // reset
                $this->connectionUrl->setServerPassword($prx->getParameter('password'));
                $this->connectionUrl->setDefaultHttpAddr($prx->getParameter('host'));
                $this->connectionUrl->setPort($prx->getParameter('port'));
                $data->connURL = $this->connectionUrl->getUrl();
            }
            if ($this->showOnlineUsers && $isBooted) {
                $data->users = count($prx->getUsers());
                $data->max = $prx->getParameter('users');
                $data->low = ceil($data->max*0.7);
                $data->high = ceil($data->max*0.9);
                $data->onlineUsers = HTML::onlineUsers($data->users, $data->max);
            }
            if ($prx->getParameter('PMA_permitConnection') === 'true') {
                $data->webAccessIMG = 'images/xchat/red_16.png';
            } else {
                $data->webAccessIMG = IMG_2_DELETE_16;
            }
            $data->delete = $this->userCanDelete;

            $tmp[] = $data;
        }
        $this->datas = $tmp;
        $this->getMinimumLines();
    }
}

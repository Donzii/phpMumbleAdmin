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

class PMA_table_whosOnline extends PMA_table
{
    protected $datasType = 'array';
    // Stats
    protected $total = 0;
    protected $auth = 0;
    protected $unauth = 0;

    protected function getDatasStructure()
    {
        $data = new stdClass();
        $data->className = '';
        $data->login = '';
        $data->ip = '';
        $data->proxyed = false;
        $data->proxy = '';
        $data->pid = '';
        $data->sid = '';
        $data->uid = '';
        $data->lastActivity = '';
        return $data;
    }

    public function contructDatas()
    {
        $this->sortDatas();

        $time = time();
        $tmp = array();
        foreach ($this->datas as $array) {

            $data = $this->getDatasStructure();

            $data->className = $array['classname'];
            $data->login = $array['login'];
            $data->ip = $array['current_ip'];
            $data->proxyed = isset($array['proxy']);
            if ($data->proxyed) {
                $data->proxy = $array['proxy'];
            }
            $data->pid = $array['profile_id'];
            $data->sid = $array['sid'];
            $data->uid = $array['uid'];
            $data->lastActivity = $this->getUptime($time - $array['last_activity']);
            /**
            * Stats :
            */
            ++$this->total;
            if ($array['class'] === PMA_USER_UNAUTH) {
                ++$this->unauth;
            } else {
                ++$this->auth;
            }

            $tmp[] = $data;
        }

        $this->datas = $tmp;
        $this->getMinimumLines();
    }
}

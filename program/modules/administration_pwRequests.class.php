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

class PMA_table_pwRequets extends PMA_table
{
    protected $datasType = 'array';

    protected function getDatasStructure()
    {
        $data = new stdClass();
        $data->id = '';
        $data->ip = '';
        $data->login = '';
        $data->pid = '';
        $data->sid = '';
        $data->uid = '';
        return $data;
    }

    public function contructDatas()
    {
        $this->sortDatas();

        $time = time();
        $tmp = array();
        foreach ($this->datas as $array) {

            $data = $this->getDatasStructure();

            $data->id = $array['id'];
            $data->ip = $array['ip'];
            $data->login = $array['login'];
            $data->pid = $array['profile_id'];
            $data->sid = $array['sid'];
            $data->uid = $array['uid'];

            $data->uptime = $this->getUptime($array['end'] - $time);
            $data->date = $this->getDate($array['start']);
            $data->time = $this->getTime($array['start']);

            $tmp[] = $data;
        }

        $this->datas = $tmp;
        $this->getMinimumLines();
    }
}

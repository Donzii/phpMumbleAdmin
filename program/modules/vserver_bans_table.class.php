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

class PMA_table_murmurBans extends PMA_table
{
    protected $datasType = 'object';

    protected function getDatasStructure()
    {
        $data = new stdClass();
        $data->key = '';
        $data->selection = false;
        $data->ip = '';
        $data->mask = '';
        $data->userName = '';
        $data->reason = '';
        $data->hash = false;
        $data->startedDate = '';
        $data->startedTime = '';
        $data->durationDate = '';
        $data->durationTime = '';
        $data->delete = false;
        return $data;
    }

    public function contructDatas()
    {
        $this->pagingDatas();

        $tmp = array();
        foreach ($this->datas as $key => $ban) {

            $ip = PMA_ipHelper::decimalTostring($ban->address);
            // mask 128 bits mean ip only, no need to show the mask

            $data = $this->getDatasStructure();

            $data->key = $key;
            $data->selection = true;
            $data->ip = $ip['ip'];
            if ($ban->bits !== 128) {
                if ($ip['type'] === 'ipv4') {
                    $data->mask = PMA_ipHelper::mask6To4($ban->bits);
                } else {
                    $data->mask = $ban->bits;
                }
            }
            $data->userName = $ban->name;
            $data->reason = $ban->reason;
            $data->hash = ($ban->hash !== '');
            $data->startedDate = $this->getDate($ban->start);
            $data->startedTime = $this->getTime($ban->start);
            if ($ban->duration > 0) {
                $timestamp = $ban->start + $ban->duration;
                $data->durationDate = $this->getDate($timestamp);
                $data->durationTime = $this->getTime($timestamp);
            }
            $data->delete = true;

            $tmp[] = $data;
        }
        $this->datas = $tmp;
        $this->getMinimumLines();
    }
}

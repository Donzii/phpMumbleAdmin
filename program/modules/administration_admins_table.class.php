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

class PMA_table_admins extends PMA_table
{
    protected $datasType = 'array';

    protected function getDatasStructure()
    {
        $data = new stdClass();
        $data->id = '';
        $data->className = '';
        $data->loginEnc = '';
        $data->lastConn = '';
        $data->access = array();
        return $data;
    }

    public function contructDatas()
    {
        $this->sortDatas();

        $tmp = array();
        foreach ($this->datas as $key => $admin) {

            $data = $this->getDatasStructure();
            $data->id = $admin['id'];
            $data->className = pmaGetClassName($admin['class']);
            $data->loginEnc = $this->htEnc($admin['login']);
            if (is_int($admin['last_conn']) && $admin['last_conn'] > 0) {
                $data->lastConn = PMA_datesHelper::uptime(time() - $admin['last_conn']);
                $data->lastConnDate = strftime($this->dateTimeFormat, $admin['last_conn']);
            }
            $data->access = $admin['accessText'];
            $tmp[] = $data;
        }

        $this->datas = $tmp;
        $this->getMinimumLines();
    }
}

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

class PMA_datas_profiles extends PMA_datas
{
    protected $storageKey = 'config_profiles';

    public function __construct()
    {
        $this->getDatasFromDB();
        // empty profile list is not allowed, add default.
        if (empty($this->datas)) {
            $this->add('Default');
        }
    }

    public function add($name)
    {
        $id = $this->getUniqueID();
        // By default, the first profile is public, but the others not.
        $public = ($id === 1);
        $this->datas[] =
            array(
                'id' => $id,
                'name' => $name,
                'public' => $public,
                'host' => '127.0.0.1',
                'port' => '6502',
                'timeout' => 10,
                'secret' => '',
                'slice_profile' => '',
                'slice_php' => '',
                'http-addr' => ''
            );
        $this->saveDatasInDB();
        return $id;
    }

    public function modify(array $array)
    {
        if (isset($array['id'])) {
            foreach ($this->datas as &$data) {
                if ($data['id'] === $array['id']) {
                    $data = $array;
                    $this->saveDatasInDB();
                    break;
                }
            }
        }
    }

    /**
    * Zero profile is not allowed
    */
    public function delete($id)
    {
        if (count($this->datas) >= 2) {
            $this->removeDatas('id', $id);
        }
    }

    public function getName($id)
    {
        foreach ($this->datas as $array) {
            if ($array['id'] === $id) {
                return $array['name'];
            }
        }
    }

    public function getFirst()
    {
        return reset($this->datas);
    }

    public function total()
    {
        return count($this->datas);
    }
}

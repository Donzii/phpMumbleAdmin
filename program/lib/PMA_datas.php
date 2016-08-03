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

/**
* Abstract class for datas
*/
abstract class PMA_datas extends PMA_debugSubject
{
    /**
    * Storage key for getDatasFromDB() saveDatasInDB() methods.
    */
    protected $storageKey;
    /**
    * All datas are stored in this var.
    */
    protected $datas = array();
    /**
    * Use compact mode to store datas.
    */
    protected $compact = false;
    /**
    * Check error on the last noQueue datas update.
    */
    protected $lastSaveSuccess = false;

    protected function getDatasFromDB()
    {
        $this->datas = PMA_db::instance()->get($this->storageKey);
    }

    protected function saveDatasInDB()
    {
        PMA_db::instance()->queue($this->storageKey, array($this, 'getAllDatas'), $this->compact);
    }

    public function forceSaveDatasInDB()
    {
        $this->lastSaveSuccess = PMA_db::instance()->noQueue($this->storageKey, array($this, 'getAllDatas'), $this->compact);
    }

    protected function getDatas($key, $value)
    {
        foreach ($this->datas as $data) {
            if ($data[$key] === $value) {
                return $data;
            }
        }
    }

    /**
    * By default, get datas by $id
    */
    public function get($id)
    {
        return $this->getDatas('id', $id);
    }

    protected function removeDatas($key, $value)
    {
        foreach ($this->datas as $k => $data) {
            if ($data[$key] === $value) {
                unset($this->datas[$k]);
                $this->saveDatasInDB();
                break;
            }
        }
    }

    /**
    * Check error for the last save on the fly.
    */
    public function isLastSaveSuccess()
    {
        return $this->lastSaveSuccess;
    }

    /**
    * By default, remove datas by $id
    */
    public function delete($id)
    {
        $this->removeDatas('id', $id);
    }

    public function getAllDatas()
    {
        return $this->datas;
    }

    /**
    * Check for a unique id
    *
    * @return bool
    */
    protected function isUniqueID($id)
    {
        foreach ($this->datas as $array) {
            if ($array['id'] === $id) {
                return false;
            }
        }
        return true;
    }

    protected function getUniqueID()
    {
        if (empty($this->datas)) {
            $id = 1;
        } else {
            $Ids = array();
            foreach ($this->datas as $data) {
                $Ids[] = $data['id'];
            }
            sort($Ids);
            $id = (int)end($Ids);
            ++$id;
        }
        return $id;
    }
}

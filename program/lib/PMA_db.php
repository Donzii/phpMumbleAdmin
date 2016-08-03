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

/*
* DB Class for datas stored in files.
*/
class PMA_db
{
    /**
    * Queue.
    */
    private $queue = array();

    private function __construct() {}

    /**
    * Singleton
    */
    public static function instance()
    {
        static $instance;
        if (is_null($instance)) {
            $instance = new self();
        }
        return $instance;
    }

    private function getPath($storageKey)
    {
        if (substr($storageKey, 0, 6) === 'config') {
            $dir = PMA_DIR_CONFIG;
            $storageKey = substr($storageKey, 7);
        } else {
            $dir = PMA_DIR_CACHE;
        }
        return $dir.$storageKey.'.php';
    }

    /**
    * Get stored datas
    */
    public function get($storageKey)
    {
        $array = array();
        if (is_file($file = $this->getPath($storageKey))) {
            include $file;
        }
        return $array;
    }

    /**
    * Get queue.
    */
    public function getQueuedKey()
    {
        $tmp = array();
        foreach ($this->queue as $id => $array) {
            $tmp[] = $id;
        }
        return $tmp;
    }

    private function saveDatas($storageKey, $callback, $compact)
    {
        $path = $this->getPath($storageKey);
        $arrayToFile = new PMA_arrayToFile($path, $compact);
        return $arrayToFile->write(call_user_func($callback));
    }

    /**
    * Attach to queue keys datas needed to be updated
    */
    public function queue($storageKey, $callback, $compact)
    {
        if (! isset($this->queue[$storageKey])) {
            $this->queue[$storageKey] = array('callback' => $callback, 'compact' => $compact);
        }
    }

    public function noQueue($storageKey, $callback, $compact)
    {
        if (isset($this->queue[$storageKey])) {
            unset($this->queue[$storageKey]);
        }
        return $this->saveDatas($storageKey, $callback, $compact);
    }

    /**
    * Save all datas stored in $queue.
    */
    public function updateQueued()
    {
        foreach ($this->queue as $storageKey => $array) {
            $this->saveDatas($storageKey, $array['callback'], $array['compact']);
        }
    }
}

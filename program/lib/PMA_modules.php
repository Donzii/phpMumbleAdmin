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
* Modules controller.
*/
class PMA_modules
{
    /**
    * Modules path.
    */
    private $path;
    /**
    * Module ID.
    */
    private $id;
    /**
    * Classes table.
    */
    private $classes = array();
    /**
    * Controllers table.
    */
    private $controllers = array();

    /**
    * Set modules path.
    */
    public function setPath($path)
    {
        $this->path = $path;
    }

    public function enable($id)
    {
        $this->id = $id;

        $file = $id.'.class.php';
        if (is_file($this->path.$file)) {
            $this->classes[] = $file;
        }
        $file = $id.'.php';
        if (is_file($this->path.$file)) {
            $this->controllers[] = $file;
        }
    }

    /**
    * Add a common controller.
    */
    public function addController($id)
    {
        $this->controllers[] = $id.'.ctrler.inc';
    }

    /**
    * @return array
    */
    public function getPaths()
    {
        return array_merge(
            $this->classes,
            $this->controllers
        );
    }

    public function getView()
    {
        return $this->path.$this->id.'.view.php';
    }
}
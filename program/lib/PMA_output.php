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

class PMA_output
{
    protected $properties = array();

    protected function htEnc($value)
    {
        return htmlEntities($value, ENT_QUOTES, 'UTF-8');
    }

    public function set($key, $value)
    {
        if (is_string($value)) {
            $value = $this->htEnc($value);
        }
        $this->properties[$key] = $value;
    }

    public function is_set($key)
    {
        return isset($this->properties[$key]) ? true : false;
    }

    public function prt($key)
    {
        echo $this->properties[$key];
    }

    public function printf($text, $key)
    {
        printf($text, $this->properties[$key]);
    }

    public function chked($key)
    {
        if ($this->properties[$key] === true) {
            echo 'checked="checked"';
        }
    }

    public function selected($key)
    {
        if ($this->properties[$key] === true) {
            echo 'selected="selected"';
        }
    }

    public function disabled($key)
    {
        if ($this->properties[$key] === true) {
            echo 'disabled="disabled"';
        }
    }

    public function required($key)
    {
        if ($this->properties[$key] === true) {
            echo 'required="required"';
        }
    }
}

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

class PMA_MumbleAvatar
{
    private $src = '';
    private $fileName = '';
    private $texture = array();

    private $pid = 'NULL';
    private $sid = 'NULL';
    private $uid = 'NULL';

    public function __construct($texture)
    {
        if (is_array($texture)) {
            $this->texture = $texture;
        }
    }

    public function constructSRC()
    {
        if (! empty($this->texture)) {

            $blob = $this->getBlob();
            $hash = md5($blob);

            $uid = 'p'.$this->pid.'s'.$this->sid.'u'.$this->uid;
            $this->fileName = 'avatar_'.$uid.'_'.$hash.'.png';

            $path = PMA_DIR_AVATARS_RELATIVE.$this->fileName;

            if (file_exists($path)) {
                $this->src = $path;
            } else {
                if (is_writeable(PMA_DIR_AVATARS)) {
                    $this->writeFile($path, $blob);
                    $this->src = $path;
                } else {
                    // Display avatar anyway
                    $this->src = 'data:image/png;base64, '.base64_encode($blob);
                }
            }
        }
    }

    public function setProfile_id($id)
    {
        $this->pid = $id;
    }

    public function setServer_id($id)
    {
        $this->sid = $id;
    }

    public function setUser_id($id)
    {
        $this->uid = $id;
    }

    public function getSRC()
    {
        return $this->src;
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function isEmpty()
    {
        return ($this->src === '');
    }

    private function getBlob()
    {
        return decimalArrayToChars($this->texture);
    }

    private function writeFile($path, $blob)
    {
        $fp = fopen($path, 'wb');
        if (is_resource($fp)) {
            fwrite($fp, $blob);
            fclose($fp);
        }
    }
}

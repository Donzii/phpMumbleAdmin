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

class PMA_checkFilesAccess
{
    public $datas = array();

    private function addData($path, $mustBeWriteable)
    {
        $data = new stdClass();

        $data->path = $path;
        $data->isWriteable = is_writable($path);
        $data->isGood = ($mustBeWriteable === $data->isWriteable);
        $data->css = $data->isGood ? 'good' : 'bad';
        $data->comment = $data->isWriteable ? 'is writeable, ' : 'is not writeable, ';
        $data->comment .= $data->css;

        $this->datas[] = $data;
    }

    public function checkDir($path, $scanDir = false)
    {
        $this->addData($path, true);
        if (is_file($file =$path.'.htaccess')) {
            $this->addData($file, false);
        }
        if (is_file($file =$path.'index.html')) {
            $this->addData($file, false);
        }

        if ($scanDir) {
            $scan = scanDir($path);
            foreach ($scan as $entry) {
                $entryPath = $path.$entry;
                if ($entry !== '.htaccess' && $entry !== 'index.html' && is_file($entryPath)) {
                    $this->addData($entryPath, true);
                }
            }
        }
        // Blank line
        $this->addData('', true);
    }
}

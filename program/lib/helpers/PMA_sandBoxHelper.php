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

class PMA_sandBoxHelper
{
    public static function create($datas)
    {
        /**
        * w = Open for writing only.
        * Place the file pointer at the beginning of the file and truncate the file to zero length.
        * If the file does not exist, attempt to create it.
        */
        $fp = @fopen(PMA_FILE_SANDBOX, 'wb');

        if (is_resource($fp)) {
            /**
            * Start
            */
            fwrite($fp, '<!DOCTYPE html>'.PHP_EOL);
            fwrite($fp, '<html>'.PHP_EOL);
            fwrite($fp, '<head>'.PHP_EOL);
            fwrite($fp, '<title>SANDBOX</title>'.PHP_EOL);
            fwrite($fp, '</head>'.PHP_EOL);
            fwrite($fp, '<body>'.PHP_EOL);

            fwrite($fp, $datas.PHP_EOL);

            fwrite($fp, '</body>'.PHP_EOL);
            fwrite($fp, '</html>'.PHP_EOL);
            fclose($fp);
        }
    }
}

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

function languages_compare($file, $lang)
{
    function getPmaText($filePath)
    {
        $TEXT = array();
        if (is_file($filePath)) {
            include $filePath;
        }
        return $TEXT;
    }

    $references = getPmaText(PMA_DIR_LANGUAGES.'/en_EN/'.$file);
    $compares = getPmaText(PMA_DIR_LANGUAGES.'/'.$lang.'/'.$file);

    $datas = array();

    foreach ($references as $key => $reference) {

        // Old $TEXT format, go to next
        if (is_array($reference)) {
            continue;
        }

        $data = new stdClass();
        $data->key = $key;
        $data->ref = $reference;
        $data->comp = null;

        if (isset($compares[$key])) {
            $data->comp = $compares[$key];
        }
        $datas[] = $data;
    }

    return $datas;
}

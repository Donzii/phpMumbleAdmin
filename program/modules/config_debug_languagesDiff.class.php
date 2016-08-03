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

function languagesDiff($lang)
{
    class diffObject
    {
        public $title = false;
        public $new = false;
        public $old = false;
        public $key = '';
        public $text = '';
    }

    function getPmaText($filePath)
    {
        $TEXT = array();
        if (is_file($filePath)) {
            include $filePath;
        }
        return $TEXT;
    }

    $REF_DIR = PMA_DIR_LANGUAGES.'/en_EN/';
    $COMP_DIR = PMA_DIR_LANGUAGES.'/'.$lang.'/';

    $scan = scanDir($REF_DIR);

    $datas = array();

    foreach ($scan as $file) {

        if (substr($file, -8) !== '.loc.php') {
            continue;
        }

        $reference = getPmaText($REF_DIR.'/'.$file);
        $compare = getPmaText($COMP_DIR.'/'.$file);

        $news = array_diff_key($reference, $compare);
        $obsoletes = array_diff_key($compare, $reference);

        if (! empty($news) OR ! empty($obsoletes)) {
            $data = new diffObject();
            $data->title = true;
            $data->text = $file;
            $datas[] = $data;
        }

        foreach ($news as $key => $text) {
            $data = new diffObject();
            $data->new = true;
            $data->key = $key;
            $data->text = $text;
            $datas[] = $data;
        }

        foreach ($obsoletes as $key => $text) {
            $data = new diffObject();
            $data->old = true;
            $data->key = $key;
            $data->text = $text;
            $datas[] = $data;
        }
    }

    return $datas;
}

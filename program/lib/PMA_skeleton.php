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

class PMA_skeleton
{
    private $captions = array();
    private $cssFiles = array();
    private $infosPanel = array();
    private $jsTexts = array();

    public function addCaption($src, $text, $css = '')
    {
        $caption = new stdClass();
        $caption->src = $src;
        $caption->text = $text;
        $caption->css = $css;
        $this->captions[] = $caption;
    }

    public function getCaptions()
    {
        return $this->captions;
    }

    public function addCssFile($fileName)
    {
        $this->cssFiles[] = $fileName;
    }

    public function getCssFiles()
    {
        return $this->cssFiles;
    }

    public function addInfoPanel($datas, $css = '')
    {
        $fill = new stdClass();
        $fill->css = $css;
        $fill->datas = $datas;
        $this->infosPanel[] = $fill;
    }

    public function getInfosPanel()
    {
        return $this->infosPanel;
    }

    public function addJsText($id, $text)
    {
        $js = new stdClass();
        $js->id = $id;
        $js->text = $text;
        $this->jsTexts[] = $js;
    }

    public function getJsTexts()
    {
        return $this->jsTexts;
    }
}

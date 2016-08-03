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

abstract class PMA_table
{
    /**
    * Abstract methods
    */
    abstract protected function getDatasStructure();
    abstract public function contructDatas();
    /**
    * The minimum of lines a table must have.
    */
    const MINIMUM_LINES = 10;
    /**
    * Datas type (object or array) for sort cmp.
    */
    protected $datasType;

    protected $datas = array();
    protected $columns = array();
    protected $pagingMenu = array();
    /**
    * Date and time format.
    */
    protected $dateTimeFormat;
    protected $dateFormat;
    protected $timeFormat;
    /**
    * Sort variables.
    */
    protected $defaultSort;
    protected $sort;
    protected $reverse = false;
    /**
    * Paging variables.
    */
    protected $maxPerPage = 10; // Max lines of datas per page (default : 10 lines).
    protected $currentPage;
    protected $totalOfPages;

    public function __construct($datas)
    {
        $this->datas = $datas;
    }

    public function __get($key)
    {
        return $this->$key;
    }

    public function setDateTimeFormat($format)
    {
        $this->dateTimeFormat = $format;
    }

    public function setDateFormat($format)
    {
        $this->dateFormat = $format;
    }

    public function setTimeFormat($format)
    {
        $this->timeFormat = $format;
    }

    public function setMaxPerPage($max)
    {
        $this->maxPerPage = $max;
    }

    public function setNavigation($nav)
    {
        $this->defaultSort = $nav['defaultSort'];
        $this->sort = $nav['sort'];
        $this->reverse = isset($nav['reverse']);
        $this->currentPage = $nav['page'];
    }

    /**
    * Add columns href and text.
    */
    public function sortColumn($key, $text, $short = false)
    {
        $column = new stdClass();
        $column->text = $text;
        $column->href = '?sort='.$key;

        if ($key === $this->sort) {
            if ($this->reverse) {
                $img = '<img src="'.IMG_ARROW_UP.'" alt="" />';
            } else {
                $img = '<img src="'.IMG_ARROW_DOWN.'" alt="" />';
                $column->href .= '&amp;reverse=true';
            }
            if ($short === true) {
                $column->text = $img;
            } else {
                $column->text .= $img;
            }
        }
        $this->columns[$key] = $column;
    }

    public function getColText($key)
    {
        return $this->columns[$key]->text;
    }

    public function getColHref($key)
    {
        return $this->columns[$key]->href;
    }

    protected function htEnc($str)
    {
        return htmlEntities($str, ENT_QUOTES, 'UTF-8');
    }

    protected function getDateTime($ts)
    {
        return strftime($this->dateTimeFormat, $ts);
    }

    protected function getDate($ts)
    {
        return strftime($this->dateFormat, $ts);
    }

    protected function getTime($ts)
    {
        return strftime($this->timeFormat, $ts);
    }

    protected function getUptime($ts)
    {
        return PMA_datesHelper::uptime($ts);
    }

    protected function sortDatas()
    {
        if (isset($this->sort, $this->defaultSort)) {
            uasort($this->datas, array('self', 'defaultCmp'));
        }
        if ($this->reverse) {
            $this->reverseDatas();
        }
    }

    /**
    * Table compare with a default key.
    * Both object or array datas type.
    * Compare table keys, if values are equal, compare default keys.
    * On empty string value, sort at last by inversing the result (PHP by default sort empty string value at first).
    */
    protected function defaultCmp($a, $b)
    {
        $key = $this->sort;
        $default = $this->defaultSort;
       if ($this->datasType === 'object') {
            $aKey = $a->$key;
            $bKey = $b->$key;
            $aDefault = $a->$default;
            $bDefault = $b->$default;
        } elseif ($this->datasType === 'array') {
            $aKey = $a[$key];
            $bKey = $b[$key];
            $aDefault = $a[$default];
            $bDefault = $b[$default];
        }

        $result = strNatCaseCmp($aKey, $bKey);
        if ($result === 0) {
            $result = strNatCaseCmp($aDefault, $bDefault);
        } elseif ($aKey === '' OR $bKey === '') {
            // Inverse empty string value.
            $result = 0 - $result;
        }
        return $result;
    }

    /**
    * Reverse $datas.
    */
    protected function reverseDatas()
    {
        $this->datas = array_reverse($this->datas, true);
    }

    /**
    * Chunk $datas array to keep the current page only.
    */
    protected function pagingDatas()
    {
        $total = count($this->datas);

        if ($this->maxPerPage > 0) {
            $this->totalOfPages = (int) ceil($total / $this->maxPerPage);
        } else {
            $this->totalOfPages = 1;
        }
        // Current page can't be null or negative
        if (! is_int($this->currentPage) OR $this->currentPage < 1) {
            $this->currentPage = 1;
        }
        // Current page can't be superior than the total of page
        if ($this->currentPage > $this->totalOfPages) {
            $this->currentPage = $this->totalOfPages;
        }
        if ($this->totalOfPages > 1) {
            $chunk = array_chunk($this->datas, $this->maxPerPage, true);
            $this->datas = $chunk[$this->currentPage -1];
        }
    }

    /**
    * Get paging table.
    */
    public function contructPagingMenu()
    {
        // Do it once.
        if (! isset($this->contructPagingMenuDone)) {
            $this->contructPagingMenuDone = true;

            if ($this->totalOfPages === 0) {
                $range = array();
            // Less than 5 pages of range.
            } elseif ($this->totalOfPages <= 5) {
                $range = range(1, $this->totalOfPages);
            } else {
                // 3 first pages range
                if ($this->currentPage <= 3) {
                    $range = range(1, 5);
                // 3 last pages range
                } elseif (($this->totalOfPages - $this->currentPage) <= 2) {
                    $range = range($this->totalOfPages -4, $this->totalOfPages);
                // All others range
                } else {
                    $range = range($this->currentPage -2, $this->currentPage +2);
                }
            }
            // Add pages range menu
            foreach ($range as $page) {
                $obj = new stdClass();
                $obj->page = $page;
                $obj->css = '';
                if ($this->currentPage === $page) {
                    $obj->css = 'selected';
                }
                $this->pagingMenu[] = $obj;
            }
        }
    }

    /**
    * Add empty datas to have a minimum number of line.
    */
    protected function getMinimumLines()
    {
        $total = count($this->datas);
        if ($total < self::MINIMUM_LINES) {
            for ($i = $total; $i < self::MINIMUM_LINES; ++$i) {
                $this->datas[] = $this->getDatasStructure();
            }
        }
    }
}

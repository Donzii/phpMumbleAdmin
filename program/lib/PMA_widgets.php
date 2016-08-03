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

class PMA_widgets
{
    /**
    * Widgets paths.
    */
    private $paths = array();
    /**
    * Widgets store.
    */
    private $store = array();
    /**
    * Current widget ID.
    */
    private $currentID;
    /**
    * Hidden count.
    */
    private $hiddens = 0;

    public function setPath($type, $path)
    {
        $this->paths[$type] = $path;
    }

    public function getList()
    {
        $tmp = array();
        $low = array();
        foreach ($this->store as $widget) {
            if ($widget->lowPriority) {
                $low[] = $widget;
            } else {
                $tmp[] = $widget;
            }
        }
        return array_merge($tmp, $low);
    }

    public function getHiddens()
    {
        $tmp = array();
        foreach ($this->store as $widget) {
            if ($widget->hidden) {
                $tmp[] = $widget;
            }
        }
        return $tmp;
    }

    public function hiddensExists()
    {
        return ($this->hiddens > 0);
    }

    private function add($id, $type, $hidden)
    {
        $obj = new stdClass();
        $obj->type = $type;
        $obj->hidden = $hidden;
        $obj->id = $id;
        $obj->lowPriority = false;
        $obj->classPath = $this->paths[$type].$id.'.class.php';
        $obj->controllerPath = $this->paths[$type].$id.'.php';
        $obj->viewPath = $this->paths[$type].$id.'.view.php';
        $obj->datas = new stdClass();

        $this->store[] = $obj;
    }

    public function newWidget($id)
    {
        $this->add($id, 'widget', false);
    }

    public function newPopup($id)
    {
        $this->add($id, 'popup', false);
    }

    public function newHiddenPopup($id)
    {
        $this->add($id, 'popup', true);
        ++$this->hiddens;
    }

    public function setLowPriority($id)
    {
        foreach ($this->store as &$widget) {
            if ($id === $widget->id) {
                $widget->lowPriority = true;
                break;
            }
        }
    }

    public function setCurrentID($id)
    {
        $this->currentID = $id;
    }

    public function saveDatas($object)
    {
        foreach ($this->store as &$widget) {
            if ($this->currentID === $widget->id) {
                $widget->datas = clone $object;
                break;
            }
        }
    }

    public function getView($id)
    {
        foreach ($this->store as $widget) {
            if ($id === $widget->id) {
                return $widget->viewPath;
            }
        }
        return $this->paths['widget'].'widgetError.view.php';
    }

    public function getDatas($id)
    {
        foreach ($this->store as $widget) {
            if ($id === $widget->id) {
                return $widget->datas;
            }
        }
        trigger_error('Invalid widget '.$id, E_USER_ERROR);
    }

    public function disableWidget()
    {
        foreach ($this->store as $key => $widget) {
            if ($this->currentID === $widget->id) {
                unset($this->store[$key]);
                break;
            }
        }
    }
}

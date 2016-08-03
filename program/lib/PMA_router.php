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

class PMA_router
{
    private $deep = array();
    private $nodeep = array();

    private $historyLoaded = false;

    /**
    * Init.
    */
    public function initialize()
    {
        if (! isset($_SESSION['navigation'])) {
            $_SESSION['navigation'] = array();
        }
    }

    public function resetNavigation()
    {
        $_SESSION['navigation'] = array();
        foreach ($this->deep as $controller) {
            $this->$controller->setCurrentRoute(null);
        }
    }

    /**
    * Controllers factory.
    */
    public function newController($controller, $deep = true, $isInteger = false)
    {
        if ($deep) {
            $this->deep[] = $controller;
        } else {
            $this->nodeep[] = $controller;
        }
        $this->$controller = new PMA_routeController($controller, $isInteger);
    }

    /**
    * Get controller route helper.
    */
    public function getRoute($controller)
    {
        return $this->$controller->getCurrentRoute();
    }

    /**
    * User navigation controller helper.
    */
    public function checkNavigation($controller)
    {
        $this->$controller->controleUserNavigation();
        if ($this->$controller->isRrouteModified() && ! $this->historyLoaded) {
            // Load history once.
            $this->historyLoaded = true;
            $this->loadHistoryDeep($controller);
        }
    }

    /**
    * Load current nodeep $navigation history.
    */
    public function loadHistorynoDeep()
    {
        $nav = $_SESSION['navigation'];
        foreach ($this->nodeep as $controller) {
            if (isset($nav[$controller])) {
                $route = $nav[$controller];
                $this->$controller->setCurrentRoute($route);
            }
        }
    }

    /**
    * Load current deep $navigation history.
    */
    public function loadHistoryDeep($from = null)
    {
        $nav = $_SESSION['navigation'];
        $route = null;
        $reached = is_null($from);
        foreach ($this->deep as $controller) {
            if (isset($nav[$controller])) {
                if ($reached) {
                    $route = $nav[$controller];
                    $this->$controller->setCurrentRoute($route);
                } else {
                    $route = $this->getRoute($controller);
                }
            } else {
                if ($reached) {
                    $this->$controller->setCurrentRoute(null);
                }
            }
            // Update $nav.
            if (isset($nav[$route])) {
                $nav = $nav[$route];
            }
            // Update $reached.
            if (! $reached) {
                $reached = ($controller === $from);
            }
        }
    }

    /**
    * Save current route in $navigation.
    */
    public function saveHistory()
    {
        // No deep
        $nav = &$_SESSION['navigation'];
        foreach ($this->nodeep as $controller) {
            $nav[$controller] = $this->getRoute($controller);
        }
        // Deep
        $nav = &$_SESSION['navigation'];
        foreach ($this->deep as $controller) {
            $route = $this->getRoute($controller);
            if (! is_null($route)) {
                if (isset($parent)) {
                    $nav = &$nav[$parent];
                }
                $nav[$controller] = $route;
                $parent = $route;
            }
        }
    }

    /**
    * Miscellaneous navigation.
    */
    public function controlMiscNavigation($id, $isInteger = true)
    {
        /**
        * Set the reference to the current route.
        */
        $nav = &$_SESSION['navigation'];
        foreach ($this->deep as $controller) {
            $route = $this->getRoute($controller);
            if ($this->$controller->routeExist($route)) {
                $nav = &$nav[$route];
            } else {
                break;
            }
        }

        if (isset($_GET[$id])) {
            if ($_GET[$id] === 'unset') {
                unset($nav['misc'][$id]);
                if (empty($nav['misc'])) {
                    unset($nav['misc']);
                }
            } else {
                if ($isInteger) {
                    $_GET[$id] = (int)$_GET[$id];
                }
                $nav['misc'][$id] = $_GET[$id];
            }
        }
        if (isset($nav['misc'][$id])) {
            return $nav['misc'][$id];
        }
    }

    /**
    * Get current miscellaneous.
    */
    public function getMiscNavigation($id)
    {
        $nav = $_SESSION['navigation'];
        foreach ($this->deep as $controller) {
            $route = $this->getRoute($controller);
            if (isset($nav[$route])) {
                $nav = $nav[$route];
            } else {
                break;
            }
        }
        if (isset($nav['misc'][$id])) {
            return $nav['misc'][$id];
        }
    }

    public function removeMisc($id)
    {
        $this->removeElementRecursive($_SESSION['navigation'], 'misc', $id);
    }

    /**
    * Remove all occurences recursively helper.
    */
    private function removeElementRecursive(&$array, $type, $id)
    {
        foreach($array as $key => &$value) {
            if ($key === $type) {
                unset($array[$type][$id]);
                if (empty($array[$type])) {
                    unset($array[$type]);
                }
            } elseif (is_array($value)) {
                $this->removeElementRecursive($value, $type, $id);
            }
        }
    }

    public function removeHistory($controller, $id)
    {
        $this->removeHistoryRecursive($_SESSION['navigation'], $controller, $id);
    }

    public function removeHistoryRecursive(&$array, $controller, $id)
    {
        foreach($array as $key => &$value) {
            if ($key === $controller && isset($array[$id])) {
                unset($array[$key], $array[$id]);
            } elseif (is_array($value)) {
                $this->removeHistoryRecursive($value, $controller, $id);
            }
        }
    }

    /**
    * Table navigation.
    */
    public function getTableNavigation($defaultSort)
    {
        /**
        * Set the reference to the current route.
        */
        $nav = &$_SESSION['navigation'];
        foreach ($this->deep as $controller) {
            $route = $this->getRoute($controller);
            if ($this->$controller->routeExist($route)) {
                $nav = &$nav[$route];
            } else {
                break;
            }
        }
        $nav = &$nav['table'];
        /**
        * Initialize navigation.
        */
        if (is_null($nav)) {
            $nav['sort'] = $defaultSort;
            $nav['page'] = 1;
        }
        /**
        * Get user sort.
        */
        if (isset($_GET['sort'])) {
            $nav['sort'] = $_GET['sort'];
            if (isset($_GET['reverse'])) {
                $nav['reverse'] = true;
            } else {
                unset($nav['reverse']);
            }
        }
        /**
        * Get user page.
        */
        if (isset($_GET['tablePage'])) {
            $nav['page'] = (int)$_GET['tablePage'];
        }

        $tableNav = $nav;
        $tableNav['defaultSort'] = $defaultSort;
        return $tableNav;
    }
}

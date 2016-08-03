<?php

 /**
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

class PMA_routeController
{
    /**
    * Route ID.
    */
    private $id;
    /**
    * availables routes table.
    */
    private $routesTable = array();
    /**
    * Default route.
    */
    private $defaultRoute;
    /**
    * Current route.
    */
    private $currentRoute;
    /**
    * Flag: route has changed.
    */
    private $routeHasChanged = false;
    /**
    * Flag: user requested a new route.
    */
    private $routeRequestedByUser = false;

    public function __construct($id, $isInteger)
    {
        $this->id = $id;
        /**
        * Setup integer routes.
        */
        if ($isInteger && isset($_GET[$this->id])) {
            $_GET[$this->id] = (int)$_GET[$this->id];
        }
    }

    /**
    * @return boolean - is route has been modified.
    */
    public function isRrouteModified()
    {
        return $this->routeHasChanged;
    }

    /**
    * @return boolean - is a new route requested by user.
    */
    public function isNewUserRoute()
    {
        return $this->routeRequestedByUser;
    }

    /**
    * @return boolean - is a route is exist.
    */
    public function routeExist($route)
    {
        return in_array($route, $this->routesTable, true);
    }

    /**
    * Set current route.
    */
    public function setCurrentRoute($route)
    {
        $this->currentRoute = $route;
    }

    /**
    * Add the default route name.
    */
    public function setDefaultRoute($route)
    {
        $this->defaultRoute = $route;
    }

    /**
    * Add a route.
    */
    public function addRoute($route)
    {
        $this->routesTable[] = $route;
    }

    /**
    * @return array - availables routes table.
    */
    public function getRoutesTable()
    {
        return $this->routesTable;
    }

    /**
    * @return string - current route name.
    */
    public function getCurrentRoute()
    {
        return $this->currentRoute;
    }

    /**
    * Control user navigation.
    */
    public function controleUserNavigation()
    {
        /**
        * Check if user requested a route.
        */
        if (isset($_GET[$this->id]) && $this->routeExist($_GET[$this->id])) {
            if (! is_null($this->currentRoute) && $_GET[$this->id] !== $this->currentRoute) {
                $this->routeRequestedByUser = true;
                $this->routeHasChanged = true;
            }
            $this->setCurrentRoute($_GET[$this->id]);
        /**
        * Otherwise, check if the current route is valid.
        */
        } elseif (! $this->routeExist($this->currentRoute)) {
            /**
            * Get the default route.
            */
            if ($this->routeExist($this->defaultRoute)) {
                $this->routeHasChanged = true;
                $this->setCurrentRoute($this->defaultRoute);
            /**
            * At last, get the first route found.
            */
            } elseif (! empty($this->routesTable)) {
                $this->routeHasChanged = true;
                $this->setCurrentRoute(reset($this->routesTable));
            } else {
                $this->setCurrentRoute(null);
            }
        }
    }
}

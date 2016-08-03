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

class PMA_MurmurMeta
{
    private $meta;
    private $secret;
    private $defaultConf = array();
    private $version = array();

    public function isConnected()
    {
        return is_object($this->meta);
    }

    public function setMeta(Ice_ObjectPrx $meta)
    {
        $this->meta = $meta;
    }

    public function setDefaultConf($default)
    {
        $this->defaultConf = $default;
    }

    public function setSecret($secret)
    {
        $this->secret = $secret;
    }

    public function setVersion($version)
    {
        $this->version = $version;
    }

    public function getVersion($key)
    {
        if (is_array($this->version) && isset($this->version[$key])) {
            return $this->version[$key];
        }
    }

    /**
    * Add secret context to ICE proxy.
    */
    private function addSecretCtx($prx)
    {
        if (is_array($this->secret) && ! empty($this->secret)) {
            $prx = $prx->ice_context($this->secret);
        }
        return $prx;
    }

    /**
    * Add PMA_MurmurServer class to ICE proxy
    */
    private function serverClassFactory($prx)
    {
        $prx = $this->addSecretCtx($prx);
        return new PMA_MurmurServer($prx, $this->getDefaultConf());
    }

    /**
    * **********************************************
    * **********************************************
    * Murmur_Meta methods
    * **********************************************
    * **********************************************
    */

    /**
    * Return cached result during connection.
    */
    public function getDefaultConf()
    {
        return $this->defaultConf;
    }

    /**
    * Add secret ctx & get PMA serverClass to all servers.
    */
    public function getAllServers()
    {
        $servers = $this->meta->getAllServers();
        foreach ($servers as &$prx) {
            $prx = $this->serverClassFactory($prx);
        }
        return $servers;
    }

    /**
    * Add secret ctx & get PMA serverClass to all booted.
    */
    public function getBootedServers()
    {
        $booted = $this->meta->getBootedServers();
        foreach ($booted as &$prx) {
            $prx = $this->serverClassFactory($prx);
        }
        return $booted;
    }

    /**
    * Add secret ctx & get PMA serverClass.
    */
    public function newServer()
    {
        $prx = $this->meta->newServer();
        if (! is_null($prx)) {
            $prx = $this->serverClassFactory($prx);
        }
        return $prx;
    }

    /**
    * Add secret ctx & get PMA serverClass.
    */
    public function getServer($id)
    {
        $prx = $this->meta->getServer((int)$id);
        if (! is_null($prx)) {
            $prx = $this->serverClassFactory($prx);
        }
        return $prx;
    }

    /**
    * Return integer or null if getUptime method don't exists.
    */
    public function getUptime()
    {
        if (method_exists('Murmur_Meta', 'getUptime')) {
            return $this->meta->getUptime();
        }
    }
}

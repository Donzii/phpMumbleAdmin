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

/**
* Ice connection abstract class
*/
abstract class PMA_MurmurConnectionIce extends PMA_debugSubject
{
    /**
    * ICE proxy interface.
    */
    protected $ICE;
    /**
    * Murmur_Meta proxy interface.
    */
    protected $meta;
    /**
    * Murmur version array.
    */
    public $version = array();
    /**
    * Murmur default configuration array.
    */
    public $defaultConf = array();
    /**
    * Connection parameters.
    */
    public $host = '127.0.0.1';
    public $port = 6502;
    public $timeout = 10;
    public $secret = '';
    public $slice_profile = '';
    public $slice_php = '';

    abstract protected function loadSlice();
    abstract protected function getIce();
    abstract protected function connection();

    /**
    * Send a debug message.
    */
    protected function debug($text, $level = 2, $error = false)
    {
        $text = __class__ .' '.$text;
        parent::debug($text, $level, $error);
    }

    /**
    * Throw PMA Exception
    * $key can also be the exception object.
    */
    protected function throwException($key)
    {
        // Exception object.
        if (is_object($key)) {
            $array = pmaGetExceptionClass($key);
            $key = $this->getExceptionKey($array['class'], $array['text']);
            $this->debugError($array['class']);
        } else {
            $this->debugError($key);
        }
        throw new PMA_IceConnectionException($key);
    }

    protected function getExceptionKey($class, $text)
    {
        switch($class) {
            case 'Ice_ProfileNotFoundException':
                $key = 'Ice_ProfileNotFoundException';
                break;
            case 'Ice_EndpointParseException':
                if (false !== stripos($text, 'no argument provided')) {
                    $key = 'missing_argument';
                    break;
                }
                if (false !== stripos($text, 'invalid port value')) {
                    $key = 'invalid_port';
                    break;
                }
                $key = 'Ice_UnknownErrorException';
                break;
            case 'Ice_DNSException':
                $key = 'Ice_DNSException';
                break;
            case 'Ice_ConnectionRefusedException':
                $key = 'Ice_ConnectionRefusedException';
                break;
            case 'Ice_ConnectTimeoutException':
                $key = 'Ice_ConnectTimeoutException';
                break;
            case 'Murmur_InvalidSecretException':
                $key = 'Murmur_InvalidSecretException';
                break;
            default:
                $key = 'Ice_UnknownErrorException';
                break;
        }
        return $key;
    }

    public function getMeta()
    {
        $this->initialize();
        $this->loadSlice();
        $this->getIce();
        $this->debug('Connecting to '.$this->host.':'.$this->port);
        $this->connection();
        $this->sliceDefinitionsSanity();
        $this->getMurmurDefaultConf();
        $this->getMurmurVersion();
        // Success
        $this->debug('Connected :)');
        return $this->meta;
    }

    protected function initialize()
    {
        $this->debug(__function__);
        if (! extension_loaded('ice')) {
            $this->throwException('ice_module_not_found');
        }
        /**
        * Theses invalid parameters return an "EndpointParseException".
        * Prevent it and send a specific error message.
        */
        if ($this->host === '') {
            $this->throwException('Ice_DNSException');
        }
        if (! is_int($this->timeout) OR $this->timeout <= 0) {
            $this->throwException('invalid_numerical');
        }
        if ($this->secret !== '') {
            $this->secret = array('secret' => $this->secret);
        } else {
            $this->secret = array();
        }
        /**
        * MEMO:
        * Ice timeout is in millisecondes.
        * WORKAROUND:
        * Zeroc ice use a retry function if timeout is reached to be sure it's a timeout.
        * It's nice but it's multiply by 2 the delay. So divide by 2 the timeout parameter.
        */
        $this->timeout = $this->timeout * 500;
    }

    protected function loadSliceIce32()
    {
        $this->debug(__function__);
        // check if slices profiles are activated
        $slice_profiles_file = ini_get('ice.profiles');
        if ($slice_profiles_file === '') {
            $this->slice_profile = '';
        }
        try {
            Ice_loadProfile($this->slice_profile);
        } catch (Exception $e) {
            $this->throwException($e);
        }
    }

    protected function loadSliceIce34()
    {
        $this->debug(__function__);
        /**
        * php-Ice 3.4 require a workaround to load Ice.php and slices definitions.
        * see includes/ice34Workaround.inc
        * Just check that Ice.php has been loaded.
        */
        if (! interface_exists('Ice_Object')) {
            $this->throwException('ice_could_not_load_Icephp_file');
        }
    }

    protected function loadSliceIce35()
    {
        $this->debug(__function__);
        if (1 !== @include 'Ice.php') {
            $this->throwException('ice_could_not_load_Icephp_file');
        }
        if (is_file($file = PMA_DIR_SLICE_PHP_CUSTOM_35.$this->slice_php)) {
            include $file;
        } elseif (is_file($file = PMA_DIR_SLICE_PHP_35.$this->slice_php)) {
            include $file;
        }
    }

    protected function getIce32()
    {
        $this->debug(__function__);
        global $ICE;
        $this->ICE = $ICE;
    }

    protected function getIce34()
    {
        $this->debug(__function__);
        try {
            $this->ICE = Ice_initialize();
        } catch (Exception $e) {
            $this->throwException($e);
        }
    }

    protected function connectionIce32()
    {
        $this->debug(__function__);
        try {
            $proxy = $this->ICE->stringToProxy('Meta:tcp -h '.$this->host.' -p '.$this->port.' -t '.$this->timeout);
        } catch (Exception $e) {
            $this->throwException($e);
        }
        if (! interface_exists('Murmur_Meta')) {
            $this->throwException('ice_no_slice_definition_found');
        }
        try {
            $this->meta = $proxy->ice_checkedCast('::Murmur::Meta')->ice_context($this->secret);
        } catch (Exception $e) {
            $this->throwException($e);
        }
    }

    protected function connectionIce35()
    {
        $this->debug(__function__);
        /**
        * Memo for Meta -e 1.0
        * See http://doc.zeroc.com/display/Doc/New+in+Ice+3.5%3A+Encoding+Version+1.1
        */
        try {
            $proxy = $this->ICE->stringToProxy(
                'Meta -e 1.0 :tcp -h '.$this->host.' -p '.$this->port.' -t '.$this->timeout
            );
        } catch (Exception $e) {
            $this->throwException($e);
        }
        if (! interface_exists('Murmur_Meta')) {
            $this->throwException('ice_no_slice_definition_found');
        }
        try {
            $this->meta = Murmur_MetaPrxHelper::checkedCast($proxy)->ice_context($this->secret);
        } catch (Exception $e) {
            $this->throwException($e);
        }
    }

    /**
    * Check for known error with slices definitions
    */
    protected function sliceDefinitionsSanity()
    {
        $this->debug(__function__);
        // ICE 3.2 : If getRegistration method do not exists, Web master need to hack Murmur.ice.
        if (! method_exists('Murmur_Server', 'getRegistration')) {
            $this->throwException('ice_invalid_slice_file');
        }
        // getUsers() method comes with murmur 1.2.0, if not exists, slice file is invalid.
        if (! method_exists('Murmur_Server', 'getUsers')) {
            $this->throwException('ice_invalid_slice_file');
        }
    }

    /**
    * Get Murmur default configuration
    * and check for a valid secret / readsecret
    */
    protected function getMurmurDefaultConf()
    {
        $this->debug(__function__);
        try {
            $this->defaultConf = $this->meta->getDefaultConf();
        } catch (Exception $e) {
            $this->throwException($e);
        }
    }

    /**
    * Get murmur version.
    * MEMO:
    * Murmur 1.2.1 doesn't check for a valid secret with getVersion()
    * only with Murmur 1.2.2 and superior.
    */
    protected function getMurmurVersion()
    {
        $this->debug(__function__);
        try {
            $this->meta->getVersion($a, $b, $c, $d);
        } catch (Exception $e) {
            $this->throwException($e);
        }

        $this->version['int'] = intval($a.$b.$c);
        $this->version['str'] = $a.'.'.$b.'.'.$c;

        if ($d !== '' && $d !== $this->version['str']) {
            $this->version['txt'] = $this->version['str'].' - '.$d;
        } else {
            $this->version['txt'] = $this->version['str'];
        }
        /**
        * PMA works for murmur 1.2.0 and superior only.
        */
        if ($this->version['int'] < 120) {
            $this->throwException('ice_invalid_murmur_version');
        }
    }
}

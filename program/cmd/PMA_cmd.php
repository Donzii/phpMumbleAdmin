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

class PMA_cmdException extends Exception {}

abstract class PMA_cmd
{
    protected $PMA;
    protected $PARAMS;

    protected $redirection;

    abstract public function process();

    public function __construct()
    {
        $this->PMA = PMA_core::getInstance();
    }

    public static function factory(array $params)
    {
        switch ($params['cmd']) {
            case 'auth':
            case 'config':
            case 'config_admins':
            case 'config_ICE':
            case 'install':
            case 'logout':
            case 'murmur_logs':
            case 'murmur_acl':
            case 'murmur_bans':
            case 'murmur_channel':
            case 'murmur_groups':
            case 'murmur_registrations':
            case 'murmur_settings':
            case 'murmur_users_sessions':
            case 'overview';
            case 'pw_requests':
            case 'routes':
                $class = 'PMA_cmd_'.$params['cmd'];
                return new $class();
        }
    }

    public function setParameters(array $params)
    {
        $this->PARAMS = $params;
        $this->debug(__class__ .'_'.$params['cmd'].' invoked');
    }

    protected function debug($text, $level = 1)
    {
        $this->PMA->debug($text, $level);
    }

    protected function debugError($text, $level = 1)
    {
        $this->PMA->debugError($text, $level);
    }

    protected function message($key)
    {
        $this->PMA->message($key);
    }

    protected function messageError($key)
    {
        $this->PMA->messageError($key);
    }

    protected function log($level, $message)
    {
        $this->PMA->log($level, $message);
    }

    protected function logUserAction($message)
    {
        $user = $this->PMA->user;
        // Dont to log SuperAdmin actions if not requested
        if (! $this->PMA->config->get('pmaLogs_SA_actions') && $user->is(PMA_USER_SUPERADMIN)) {
            return;
        }
        if ($user->adminID !== null) {
            $message = $user->adminID.'# '.$user->login.' - '.$message;
        } else {
            $message = pmaGetClassName($user->class).' - '.$message;
        }
        $this->log('action.info', $message);
    }

    /**
    * Get PMA_MurmurMeta helper
    */
    protected function getMurmurMeta()
    {
        PMA_MurmurMetaHelper::connection();
        if (! $this->PMA->meta->isConnected()) {
            $this->throwException();
        }
    }

    /**
    * meta::getServer() method helper
    */
    protected function getServerPrx($sid)
    {
        if (is_null($prx = $this->PMA->meta->getServer($sid))) {
            $this->messageError('Murmur_InvalidServerException');
            $this->throwException();
        }
        return $prx;
    }

    /**
    * Process autoBan feature
    */
    protected function autoBanAttempts()
    {
        if ($this->PMA->config->get('autoban_attempts') > 0) {

            $userIP = $_SERVER['REMOTE_ADDR'];

            $autoBan = new PMA_datas_autoban($userIP);
            $autoBan->setConf('attempts', $this->PMA->config->get('autoban_attempts'));
            $autoBan->setConf('frame', $this->PMA->config->get('autoban_frame'));
            if ($autoBan->checkIP()) {
                $autoBan->deleteIP($userIP);
                $this->PMA->bans->add($userIP, $this->PMA->config->get('autoban_duration'), 'Added from autoban');
                $this->log('autoBan.info', 'IP address has been auto-banned');
                $this->PMA->bans->killPma();
            }
        }
    }

    /**
    * Throw command exception.
    */
    protected function throwException()
    {
        throw new PMA_cmdException();
    }

    protected function setRedirection($redirect)
    {
        if ($redirect === 'referer') {
            $this->redirection = $_SESSION['referer'];
        } else {
            $this->redirection = $redirect;
        }
    }

    public function getRedirection()
    {
        return $this->redirection;
    }

    /**
    * Perform common end actions.
    */
    public function cmdShutdown()
    {
        if ($this->PMA->config->get('debug') > 0) {
            $_SESSION['cmd_stats']['duration'] = PMA_statsHelper::duration(PMA_STARTED);
            $_SESSION['cmd_stats']['memory'] = PMA_statsHelper::memory();
            $_SESSION['cmd_stats']['ice'] = PMA_statsHelper::iceQueries();
        }
    }
}

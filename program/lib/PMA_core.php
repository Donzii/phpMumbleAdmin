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
* Core of the PMA project.
*/
class PMA_core
{
    /**
    * Singleton
    */
    public static function getInstance()
    {
        static $instance;
        if (is_null($instance)) {
            $instance = new self();
        }
        return $instance;
    }

    private function __construct()
    {
        $this->messages = array();
        $this->router = new PMA_router();
        $this->db = PMA_db::instance(); // Rework required
        $this->app = new PMA_datas_app();
        $this->config = new PMA_datas_config();
        $this->bans = new PMA_datas_bans();
        $this->cookie = new PMA_cookie();
        $this->session = new PMA_session();
        $this->profiles = new PMA_datas_profiles();
        $this->user = new PMA_user();
        $this->meta = new PMA_MurmurMeta();
        $this->skeleton = new PMA_skeleton();
    }

    /**
    * Add a debug message
    */
    public function debug($message, $level = 1, $error = false)
    {
        $this->messages['debug'][] = array(
            'level' => $level,
            'error' => $error,
            'msg' => $message
        );
    }

    public function debugError($message)
    {
        $this->debug($message, 1, true);
    }

    /**
    * Add an user message
    */
    public function message($key, $type = 'success')
    {
        if (is_array($key)) {
            $message['key'] = $key[0];
            $message['sprintf'] = $key[1];
        } else {
            $message['key'] = $key;
        }
        $message['type'] = $type;
        $this->messages['box'][] = $message;
    }

    /**
    * Add an user error message
    */
    public function messageError($key)
    {
        $this->message($key, 'error');
    }

    public function messageIceError($key)
    {
        $this->messages['iceError'] = $key;
    }

    /**
    * Redirection
    */
    public function redirection($redirection = null)
    {
        if ($redirection === null) {
            $redirection = './';
        }
        /**
        * Shutdown.
        */
        $this->shutdown();
        $this->debug(__method__);
        /**
        * Cache all messages in $_SESSION.
        */
        $_SESSION['messages'] = $this->messages;
        /**
        * Setup headers.
        */
        header('Status: 303 See other');
        header('location:'.$redirection);
        /**
        * Make sur to not execute extra code before redirection.
        */
        die();
    }

    /**
    * >rite log in a file
    */
    public function log($level, $message, $file = PMA_FILE_LOGS)
    {
        /**
        * 'a' = Open for writing only; place the file pointer at the end of the file.
        * If the file does not exist, attempt to create it.
        */
        $fp = @fopen($file, 'ab');
        if (is_resource($fp)) {
            /**
            * PMA print a human readable date time inside the log file,
            * use default timezone for it.
            */
            setTimezone($this->config->get('default_timezone'));
            /**
            * MEMO : [0]timestamp ::: [1]dateTime ::: [2]logLvl ::: [3]ip ::: [4]message ::: [5]EOL
            */
            $timestamp = time();
            $dateTime = date('H:i:s - Y-m-d', $timestamp);
            $level = '['.$level.']';
            $ip = $_SERVER['REMOTE_ADDR'];
            fwrite($fp, $timestamp.':::'.$dateTime.':::'.$level.':::'.$ip.':::'.$message.':::'.PHP_EOL);
            fclose($fp);
            /**
            * Back to user timezone
            */
            setTimezone($this->cookie->get('timezone'));
        }
    }

    /**
    * Logout PMA user.
    */
    public function logout()
    {
        $this->user->resetAuth();
        $this->router->resetNavigation();
        unset($_SESSION['page_vserver']);
    }

    /**
    * Fatal error
    */
    public function fatalError($message = '')
    {
        die('<strong style="color: red;">phpMumbleAdmin fatal error</strong> ::: '.$message);
    }

    /**
    * Shutdown operations.
    */
    public function shutdown()
    {
        if (! isset($this->shutdown)) {
            // Allow once, as PMA can call this method during the script.
            $this->shutdown = true;
            $this->debug(__method__, 3);
            $this->db->updateQueued();
            foreach ($this->db->getQueuedKey() as $key) {
                $this->debug(__method__ .' '.$key.' datas updated', 3);
            }
            if ($this->cookie->update()) {
                $this->debug(__method__ .' Cookie updated', 3);
            }
            $this->debug(__method__ .' Update router history', 3);
            $this->router->saveHistory();
        }
    }
}


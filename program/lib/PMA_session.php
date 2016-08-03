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

class PMA_session
{
    /**
    * Sanity on all session files in secondes (default : every day).
    */
    const SESSION_FILES_SANITY = 86400;

    private $cookieName = 'phpMumbleAdmin_session';
    private $sessDirectory;
    private $cookiePath;
    private $autoLogout = 15;

    public function setDirectory($directory)
    {
        $this->sessDirectory = $directory;
    }

    public function setCookiePath($path)
    {
        $this->cookiePath = $path;
    }

    public function setAutoLogout($int)
    {
        $this->autoLogout = $int;
    }

    /**
    *
    * @return boolean.
    */
    public function isSsanityRequired($lastCheck)
    {
        if (! is_int($lastCheck)) {
            $lastCheck = 1;
        }
        return ((time() - $lastCheck) > self::SESSION_FILES_SANITY);
    }

    /**
    * Check is the session cookie is writable.
    *
    * @return boolean
    */
    public function isWritableDir()
    {
        return (is_dir($this->sessDirectory) && is_writeable($this->sessDirectory));
    }

    /**
    * Remove outdated sessions files.
    */
    public function removeOutdatedSessions()
    {
        $scanDir = scanDir($this->sessDirectory);

        foreach ($scanDir as $entry) {
            if (substr($entry, 0, 5) === 'sess_') {
                $path = $this->sessDirectory . $entry;
                if ((time() - filemtime($path)) > $this->autoLogout) {
                    unlink($path);
                }
            }
        }
    }

    /**
    * Start user session
    */
    public function start()
    {
        session_save_path($this->sessDirectory);
        session_name($this->cookieName);
        session_set_cookie_params(0, $this->cookiePath);
        session_start();
    }

    /**
    * Initialize $_SESSION.
    */
    public function initialize()
    {
        // Check for autologout.
        if (isset($_SESSION['last_activity'])) {
            if ((time() - $_SESSION['last_activity']) > $this->autoLogout) {
                $_SESSION = array();
            }
        }
        // Mark user as proxyed if his IP address have changed at least one time, keep last ip.
        if (isset($_SESSION['current_ip'])) {
            if ($_SESSION['current_ip'] !== $_SERVER['REMOTE_ADDR']) {
                $_SESSION['proxy'] = $_SESSION['current_ip'];
            }
        }
        // Update current IP
        $_SESSION['current_ip'] = $_SERVER['REMOTE_ADDR'];
        // Update last activity timestamp.
        $_SESSION['last_activity'] = time();

        if (! isset($_SESSION['referer'])) {
            $_SESSION['referer'] = './';
        }
    }

    public function updateReferer()
    {
        $_SESSION['referer'] = PMA_HTTP_HOST.$_SERVER['REQUEST_URI'];
    }

    /**
    * Merge cached messages in session with the submitted messages array.
    */
    public function mergeMessages(array $messages)
    {
        if (isset($_SESSION['messages'])) {
            $messages = array_merge_recursive($_SESSION['messages'], $messages);
            unset($_SESSION['messages']);
        }
        return $messages;
    }
}

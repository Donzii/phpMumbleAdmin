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

/**
* TODO
*/
if (! defined('PMA_STARTED')) { die('ILLEGAL: You cannot call this script directly !'); }

class PMA_messages
{
    private $iceErrorStore;
    private $messagesStore = array();
    private $debugsStore = array();

    /**
    * Add a debug message
    */
    public function debug($text, $level = 1, $error = false)
    {
        $debug = new stdClass();
        $debug->level = $level;
        $debug->error = $error;
        $debug->text = $text;
        $this->debugsStore[] = $debug;
    }

    public function getDebugs()
    {
        return $this->debugsStore;
    }

    /**
    * Add an user message
    */
    public function message($key, $type = 'success')
    {
        if (is_array($key)) {
            $key = $key[0];
            $sprintf = $key[1];
        } else {
            $key = $key;
            $sprintf = null;
        }
        if ($type !== 'success') {
            $type = 'error';
        }

        $message = new stdClass();
        $message->key = $key;
        $message->type = $type;
        $message->sprintf = $sprintf;
        $this->messagesStore[] = $message;
    }

    public function getMessages()
    {
        return $this->messagesStore;
    }

    public function iceError($key)
    {
        $this->iceErrorStore = $key;
    }

    public function getIceError()
    {
        return $this->iceErrorStore;
    }

    public function getFromSession()
    {
        if (isset($_SESSION['messages'])) {
            foreach ($_SESSION['messages'] as $key => $array) {
                $this->$key = $array;
            }
            unset($_SESSION['messages']);
        }
    }

    public function saveInSession()
    {
        $_SESSION['messages']['iceErrorStore'] = $this->iceErrorStore;
        $_SESSION['messages']['messagesStore'] = $this->messagesStore;
        $_SESSION['messages']['debugsStore'] = $this->debugsStore;
    }
}

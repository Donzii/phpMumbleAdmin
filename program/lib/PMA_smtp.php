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
* Mail end of line
*/
define('MEOL', "\r\n");

/**
* Simple smtp client
*/
class PMA_smtp
{
    /**
    * Max characters size for a response.
    */
    const RESPONSE_SIZE = 2048;
    /**
    * Max characters size for a line to send.
    */
    const LINE_WRAP = 72;
    /**
    * Smtp server host / port.
    */
    private $host;
    private $port;
    /**
    * Smtp socket.
    */
    private $socket;
    /**
    * All dialogues with the smtp server.
    */
    public $dialogues = array();

    public function __construct($host = '127.0.0.1', $port = 25)
    {
        $this->host = $host;
        $this->port = $port;
    }

    private function error($msg)
    {
        $this->addDialogue('ERROR', -1, trim($msg));
    }

    private function addDialogue($direction, $code, $text)
    {
        $dial = new stdclass();
        $dial->error = ($direction === 'ERROR'); // For the futur ? Not usefull for now.
        $dial->code = $code;
        $dial->text = utf8_encode($text);
        $dial->debug = __class__ .' '.$direction.' '.$text;
        $this->dialogues[] = $dial;
    }

    public function getLastCode()
    {
        $last = end($this->dialogues);
        return $last->code;
    }

    public function getLastDialogue()
    {
        $last = end($this->dialogues);
        return $last->text;
    }

    public function connection()
    {
        $this->addDialogue('CONN', 1, 'Connecting to '.$this->host.':'.$this->port.'...');
        $this->socket = @fsockopen('tcp://'.$this->host, $this->port, $errno, $errstr);
        if (! is_resource($this->socket)) {
            $this->error('Could not connect => '.$errno.' - '.$errstr);
        } else {
            $this->getResponse();
        }
    }

    public function quit()
    {
        if (is_resource($this->socket)) {
            $this->sendCommand('QUIT');
            $this->getResponse();
            $this->closeSocket();
        }
    }

    public function closeSocket()
    {
        @fclose($this->socket);
    }

    /**
    * Send multiple commandes in a row
    */
    public function sendCommand($cmd)
    {
        if (! is_resource($this->socket)) {
            $this->error(__function__ .': Invalid ressource. Not connected ?');
        } else {

            if (! is_array($cmd)) {
                $cmd = array($cmd);
                $dialText = 'SEND';
            } else {
                $dialText = 'PIPE';
            }

            // Cut too long lines and
            // add all commandes to the dialogue, line by line.
            foreach ($cmd as &$line) {
                //$line = wordwrap($line, self::LINE_WRAP, MEOL, true); // Bug with linux php.
                $this->addDialogue($dialText, 1, $line);
            }

            $join = join(MEOL, $cmd);

            @fwrite($this->socket, $join.MEOL);
        }
    }

    public function getResponse($byline = false)
    {
        if (! is_resource($this->socket)) {
            $this->error(__function__ .': Invalid ressource. Not connected ?');
        } else {

            if ($byline === true) {
                // Return the current line
                // On UNIX system, this is required for pipelining
                $response = fgets($this->socket, self::RESPONSE_SIZE);
            } else {
                // Get all responses in a row
                $response = fread($this->socket, self::RESPONSE_SIZE);
            }

            $response = trim($response);

            /**
            * Empty response means a timeout or the remote server closed the connection.
            * It can be also the result of a bug from PMA where it's wait for an answer which will never come
            * ( untill the timeout is reached).
            * Memo: do not send commande QUIT, it will increase the timeout by two.
            * Close the socket instead of.
            */
            if ($response === '') {
                $this->closeSocket();
                $this->error(__function__ .'(): Connection timeout waiting for a response...');
                return;
            }

            $responses = explode(MEOL, $response);

            foreach ($responses as $line) {

                $code = substr($line, 0, 3);

                if (ctype_digit($code)) {
                    $code = (int)$code;
                } else {
                    $code = -1;
                }

                $this->addDialogue('RESP', $code, $line);
            }
        }
    }

    /**
    * Send command and get response helper
    */
    public function sendAndGetResponse($cmd, $pipelining = false)
    {
        $this->sendCommand($cmd);
        $this->getResponse($pipelining);
    }
}

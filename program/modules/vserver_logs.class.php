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
* @return string - src image ok/cancel.
*/
function getBooleanImgSrc($boolean)
{
    if ($boolean === true) {
        return IMG_OK_16;
    }
    return IMG_CANCEL_16;
}

class PMA_logsMumble extends PMA_logs
{
    protected $diffUTC;

    /**
    * Special traitement for the "Moved" string in murmur logs:
    * try to find if it's self action or on another user
    */
    protected function applyMovedWorkaround($text)
    {
        if (
            (false !== stripos($text, 'Moved')) &&
            (false === stripos($text, 'Moved channel')) &&
            (false === stripos($text, 'Removed'))
        ) {
            /**
            * example:
            * <35:ipnoz(16)> Moved ipnoz:16(35) to channelName[2:0]
            * $actor = "<35:ipnoz(16)>"
            * $target = "ipnoz:16(35) to channelName[2:0]"
            */
            list($actor, $target) = explode(' Moved', $text);
            // Find session id -> "<35:"
            preg_match('/^<[0-9]+:/', $actor, $session);
            $session['id'] = substr($session[0], 1, -1);
            // Find user id -> "(16)>" or "(-1)>"
            preg_match('/\([0-9]+\)>$|\(-1\)>$/', $actor, $uid);
            $uid['id'] = substr($uid[0], 1, -2);
            // Find login - remove session and uid string
            $login = str_replace(array($session[0], $uid[0]), '', $actor);
            // Now reconstruct target string like murmur do in the logs ( ie: "ipnoz:35(16) to" )
            $reconstructed = $login.':'.$session['id'].'('.$uid['id'].') to';
            // Check if the recontructed target is different.
            if (false !== strpos($target, $reconstructed)) {
                // Match, user moved self.
                $text = $actor.' Moved to channel'.str_replace($reconstructed, '', $target);
            } else {
                // Different, user have moved another user
                $text = $actor.' Moved user'.$target;
            }
        }
        return $text;
    }

    /**
    * Workaround for the timestamp bug with Murmur_Server::getLog() method:
    * getLog() return a modified timestamp if the OS time is not UTC+00.
    *
    * examples of getLog():
    * UTC+01 return the timestamp -3600 secondes.
    * UTC+02 return the timestamp -7200 secondes
    * UTC-03 return the timestamp +10800 secondes
    * etc...
    *
    * This workaround have been tested for linux.
    * strftime('%z') return the time difference (+0100, +0200, -0300 etc...).
    *
    * On windows, it's definitly not working.
    * See http://php.net/manual/en/function.strftime.php
    */
    protected function logTimestampWorkaround()
    {
        // Get diff once, cache the result.
        if (is_null($this->diffUTC)) {
            $this->diffUTC = strftime('%z');
            if (is_numeric($this->diffUTC)) {
                if (substr($this->diffUTC, -2) === '00') {
                    $this->diffUTC = substr($this->diffUTC, 0, -2);
                }
                $this->diffUTC = $this->diffUTC * 3600;
            } else {
                $this->diffUTC = 0;
            }
        }
        return $this->diffUTC;
    }

    /**
    * Add custom controls.
    */
    protected function logControl($log)
    {
        $log->text = $this->applyMovedWorkaround($log->text);
        // Apply timestamp workaround
        $log->timestamp += $this->logTimestampWorkaround();
        return parent::logControl($log);
    }
}

class filtersMenu
{
    public $menu = array();

    public function addFilterLink($href, $text, $img)
    {
        $filter = new stdClass();
        $filter->href = $href;
        $filter->text = $text;
        $filter->img = $img;
        $this->menu[] = $filter;
    }

    public function addText($text, $img = '')
    {
        $filter = new stdClass();
        $filter->text = $text;
        $filter->img = $img;
        $this->menu[] = $filter;
    }

    public function addSeparation()
    {
        $filter = new stdClass();
        $filter->separation = true;
        $this->menu[] = $filter;
    }
}

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

// Generic class for HTML code.
class HTML
{
    static function disabled($bool)
    {
        if ($bool === true) {
            echo 'disabled="disabled"';
        }
    }

    static function chked($bool)
    {
        if ($bool === true) {
            echo 'checked="checked"';
        }
    }

    static function selected($bool)
    {
        if ($bool === true) {
            echo 'selected="selected"';
        }
    }

    static function selectedCss($bool)
    {
        if ($bool === true) {
            echo ' selected';
        }
    }

    /**
    * @return string - online users
    */
    static function onlineUsers($total, $maxUsers)
    {
        if (is_string($maxUsers)) {
            if (! ctype_digit($maxUsers)) {
                $maxUsers = 0;
            } else {
                $maxUsers = (int)$maxUsers;
            }
        } elseif (is_int($maxUsers)) {
            if ($maxUsers < 0) {
                $maxUsers = 0;
            }
        } else {
            $maxUsers = 0;
        }
        $css = '';
        if ($total > 0) {
            if ($maxUsers === 0) {
                $css = 'unsafe';
            } elseif (($total * 100 / $maxUsers) <= 70) {
                $css = 'safe';
            } elseif (($total * 100 / $maxUsers) <= 90) {
                $css = 'warn';
            } else {
                $css = 'unsafe';
            }
        }
        return '<span class="onlineUsers"><strong class="'.$css.'">'.$total.'</strong> / '.$maxUsers.'</span>';
    }
}

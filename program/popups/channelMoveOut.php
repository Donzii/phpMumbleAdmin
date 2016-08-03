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

// Test scroll
// for ($i=100000; $i < 100150; ++$i) {
//     $module->onlineUsersList[$i] = new Murmur_User();
//     $module->onlineUsersList[$i]->name = 'name-'.$i.'        sdfdf <a  href=""       a>';
//     $module->onlineUsersList[$i]->session = $i;
//     $module->onlineUsersList[$i]->channel = $module->channelObj->id;
// }

$widget->scroll = array();

foreach ($module->onlineUsersList as $user) {
    // Show only users in the selected channel
    if ($user->channel === $module->channelObj->id) {
        $widget->scroll[] = $user;
    }
}

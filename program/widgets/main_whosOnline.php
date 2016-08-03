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

$widget->footerWhosOnline = array();

if ($PMA->user->isMinimum(PMA_USER_ROOTADMIN) && isset($PMA->whosOnline)) {

    $whosOnlineList = $PMA->whosOnline->getAllDatas();
    sortArrayBy($whosOnlineList, 'class');
    // Count unauthenticated users.
    $totalUnauth = 0;

    foreach ($whosOnlineList as $array) {
        if ($array['class'] === PMA_USER_UNAUTH) {
            ++$totalUnauth;
            continue;
        }

        $data = new stdClass();
        $data->css = $array['classname'];
        $data->title = $array['classname'];
        $data->login = $array['login'];

        if (is_int($array['uid'])) {
            // Show more informations for mumble users.
            $profileName = $PMA->profiles->getName($array['profile_id']);
            $data->title .= ' (profile '.$profileName.', server id #'.$array['sid'].')';
        }
        $widget->footerWhosOnline[] = $data;
    }

    if ($totalUnauth > 0) {
        $data = new stdClass();
        $data->css = 'unauth';
        $data->title = '';
        $data->login = sprintf($TEXT['total_unauth'], $totalUnauth);
        $widget->footerWhosOnline[] = $data;
    }
}

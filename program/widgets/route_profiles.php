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

$widget->profiles = array();

if ($PMA->cookie->userAcceptCookies()) {

    $profiles = array();
    foreach ($PMA->router->profile->getRoutesTable() as $id) {
        $profiles[] = $PMA->profiles->get($id);
    }
    sortArrayBy($profiles, 'name');

    foreach ($profiles as $profile) {
        $data = new stdClass();
        $data->css = '';
        $data->title = '';
        $data->id = $profile['id'];
        $data->name = $profile['name'];
        $data->isPublic = false;
        $data->isDisabled = false;
        $data->isDefault = false;

        if ($profile['id'] === $PMA->router->getRoute('profile')) {
            $data->css = ' selected';
        }
        if ($PMA->user->isMinimum(PMA_USER_ROOTADMIN)) {
            $data->title = $data->id.'#';
            if ($profile['id'] === $PMA->config->get('default_profile')) {
                $data->title .= '-Default';
                $data->isDefault = true;
            }
            if ($profile['public']) {
                $data->title .= ' *Public';
                $data->isPublic = true;
            }
        }
        $widget->profiles[] = $data;
    }
}

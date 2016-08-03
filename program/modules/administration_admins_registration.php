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

$PMA->widgets->newHiddenPopup('adminsRegistrationEditor');

/**
* Admin registration.
*/
$module->set('admID', $registration['id']);
$module->admClass = $registration['class'];
$module->set('admClassName', pmaGetClassName($registration['class']));
$module->set('admLogin', $registration['login']);
$module->set('admCreatedUptime', PMA_datesHelper::uptime(time() - $registration['created']));
$module->set('admCreatedDate', strftime($PMA->dateTimeFormat, $registration['created']));
$module->set('admName', $registration['name']);
if ($registration['last_conn'] > 0) {
    $module->lastConn = true;
    $module->set('lastConnUptime', PMA_datesHelper::uptime(time() - $registration['last_conn']));
    $module->set('lastConnDate', strftime($PMA->dateTimeFormat, $registration['last_conn']));
}
if ($registration['email'] !== '') {
    $module->email = true;
    $module->set('email', $registration['email']);
}
/**
* Admin access.
*/
$module->set('hasFullAccess', false);
$module->profilesAccess = array();
$module->serversScroll = array();
/**
* Profiles access.
*/
foreach ($registration['access'] as $profileID => $servers) {

    $nameEnc = htEnc($PMA->profiles->getName($profileID));

    if (hasFullAccess($servers)) {
        $text = sprintf(''.$TEXT['full_access'], $nameEnc);
    } else {
        $text = sprintf($TEXT['srv_access'], $nameEnc, count(strToArrayAccess($servers)));
    }

    $data = new stdClass();
    $data->selected = ($profileID === $PMA->router->getRoute('profile'));
    $data->textEnc = $text;

    $module->profilesAccess[] = $data;
}
/**
* Current profile access.
*/
if ($registration['class'] > PMA_USER_ROOTADMIN) {

    $module->showServersScroll = true;

    $pid = $PMA->router->getRoute('profile');

    $profileAccess = array();
    if (isset($registration['access'][$pid])) {
        $module->set('hasFullAccess', (hasFullAccess($registration['access'][$pid])));
        $profileAccess = strToArrayAccess($registration['access'][$pid]);
    }

    $cache = PMA_serversCacheHelper::get('normal');

    if (isset($cache['vservers'])) {
        foreach ($cache['vservers'] as $array) {
            $data = new stdClass();
            $data->id = $array['id'];
            $data->label = 's'.$data->id;
            $data->name = $array['name'];
            $data->chked = hasAccessToServer($data->id, $profileAccess);
            $module->serversScroll[] = $data;
        }
    }
}

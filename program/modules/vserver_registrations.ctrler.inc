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
* Check if a registered user is online.
*
* @return array
*/
function registered_is_online($uid, $getUsers)
{
    // By default, we assume that user is offline.
    $array['url'] = '';
    $array['txt'] = 'off';
    $array['status'] = 2;
    foreach ($getUsers as $obj) {
        if ($obj->userid === $uid) {
            $array['url'] = $obj->session.'-'.$obj->name;
            $array['txt'] = 'on';
            $array['status'] = 1;
            break;
        }
    }
    return $array;
}


/**
* Transforme an array of a mumble registration into an object.
*
* @return object
*/
function getMumbleRegistrationObj($array)
{
    $obj = new stdClass;
    // By default, this is not our own registration
    $obj->own_account = false;
    $obj->name = isset($array[0]) ? $array[0] : '';
    $obj->email = isset($array[1]) ? $array[1] : '';
    $obj->desc = isset($array[2]) ? $array[2] : '';
    $obj->cert = isset($array[3]) ? $array[3] : '';
    $obj->last_activity = isset($array[5]) ? $array[5] : '';
    return $obj;
}

if (! $module->vserverIsBooted) {
    throw new PMA_moduleException();
}

pmaLoadLanguage('vserver_registrations');

$getRegisteredUsers = $prx->getRegisteredUsers('');
$totalRegistered = count($getRegisteredUsers);
/**
* Remove SuperUser registration for SuperUser_ru.
*/
if ($PMA->user->is(PMA_USER_SUPERUSER_RU)) {
    unset($getRegisteredUsers[0]);
}

if ($module->showInfoPanel && $PMA->user->isMinimum(PMA_USER_SUPERUSER_RU)) {
    $PMA->skeleton->addInfoPanel(sprintf($TEXT['fill_registrations'], '<mark>'.$totalRegistered.'</mark>' ), 'occasional');
}

$MiscNav = $PMA->router->getMiscNavigation('mumbleRegistration');
// Mumble users must get their own registration
if ($PMA->user->is(PMA_USER_MUMBLE)) {
    $MiscNav = $PMA->user->mumbleUID;
}

if (is_int($MiscNav)) {
    if (! isset($getRegisteredUsers[$MiscNav])) {
        if ($PMA->user->is(PMA_USER_MUMBLE)) {
            $PMA->logout();
        } else {
            $PMA->router->removeMisc('mumbleRegistration');
        }
        $PMA->messageError('Murmur_InvalidUserException');
        $PMA->redirection();
    }

    $registration = getMumbleRegistrationObj($prx->getRegistration($MiscNav));
    //pdump($registration);
    $registration->id = $MiscNav;

    // Own account
    if ($PMA->user->mumbleUID === $registration->id) {
        $registration->own_account = true;
        pmaLoadLanguage('vserver_registrations_own');
    }
}

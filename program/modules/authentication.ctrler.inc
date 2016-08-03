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
* *******************************************
* Common controllers for authentications modules.
* *******************************************
*/

if ($PMA->cookie->userAcceptCookies()) {
    PMA_MurmurMetaHelper::connection();
}

$module->set('noCookie', ! $PMA->cookie->userAcceptCookies());

/**
* Enable the server field if we have a connection and
* if SuperUser or registered users are authorized to authenticate.
*/
$module->allowServerField = (
    $PMA->meta->isConnected() && ($PMA->config->get('SU_auth') OR $PMA->config->get('RU_auth'))
);
/**
* Enable password requests, if we allow the server field and
* if the option is enable.
*/
$module->allowPasswordRequests = ($PMA->config->get('pw_gen_active') && $module->allowServerField);
/**
* Check if we are in password request module.
*/
$module->set('isPwRequest', isset($_GET['password_request']));
/**
* Check if we are in password request confirmation module.
*/
$module->isPwRequestConfirmation = (isset($_GET['confirm_pw_request']));
/**
* Construct the servers dropdown list (ddl).
*/
if ($module->allowServerField && $PMA->config->get('ddl_auth_page') && ! $module->isPwRequestConfirmation) {

    $cache = PMA_serversCacheHelper::get('htEnc');

    if (isset($cache['vservers'])) {
        $module->vserversList = array();
        foreach ($cache['vservers'] as $array) {
            if ($array['access']) {
                $module->vserversList[] = $array;
            }
        }
    }
}

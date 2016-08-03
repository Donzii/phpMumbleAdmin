<?php

 /**
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
* Password request - Force the profile of the request id.
*/
if (isset($_GET['confirm_pw_request']) && $PMA->user->is(PMA_USER_UNAUTH)) {
    $PMA->pwRequests = new PMA_datas_pwRequests();
    $request = $PMA->pwRequests->get($_GET['confirm_pw_request']);
    if (! is_null($request)) {
        $PMA->pwRequests->requestFound = $request;
        $PMA->router->profile->addRoute($request['profile_id']);
    }
} else {
    if (isset($_GET['confirm_pw_request'])) {
        $PMA->messageError('gen_pw_authenticated');
    }
    foreach ($PMA->profiles->getAllDatas() as $profile) {
        if ($PMA->user->checkProfileAccess($profile['id'], $profile['public'])) {
            $PMA->router->profile->addRoute($profile['id']);
        }
    }
}
$PMA->router->profile->setDefaultRoute($PMA->cookie->get('profile_id'));
$PMA->router->checkNavigation('profile');
/**
* sanity
*/
if ($PMA->router->profile->isNewUserRoute()) {
    /**
    * Remove this ?
    */
    unset($_SESSION['page_vserver']);
    if ($PMA->router->getRoute('page') === 'vserver') {
        $PMA->redirection('?page=overview');
    }
}
/**
* Update user profile.
*/
$PMA->user->setProfileID($PMA->router->getRoute('profile'));
$PMA->userProfile = $PMA->profiles->get($PMA->router->getRoute('profile'));
$PMA->cookie->set('profile_id', $PMA->router->getRoute('profile'));


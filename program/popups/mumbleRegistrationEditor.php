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

$allowModifyLogin = (
    $PMA->user->isMinimum(PMA_USER_SUPERUSER_RU) OR
    $PMA->config->get('RU_edit_login')
);
$allowModifyPassword = (
    $PMA->user->isMinimum(PMA_USER_ADMIN) OR
    $PMA->config->get('SU_edit_user_pw') OR
    $registration->own_account
);

if ($allowModifyLogin) {
    $widget->set('login', $registration->name);
}
if ($allowModifyPassword) {
    $widget->set('pw', true);
    $widget->ownAccount = $registration->own_account;
}
$widget->set('email', $registration->email);
$widget->set('description', $registration->desc);
$widget->certificat = $registration->cert;

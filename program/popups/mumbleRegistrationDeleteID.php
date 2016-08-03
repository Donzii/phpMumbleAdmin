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

if (! isset($_GET['deleteMumbleAccountID'])) {
    $widget->set('id', '%d');
    $widget->set('name', '%s');
} else {
    if (! ctype_digit($_GET['deleteMumbleAccountID']) OR $_GET['deleteMumbleAccountID'] < 1) {
        $PMA->messageError('illegal_operation');
        throw new PMA_moduleException();
    }

    if (! isset($getRegisteredUsers[$_GET['deleteMumbleAccountID']])) {
        $PMA->messageError('Murmur_InvalidUserException');
        throw new PMA_moduleException();
    }

    $widget->set('id', $_GET['deleteMumbleAccountID']);
    $widget->set('name', $getRegisteredUsers[$_GET['deleteMumbleAccountID']]);
}

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

if (! isset($getBans[$_GET['edit_ban_id']])) {
    throw new PMA_moduleException();
}

$BAN = $getBans[$_GET['edit_ban_id']];
$ip = PMA_ipHelper::decimalTostring($BAN->address);
if ($ip['type'] === 'ipv4') {
    $mask = PMA_ipHelper::mask6To4($BAN->bits);
} else {
    $mask = $BAN->bits;
}

$module->set('banID', (int)$_GET['edit_ban_id']);
$module->set('ip', $ip['ip']);
$module->set('mask', $mask);
$module->set('login', $BAN->name);
$module->set('reason', $BAN->reason);
if ($BAN->hash !== '') {
    $module->set('hash', $BAN->hash);
}
$module->set('start', strftime($PMA->dateTimeFormat, $BAN->start));

if ($BAN->duration !== 0) {
    $module->set('end', strftime($PMA->dateTimeFormat, ($BAN->start + $BAN->duration)));
    $module->endTimeStamp = ($BAN->start + $BAN->duration); // selectBanDuration widget
    $module->permanent = false;
} else {
    $module->permanent = true;
}

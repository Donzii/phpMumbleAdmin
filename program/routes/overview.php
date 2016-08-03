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

$PMA->modules->addController('overview');

if (isset($_GET['confirmStopSrv'])) {
    $PMA->modules->enable('confirmStopServer');
}elseif (isset($_GET['addServer'])) {
    $PMA->widgets->newPopup('serverAdd');
} elseif (isset($_GET['messageToServers'])) {
    $PMA->widgets->newPopup('serversMessage');
} elseif (isset($_GET['deleteServer'])) {
    $PMA->widgets->newPopup('serverDelete');
} elseif (isset($_GET['resetServer'])) {
    $PMA->widgets->newPopup('serverReset');
} elseif (isset($_GET['murmurInformations'])) {
    $PMA->modules->enable('overview_murmurInformations');
} elseif (isset($_GET['murmurMassSettings'])) {
    $PMA->modules->enable('overview_murmurMassSettings');
} else {
    $PMA->modules->enable('overview_table');
}

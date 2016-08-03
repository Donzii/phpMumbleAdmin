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
* LOGS FILTERS
* Common for the view and the commande
*/
$logsfilters[] = array('mask' => 1, 'pattern' => 'New connection');
$logsfilters[] = array('mask' => 2, 'pattern' => 'Authenticated');
$logsfilters[] = array('mask' => 4, 'pattern' => 'Connection closed');
$logsfilters[] = array('mask' => 1024, 'pattern' => 'Has left the server');
$logsfilters[] = array('mask' => 8, 'pattern' => 'Changed speak-state');
$logsfilters[] = array('mask' => 16, 'pattern' => 'Moved to channel');
$logsfilters[] = array('mask' => 32, 'pattern' => 'Voice thread');
$logsfilters[] = array('mask' => 64, 'pattern' => 'Crypt-nonce resync');
$logsfilters[] = array('mask' => 128, 'pattern' => 'CELT codec');
$logsfilters[] = array('mask' => 256, 'pattern' => 'Client version');
$logsfilters[] = array('mask' => 512, 'pattern' => 'Not allowed to');

<?php

 /*
 *    phpMumbleAdmin (PMA), web php administration tool for murmur ( mumble server daemon ).
 *    Copyright (C) 2010 - 2013  Dadon David. PMA@ipnoz.net
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if ( ! defined( 'PMA_STARTED' ) ) { die( 'ILLEGAL: You cannot call this script directly !' ); }

$vlogs = array();
$vlogs['filters_actived'] = bitmask_decompose( PMA_cookie::instance()->get( 'logsFilters' ) );
$vlogs['allow_highlight'] = PMA_cookie::instance()->get( 'highlight_logs' );

// Never let SuperUsers highlight logs if SuperAdmins don't authorise it.
if ( ! PMA_config::instance()->get( 'vlogs_admins_highlights' ) && PMA_user::instance()->is_in( LOW_LVL_ADMINS ) ) {
	$vlogs['allow_highlight'] = FALSE;
}

/**
* LOGS FILTERS
*
* key: filter bitmask
* value: string to match and filter text menu ( need to be short ).
*
*/
$vlogs['filters'][1] = 'New connection';
$vlogs['filters'][2] = 'Authenticated';
$vlogs['filters'][4] = 'Connection closed';
if ( $vlogs['allow_highlight'] ) {
	$vlogs['filters'][1024] = 'Has left the server';
}
$vlogs['filters'][8] = 'Changed speak-state';
$vlogs['filters'][16] = 'Moved to channel';
$vlogs['filters'][32] = 'Voice thread';
$vlogs['filters'][64] = 'Crypt-nonce resync';
$vlogs['filters'][128] = 'CELT codec';
$vlogs['filters'][256] = 'Client version';
$vlogs['filters'][512] = 'Not allowed to';



?>

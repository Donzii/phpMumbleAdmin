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

if ( $PMA->user->is_min( CLASS_SUPERUSER_RU ) ) {

	$array[] = 'channels';
	$array[] = 'settings';
	$array[] = 'settings_more';
	$array[] = 'registrations';
	$array[] = 'bans';

	// Check if logs tab is activated ( always active for SuperAdmins ).
	if ( $PMA->user->is_min( CLASS_ROOTADMIN ) OR $PMA->config->get( 'vlogs_admins_active' ) ) {
		$array[] = 'logs';
	}

} else {

	$array[] = 'channels';
	$array[] = 'registrations';
}

$PMA->tabs = new PMA_controler_tabs( 'vserver', 'channels', $array );

?>
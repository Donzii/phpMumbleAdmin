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

if ( isset( $_GET['action'] ) ) {

	switch( $_GET['action'] ) {

		case 'add_vserver':
		case 'delete_vserver':
		case 'send_msg_vservers':
		case 'reset_vserver':
		case 'murmur_conf':
		case 'mass_settings':

			require 'actions/'.$_GET['action'].'.php';
	}

} elseif ( isset( $_GET['confirm_stop_sid'] ) ) {

	require 'actions/confirm_stop_vserver.php';

} else {

	// Default: vservers overview table
	require 'views/toolbar.php';
	require 'views/vservers_table.php';
}

?>
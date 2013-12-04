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

function save_message( $key, $message ) {
	PMA::instance()->messages[ $key ][] = $message;
}

/**
* Add a message box.
*/
function msg_box( $key, $type = 'error', $options = NULL ) {

	switch( $type ) {

		case 'success':
		case 'error':
		case 'fatal':
			$type = $type;
			break;

		default:
			$type = 'error';
			break;
	}

	save_message( 'box', array( 'key' => $key, 'type' => $type, 'options' => $options ) );
}

/**
* Add an alert error message box.
*/
function msg_alert( $msg ) {
	save_message( 'alert', $msg );
}

/**
* Add a debug message
*/
function msg_debug( $msg, $level = 1, $error = FALSE ) {

	if ( ! defined( 'PMA_DEBUG' ) OR PMA_DEBUG >= $level ) {

		$cmd = ( PMA_MODE === 'cmd' );

		save_message( 'debug', array( 'level' => $level, 'error' => $error, 'cmd' => $cmd, 'msg' => $msg ) );
	}
}

/**
* Add an Ice fatal error mesage for SuperAdmins
*/
function msg_ice_fatal_error( $title_key, $help_key, $options = '' ) {

	$options .= ', title='.$title_key.', ice_error, nobutton';

	save_message( 'ice_error', array( 'key' => $help_key, 'type' => 'error', 'options' => $options ) );
}

?>
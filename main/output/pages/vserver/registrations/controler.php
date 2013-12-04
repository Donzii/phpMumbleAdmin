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

/**
* @function registered_is_online
*
* Check if a registered user is online.
*
* @return array
*/
function registered_is_online( $uid, $getUsers ) {

	// By default, we assume that user is offline.
	$array['url'] = '';
	$array['txt'] = 'off';
	$array['status'] = 2;

	foreach ( $getUsers as $obj ) {

		if ( $obj->userid === $uid ) {

			$array['url'] = $obj->session.'-'.$obj->name;
			$array['txt'] = 'on';
			$array['status'] = 1;
			break;
		}
	}

	return $array;
}


/**
* Transforme an array of a mumble registration to an object.
*
* @return object
*/
function mumble_registration( $array ) {

	$obj = new stdClass;

	// By default, this is not our own registration
	$obj->own_account = FALSE;

	if ( isset( $array[0] ) ) {
		$obj->name = $array[0];
	} else {
		$obj->name = '';
	}

	if ( isset( $array[1] ) ) {
		$obj->email = $array[1];
	} else {
		$obj->email = '';
	}

	if ( isset( $array[2] ) ) {
		$obj->desc = $array[2];
	} else {
		$obj->desc = '';
	}

	if ( isset( $array[3] ) ) {
		$obj->cert = $array[3];
	} else {
		$obj->cert = '';
	}

	if ( isset( $array[5] ) ) {
		$obj->last_activity = $array[5];
	} else {
		$obj->last_activity = '';
	}

	return $obj;
}

pma_load_language( 'vserver_registrations' );

$JS->add_text( 'add_acc', $TEXT['add_acc'] );
$JS->add_text( 'redirect_to_new_acc', $TEXT['add_acc_redirect'] );
$JS->add_text( 'confirm_del_acc', $TEXT['confirm_delete_acc'] );

// Don't keep registration_id in session when we change vserver tab.
if ( isset( $_GET['tab'] ) ) {
	unset( $_SESSION['page_vserver']['registration_id'] );
}

// Change REGISTRATION ID
if ( isset( $_GET['registration_id'] ) && ctype_digit( $_GET['registration_id'] ) ) {
	$_SESSION['page_vserver']['registration_id'] = (int)$_GET['registration_id'];
}

// User must to get their own registration
if ( $PMA->user->is( CLASS_USER ) ) {
	$_SESSION['page_vserver']['registration_id'] = $PMA->user->mumble_id;
}

// ROUTE
if ( isset( $_SESSION['page_vserver']['registration_id'] ) ) {

	require 'controler_registration.php';

} elseif ( isset( $_GET['add_new_account'] ) ) {

	require 'actions/add.php';

} elseif ( isset( $_GET['delete_account_id'] ) ) {

	require 'actions/delete_id.php';

} else {
	require 'views/table.php';
}


?>

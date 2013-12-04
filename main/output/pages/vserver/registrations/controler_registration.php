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

if ( ! isset( $getRegisteredUsers[ $_SESSION['page_vserver']['registration_id'] ] ) ) {

	if ( $PMA->user->is( CLASS_USER ) ) {

		pma_logout();

	} else {
		unset( $_SESSION['page_vserver']['registration_id'] );
	}

	msg_box( 'InvalidUserException', 'error' );
	pma_redirect();
}

// SU_ru can't edit SuperUser account
if ( $PMA->user->is( CLASS_SUPERUSER_RU ) && $_SESSION['page_vserver']['registration_id'] === 0 ) {

	unset( $_SESSION['page_vserver']['registration_id'] );

	pma_illegal_operation();
}

$registration = mumble_registration( $getServer->getRegistration( $_SESSION['page_vserver']['registration_id'] ) );

$registration->id = $_SESSION['page_vserver']['registration_id'];

$registration->name = html_encode( $registration->name );

if ( $registration->desc === '' ) {

	$registration->desc = '<div class="empty">'.$TEXT['no_comment'].'</div>';
	$desc_textarea = '';

} else {
	$desc_textarea = html_encode( $registration->desc, FALSE );
}

// Own account
if ( $PMA->user->mumble_id !== NULL && $PMA->user->mumble_id === $registration->id ) {

	$registration->own_account = TRUE;
	pma_load_language( 'vserver_registrations_own' );
}

$JS->add_text( 'confirm_del_acc', $TEXT['confirm_delete_acc'] );
$JS->add_text( 'modify_login', $TEXT['modify_login'] );
$JS->add_text( 'modify_email', $TEXT['modify_email'] );
$JS->add_text( 'modify_comm', $TEXT['modify_comm'] );
$JS->add_text( 'confirm_del_avatar', $TEXT['confirm_delete_avatar'] );

if ( isset( $_GET['delete_account'] ) ) {

	require 'actions/delete_account.php';

} elseif ( isset( $_GET['change_login'] ) ) {

	require 'actions/change_login.php';

} elseif ( isset( $_GET['change_password'] ) ) {

	require 'actions/change_password.php';

} elseif ( isset( $_GET['change_email'] ) ) {

	require 'actions/change_email.php';

} elseif ( isset( $_GET['change_desc'] ) ) {

	require 'actions/change_comment.php';

} elseif ( isset( $_GET['remove_avatar'] ) ) {

	require 'actions/delete_avatar.php';

} else {

	require 'methods/avatar.php';
	require 'views/registration.php';
}

?>

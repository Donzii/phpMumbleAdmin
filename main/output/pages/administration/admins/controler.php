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

// Return the select field for add and edit registration.
function classes_selection( $autoselect = CLASS_ADMIN ) {

	$user = PMA_user::instance();

	// Define available admins classes
	if ( $user->is_superior( CLASS_ROOTADMIN ) ) {
		$classes[] = CLASS_ROOTADMIN;
	}

	if ( $user->is_superior( CLASS_ADMIN ) ) {
		$classes[] = CLASS_ADMIN;
	}

	if ( count( $classes ) < 2 ) {
		return;
	}

	global $TEXT;

	$output = '<th>'.$TEXT['class'].'</th>';
	$output .= '<td><select name="class">';

	foreach( $classes as $class ) {


		if ( $class === $autoselect ) {
			$output .= '<option value="'.$class.'" selected="selected">'.pma_class_name( $class ).'</option>';
		} else {
			$output .= '<option value="'.$class.'">'.pma_class_name( $class ).'</option>';
		}
	}

	$output .= '</select></td>';

	return $output;
}

$JS->add_text( 'del_admin', $TEXT['confirm_del_admin'] );

$PMA->admins = PMA_admins::instance();

if ( isset( $_GET['tab'] ) ) {

	unset( $_SESSION['page_administration']['adm_id'] );
}

if ( isset( $_GET['admin'] ) ) {

	$_SESSION['page_administration']['adm_id'] = $_GET['admin'];
}


// ROUTE
if ( isset( $_GET['add_admin'] ) ) {

	require 'actions/add.php';

} elseif ( isset( $_GET['remove_admin'] ) ) {

	require 'actions/delete.php';

} elseif ( isset( $_SESSION['page_administration']['adm_id'] ) ) {

	$registration = $PMA->admins->get( $_SESSION['page_administration']['adm_id'] );

	if ( $registration === NULL OR ! $PMA->user->is_superior( $registration['class'] ) ) {

		unset( $_SESSION['page_administration']['adm_id'] );

		pma_redirect();
	}

	if ( isset( $_GET['edit_registration'] ) ) {

		require 'actions/edit_registration.php';

	} else {

		require 'views/edit_access.php';
	}

} else {

	// Default : admins table
	require 'views/table.php';
}


?>
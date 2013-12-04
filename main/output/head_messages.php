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

pma_load_language( 'messages' );

if ( isset( $PMA->messages['ice_error'] ) ) {

	if ( ! $PMA->user->is_min( CLASS_ROOTADMIN ) ) {

		// Common ICE error message for all other users
		msg_box( 'ice_help_unauth', 'error', 'nobutton' );

	} else {

		// Print only the first Ice fatal error message.
		$box = new PMA_output_messageBox( $PMA->messages['ice_error'][0] );
		echo $box->get_cache();
	}
}

if ( isset( $PMA->messages['box'] ) ) {

	foreach( $PMA->messages['box'] as $array ) {

		$box = new PMA_output_messageBox( $array );
		echo $box->get_cache();
	}
}

if ( isset( $PMA->messages['alert'] ) ) {

	echo '<div class="oBox debug" style="margin: 10px 0px;">';

	foreach( $PMA->messages['alert'] as $msg ) {

		if ( isset( $TEXT[ $msg ] ) ) {
			$msg = $TEXT[ $msg ];
		}

		echo '<div><span style="font-weight: bold; color: red;">ALERT: </span>'.$msg.'</div>';
	}

	echo '</div>'.EOL.EOL;
}

?>
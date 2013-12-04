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

if ( ! COOKIE_ACCEPTED ) {
	return;
}

function profiles_menu( $avalaibles ) {

	$default = PMA_config::instance()->get( 'default_profile' );

	$current = PMA_user::instance()->profile_id;

	$is_superadmin = PMA_user::instance()->is_min( CLASS_ROOTADMIN );

	$output = '';


	$list = array();

	foreach( $avalaibles as $id ) {
		$list[] = PMA_profiles::instance()->get( $id );
	}

	sort_array_by( $list, 'name' );

	foreach( $list as $profile ) {

		$name = html_encode( $profile['name'] );

		if ( $profile['id'] === $current ) {
			$selected = 'class="selected"';
		} else {
			$selected = '';
		}

		if ( $is_superadmin ) {

			// Public img
			if ( $profile['public'] === TRUE ) {
				$name = HTML::img( 'xchat/blue.png', 'left pub' ).$name;
			}

			// Disabled img
			if ( isset( $profile['invalid_slice_file'] ) ) {
				$name = HTML::img( 'xchat/red_16.png', 'left pub' ).$name;
			}

			// Default profile
			if ( $profile['id'] === $default ) {
				$name .= ' +';
			}
		}

		$output .= '<li><a '.$selected.' href="?profile='.$profile['id'].'">'.$name.'</a></li>'.EOL;
	}

	return $output;
}

if ( count( $PMA->user->profiles_avalaible ) > 1 ) {
	echo '<ul id="profiles">'.EOL;
	echo profiles_menu( $PMA->user->profiles_avalaible );
	echo '</ul><!-- profiles - END -->'.EOL.EOL;
}

?>

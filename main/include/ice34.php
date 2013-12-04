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

msg_debug( ' => Ice 3.4 workaround' );

/**
* Ice.php ( and slice definitions obtain with slice2php ) need to be loaded in the global scope.
* See: http://www.zeroc.com/forums/help-center/5200-no-object-found-icephp_definestruct.html
*/

$workaround_ice34_inc_IcePhp = @include( 'Ice.php' );

if ( $workaround_ice34_inc_IcePhp === 1 ) {

	if ( PMA_MODE === 'ext_viewer' ) {
		$profile = PMA_profiles::instance()->get( $pid );
	} else {
		$profile = $PMA->user->get_profile();
	}

	$file = 'slice_php/'.$profile['slice_php'];

	if ( is_file( $file ) && is_readable( $file ) ) {
		require $file;
		$workaround_ice34_inc_slice_file = TRUE;
	} else {
		$workaround_ice34_inc_slice_file = FALSE;
	}
}

?>
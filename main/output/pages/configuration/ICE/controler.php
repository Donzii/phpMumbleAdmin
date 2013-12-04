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

$JS->add_text( 'add_ice_profile', $TEXT['add_ICE_profile'] );
$JS->add_text( 'del_ice_profile', $TEXT['confirm_del_ICE_profile'] );
$JS->add_text( 'invalid_timeout', sprintf( $TEXT['invalid_numerical'], 'timeout > 0' ) );

$profile = $PMA->user->get_profile();

if ( isset( $_GET['delete_profile'] ) ) {

	require 'actions/delete.php';

} elseif ( isset( $_GET['add_profile'] ) ) {

	require 'actions/add.php';

} else {

	require 'views/profile.php';
}

?>
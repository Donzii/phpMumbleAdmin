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

// 946684799 = 23H59:59 - 31-12-1999 - GMT / UTC
$ts = 946684799;

// Shared options
$time_options[] = array( 'options' => 'h:i:s A', 'desc' => gmdate( 'h:i:s A', $ts ) );
$time_options[] = array( 'options' => 'h:i A', 'desc' => gmdate( 'h:i A', $ts ) );
$time_options[] = array( 'options' => 'H:i:s', 'desc' => gmdate( 'H:i:s', $ts ) );
$time_options[] = array( 'options' => 'H:i', 'desc' => gmdate( 'H:i', $ts ) );

$date_options[] = array( 'options' => '%d %b %Y', 'desc' => gmstrftime( '%d %b %Y', $ts ) );
$date_options[] = array( 'options' => '%d %B %Y', 'desc' => gmstrftime( '%d %B %Y', $ts ) );
$date_options[] = array( 'options' => '%m-%d-%Y', 'desc' => gmstrftime( '%m-%d-%Y', $ts ) );
$date_options[] = array( 'options' => '%d-%m-%Y', 'desc' => gmstrftime( '%d-%m-%Y', $ts ) );
$date_options[] = array( 'options' => '%Y-%m-%d', 'desc' => gmstrftime( '%Y-%m-%d', $ts ) );
$date_options[] = array( 'options' => '%Y-%d-%m', 'desc' => gmstrftime( '%Y-%d-%m', $ts ) );

if ( isset( $_GET['set_default_options'] ) ) {

	require 'views/set_defaults.php';

} elseif ( isset( $_GET['edit_SuperAdmin'] ) ) {

	require 'views/superadmin.php';

} elseif ( isset( $_GET['change_your_password'] ) ) {

	require 'views/change_password.php';

} else {

	require 'views/user_options.php';
}

?>
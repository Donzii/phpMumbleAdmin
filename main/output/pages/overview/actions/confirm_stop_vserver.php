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

if ( ! ctype_digit( $_GET['confirm_stop_sid'] ) ) {
	pma_illegal_operation();
}

$sid = (int)$_GET['confirm_stop_sid'];

// Check current admin rights for the virtual server
if ( $PMA->user->is( CLASS_ADMIN ) ) {

	if ( ! $PMA->user->check_admin_sid( $sid ) ) {
		pma_illegal_operation();
	}
}

$actionBox = new PMA_output_actionBox();

$actionBox->set_conf( 'css', 'alert small' );

$actionBox->form();
$actionBox->cancel();
$actionBox->input( 'hidden', 'cmd', 'overview' );
$actionBox->input( 'hidden', 'confirm_stop_sid', $sid );
$actionBox->confirm_stop_sid();
$actionBox->close();

echo $actionBox->output;

?>
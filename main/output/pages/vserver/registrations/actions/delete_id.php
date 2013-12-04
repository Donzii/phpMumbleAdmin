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

if ( ! ctype_digit( $_GET['delete_account_id'] ) OR $_GET['delete_account_id'] < 1 ) {
	pma_illegal_operation();
}

$name = $getRegisteredUsers[ $_GET['delete_account_id'] ];

$actionBox = new PMA_output_actionBox();

$actionBox->set_conf( 'css', 'alert small' );

$actionBox->form();
$actionBox->input( 'hidden', 'cmd', 'murmur_registrations' );
$actionBox->input( 'hidden', 'delete_account_id', $_GET['delete_account_id'] );
$actionBox->confirm( $name, $TEXT['confirm_delete_acc'] );
$actionBox->submit( 'confirm' );
$actionBox->close();

echo $actionBox->output;


?>

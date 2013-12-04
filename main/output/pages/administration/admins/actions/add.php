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

$asterix = '<span style="color: red;">*</span>';

$classes_selection = classes_selection();

$actionBox = new PMA_output_actionBox();

$actionBox->set_conf( 'onSubmit', 'return validate_add_admin( this );' );
$actionBox->set_conf( 'submit_txt', $TEXT['add'] );

$actionBox->cancel( 'toolbox' );
$actionBox->form();
$actionBox->input( 'hidden', 'cmd', 'config_admins' );
$actionBox->input( 'hidden', 'add_new_admin', '' );
$actionBox->table();
$actionBox->tr_title( $TEXT['add_admin'] );
$actionBox->tr( 'text', $TEXT['login'].$asterix, 'login', '' );
$actionBox->tr_pad();
$actionBox->tr( 'password', $TEXT['new_pw'].$asterix, 'new_pw', '' );
$actionBox->tr( 'password', $TEXT['confirm_pw'].$asterix, 'confirm_new_pw', '' );
$actionBox->tr_pad();

if ( $classes_selection !== NULL ) {

	$actionBox->tr_custom( $classes_selection );
	$actionBox->tr_pad();
} else {
	$actionBox->input( 'hidden', 'class', CLASS_ADMIN );
}

$actionBox->tr( 'text', $TEXT['email_addr'], 'email', '' );
$actionBox->tr( 'text', $TEXT['user_name'], 'name', '' );
$actionBox->submit( 'table' );
$actionBox->close( 'table' );
$actionBox->close();

echo $actionBox->output;

?>
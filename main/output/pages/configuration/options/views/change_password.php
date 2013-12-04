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


if ( $PMA->user->admin_id === NULL ) {
	pma_illegal_operation();
}

$actionBox = new PMA_output_actionBox();

$actionBox->set_conf( 'onSubmit', 'return validate_pw( this );' );
$actionBox->set_conf( 'submit_txt', $TEXT['modify'] );

$actionBox->cancel( 'toolbox' );
$actionBox->form();
$actionBox->input( 'hidden', 'cmd', 'config_admins' );
$actionBox->input( 'hidden', 'change_own_pw', '' );
$actionBox->table();
$actionBox->tr_title( $TEXT['change_your_pw'] );
$actionBox->tr( 'password', $TEXT['enter_your_pw'], 'current', '' );
$actionBox->tr_pad();
$actionBox->tr( 'password', $TEXT['new_pw'], 'new_pw', '' );
$actionBox->tr( 'password', $TEXT['confirm_pw'], 'confirm_new_pw', '' );
$actionBox->submit( 'table' );
$actionBox->close( 'table' );
$actionBox->close();

echo $actionBox->output;

?>
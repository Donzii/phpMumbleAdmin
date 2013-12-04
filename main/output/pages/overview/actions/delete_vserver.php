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

if ( ! $PMA->user->is_min( CLASS_ADMIN_FULL_ACCESS ) OR ! ctype_digit( $_GET['id'] ) ) {
	pma_illegal_operation();
}

$sid = (int)$_GET['id'];

if ( NULL === $server = $PMA->meta->getServer( $sid ) ) {
	return;
}

$name = $server->get_conf( 'registername' );

$actionBox = new PMA_output_actionBox();

$actionBox->set_conf( 'css', 'alert small' );

$actionBox->form();
$actionBox->input( 'hidden', 'cmd', 'overview' );
$actionBox->input( 'hidden', 'delete_vserver_id', $sid );
$actionBox->confirm( $name, sprintf( $TEXT['confirm_del_server'], $sid ) );
$actionBox->submit( 'confirm' );
$actionBox->close();

echo $actionBox->output;

?>
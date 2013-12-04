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

pma_load_language( 'vserver_bans' );

$JS->add_text( 'del_ban', $TEXT['confirm_del_ban'] );
$JS->add_text( 'invalid_mask', $TEXT['invalid_mask'] );

if ( isset( $_GET['addBan'] ) ) {

	require 'actions/add.php';

} elseif ( isset( $_GET['edit_ban_id'] ) ) {

	require 'actions/edit.php';

} elseif ( isset( $_GET['delete_ban_id'] ) ) {

	require 'actions/delete.php';

} else {

	require 'views/table.php';
}

?>

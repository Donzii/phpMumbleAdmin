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

$OUTPUT->info_panel = new PMA_output_info_panel();

$OUTPUT->info_panel->add_fill( $murmur );

$OUTPUT->info_panel->add_fill( sprintf( $TEXT['total_srv'], '<b class="safe">'.count( $lists['booted'] ).'</b> / <b>'.count( $lists['vservers'] ).'</b>' ) );

if ( $PMA->config->get( 'show_total_users' ) && ( $PMA->user->is_min( CLASS_ROOTADMIN ) OR ! $PMA->config->get( 'show_total_users_sa' ) ) ) {
	$OUTPUT->info_panel->add_fill( sprintf( $TEXT['total_users'], '<b class="safe">'.$PMA->meta->count_all_users().'</b>' ) );
}

if ( $PMA->user->is_min( CLASS_ROOTADMIN ) && $PMA->updates->exists() ) {
	$OUTPUT->info_panel->add_fill( $PMA->updates->fill(), 'right' );
}

?>

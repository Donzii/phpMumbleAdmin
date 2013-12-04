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

$OUTPUT->box = 'box small';

// Do we have to disable the server field?
function disable_server_field() {

	$config = PMA_config::instance();

	$disable = ( ! $config->get( 'SU_auth' ) && ! $config->get( 'RU_auth' ) );

	if ( ! COOKIE_ACCEPTED OR ! pma_ice_conn_is_valid() ) {
		$disable = TRUE;
	}

	return $disable;
}

// Server field output
function server_field() {

	$FIELD['disabled'] = '<input type="text" id="server" disabled="disabled" name="server_id" maxlength="6" style="width: 50px">';
	$FIELD['input'] = '<input type="text" id="server" name="server_id" maxlength="6" style="width: 50px">';

	if ( disable_server_field() ) {
		return $FIELD['disabled'];
	}

	if ( ! PMA_config::instance()->get( 'ddl_auth_page' ) ) {
		return $FIELD['input'];
	}

	$cache = PMA_vservers_cache::instance()->get_current();

	if ( ! isset( $cache['vservers'] ) ) {
		return $FIELD['input'];
	}

	global $TEXT;

	// Dropdown list server field
	$output = '<select id="server" name="server_id">';
	$output .= '<option value="">'.$TEXT['select_server'].'</option>';

	foreach( $cache['vservers'] as $array ) {

		if ( $array['access'] ) {
			$output .= '<option value="'.$array['id'].'">'.$array['id'].'# '.$array['name'].'</option>';
		}
	}

	$output .= '</select>';

	return $output;
}

if ( COOKIE_ACCEPTED ) {
	$PMA->meta = PMA_meta::instance( $PMA->user->get_profile() );
}

// Do we allow pw request ?
$allow_pw_request = ( $PMA->config->get( 'pw_gen_active' ) && ! disable_server_field() && check_file( PMA_FILE_PW_REQUEST ) );

require 'route.php';

?>
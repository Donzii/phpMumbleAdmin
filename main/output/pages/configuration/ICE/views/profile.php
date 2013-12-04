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


function select_slice_profiles( $datas, $profile ) {

	global $TEXT;

	$output = '<tr><th>'.$TEXT['slice_profile'].'</th>';
	$output .= '<td><select name="slice_profile">';
	$output .= '<option value="">'.$TEXT['none'].'</option>';

	foreach( $datas as $name => $array ) {

		if ( ! isset( $array['ice.slice'] ) ) {
			continue;
		}

		if ( $profile['slice_profile'] === $name ) {
			$output .= '<option selected="selected" value="'.$name.'">'.$name.'</option>';
		} else {
			$output .= '<option value="'.$name.'">'.$name.'</option>';
		}
	}

	$output .= '</select></td></tr>'.EOL;

	return $output;
}

function select_slice_php( $profile ) {

	global $TEXT;

	$scan = scan_dir( 'slice_php/' );

	$output = '<tr><th>'.$TEXT['slice_php_file'].'</th>';
	$output .= '<td><select name="slice_php">';
	$output .= '<option value="">'.$TEXT['none'].'</option>';

	foreach( $scan as $filename ) {

		if ( substr( $filename, -4 ) === '.php' ) {

			$name = substr( $filename, 0, -4 );

			if ( $profile['slice_php'] === $filename ) {
				$output .= '<option selected="selected" value="'.$filename.'">'.$name.'</option>';
			} else {
				$output .= '<option value="'.$filename.'">'.$name.'</option>';
			}
		}
	}

	$output .= '</select></td></tr>'.EOL;

	return $output;
}

$PMA->meta = PMA_meta::instance( $profile );

// get icePHP infos
if ( PMA_ICE_INT > 30400 ) {

	$icePHP['slice_file'] = '<span class="unsafe b">Obsolete</span>';
	$icePHP['profiles_file'] = '<span class="unsafe b">Obsolete</span>';
} else {

	$icePHP['slice_file'] = ini_get( 'ice.slice' );
	$icePHP['profiles_file'] = ini_get( 'ice.profiles' );

	// icePHP 3.2 / 3.3 - Setup $icePHP['profiles_list'] only if web master have activated slice profiles.
	if ( $icePHP['profiles_file'] !== '' && $icePHP['profiles_file'] !== FALSE ) {

		if ( is_readable( $icePHP['profiles_file'] ) ) {

			$icePHP['profiles_list'] = parse_ini_file( $icePHP['profiles_file'], TRUE );

		} else {

			msg_alert( 'PMA has detected that slices profiles are activated but cannot read <b>"'.$icePHP['profiles_file'].'"</b>. Failed to get slice profile list.' );

			$icePHP['profiles_list'] = array();
		}

		if ( PMA_DEBUG > 0 ) {

			// Add an invalid slice profile for debuging
			$icePHP['profiles_list']['DEBUG_INVALID_PROFILE'] = array( 'ice.slice' => '' );
		}
	}
}

// ToolBar
echo '<div class="toolbar">'.EOL;
echo '<a href="?add_profile" title="'.$TEXT['add_ICE_profile'].'" onClick="return add_ice_profile();">'.HTML::img( IMG_ADD_22, 'button' ).'</a>'.EOL;
// Set default profile
if (
	pma_ice_conn_is_valid()
	&& $profile['public'] === TRUE
	&& $profile['id'] !== $PMA->config->get( 'default_profile' )
) {
	echo '<a href="?cmd=config_ICE&amp;set_default_profile" title="'.$TEXT['default_ICE_profile'].'">';
	echo HTML::img( 'tango/fav_22.png', 'button' ).'</a>'.EOL;
}
// Re-enable profile
if ( isset( $profile['invalid_slice_file'] ) ) {
	echo '<a href="?cmd=config_ICE&amp;enable_profile" title="'.$TEXT['enable_profile'].'">'.HTML::img( 'xchat/red_22.png', 'button' ).'</a> <== '.$TEXT['enable_profile'].EOL;
}
echo '</div>'.EOL.EOL;

if ( isset( $profile ) ) {

	echo '<form method="post" action="" onSubmit="return validate_ice_profile( this );">'.EOL;

	echo '<input type="hidden" name="cmd" value="config_ICE">';
	echo '<input type="hidden" name="edit_profile">'.EOL;

	echo '<table class="config oBox">'.EOL;

	// Name
	echo '<tr><th class="title"><label for="name">'.$TEXT['profile_name'].'</label></th><td>';
	if ( $PMA->profiles->total() > 1 ) {
		// Delete profile
		echo '<a href="?delete_profile" onClick="return del_ice_profile( \''.$profile['name'].'\' );">';
		echo HTML::img( IMG_TRASH_16, 'button right', $TEXT['del_profile'] ).'</a>';
	}
	echo '<input type="text" id="name" name="name" value="'.$profile['name'].'"></td></tr>'.EOL;

	// Public
	echo '<tr><th><label for="public">'.$TEXT['public_profile'].'</label></th>';
	echo '<td><input type="checkbox" '.HTML::chked( $profile['public'] ).' id="public" name="public"></td></tr>'.EOL;

	// Host
	echo '<tr><th><label for="host">'.$TEXT['ICE_host'].'</label></th>';
	echo '<td><input type="text" id="host" name="host" value="'.$profile['host'].'"></td></tr>'.EOL;
	// Port
	echo '<tr><th><label for="port">'.$TEXT['ICE_port'].'</label></th>';
	echo '<td><input type="text" maxlength="5" style="width: 60px" id="port" name="port" value="'.$profile['port'].'"></td></tr>'.EOL;
	// Timeout
	echo '<tr><th><label for="timeout">'.$TEXT['ICE_timeout'].'</label></th><td>';
	echo '<input type="text" maxlength="4" style="width: 60px" id="timeout" name="timeout" value="'.$profile['timeout'].'"></td></tr>'.EOL;
	// Secret
	echo '<tr><th><label for="secret">'.$TEXT['ICE_secret'].'</label></th>';
	echo '<td><input type="text" id="secret" name="secret" value="'.$profile['secret'].'"></td></tr>'.EOL;

	if ( PMA_ICE_INT < 30400 ) {

		if ( isset( $icePHP['profiles_list'] ) ) {

			echo select_slice_profiles( $icePHP['profiles_list'], $profile );
		}

	} else {
		echo select_slice_php( $profile );
	}

	// Connection URL
	echo '<tr><th><label for="http_addr">'.$TEXT['conn_url'];
	echo HTML::info_bubble( HTML::img( IMG_INFO_16 ), $TEXT['conn_url_info'] ).'</label></th>';
	echo '<td><input type="text" id="http_addr" name="http_addr" value="'.$profile['http-addr'].'"></td></tr>'.EOL;

	// Submit
	echo '<tr><th colspan="2"><input type="submit" value="'.$TEXT['apply'].'"></th></tr>'.EOL;

	echo '</table>'.EOL;
	echo '</form>'.EOL.EOL;
}

// infos.
echo '<div class="fontsmall" style="margin: 10px; color: black;">'.EOL;
echo '<div style="margin: 10px;"><b>infos:</b></div>'.EOL;

if ( isset( $PMA->meta->txt_version ) ) {
	echo '<div style="margin: 10px 0px;">Murmur: <b>'.$PMA->meta->txt_version.'</b></div>'.EOL;
}

echo '<div>phpICE: <b>'.PMA_ICE_STR.'</b></div>'.EOL;
echo '<div>ice.slice: <b>'.$icePHP['slice_file'].'</b></div>'.EOL;
echo '<div>ice.profiles: <b>'.$icePHP['profiles_file'].'</b></div>'.EOL;
echo '</div>'.EOL.EOL;

?>
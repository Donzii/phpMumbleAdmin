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

function get_settings_descriptions( $array ) {

	global $TEXT;

	foreach( $array as $key => $arr ) {

		if ( isset( $TEXT[ $key.'_info'] ) ) {
			$array[ $key ]['desc'] = $TEXT[ $key.'_info'];
		} else {
			$array[ $key ]['desc'] = '';
		}
	}

	return $array;
}

pma_load_language( 'vserver_settings' );

// Get $vserver_settings
require 'main/include/vars.vserver_settings.php';
unset( $vserver_settings['welcometext'], $vserver_settings['certificate'], $vserver_settings['key'] );

$vserver_settings = get_settings_descriptions( $vserver_settings );

$default_conf = $PMA->meta->getDefaultConf();

// Murmur default port particularity
$default_conf['port'] += $getServer->sid() - 1;

try {
	$custom_conf = $getServer->getAllConf();

} catch ( Exception $Ex ) {

	pma_murmur_exception( $Ex );
}

function table( $settings, $default, $custom ) {

	global $TEXT;

	$tabindex = 0;

	$is_SuperAdmin = PMA_user::instance()->is_min( CLASS_ROOTADMIN );

	$output = '';

	foreach( $settings as $key => $array ) {

		// Dont show SuperAdmins parameters to others.
		if ( $array['right'] === 'SA' && ! $is_SuperAdmin ) {
			continue;
		}

		++$tabindex;

		// Workaround : getDefaultConf() dont return userperchannel & imagemessagelength parameters
		if ( isset( $default[ $key ] ) ) {
			$conf = $default[ $key ];
		} else {
			$conf = '';
		}

		// Is modified ?
		if ( isset( $custom[ $key ] ) ) {

			$css = 'name modified';
			$conf = $input_value = $custom[ $key ];
			$reset = '<a href="?cmd=murmur_settings&amp;reset_setting='.$key.'">';
			$reset .= HTML::img( IMG_CLEAN_16, 'button', sprintf( $TEXT['reset_param'], $array['name'] ) ).'</a>';

		} else {
			$css = 'name';
			$input_value = '';
			$reset = '';
		}

		// Input
		if ( $array['type'] === 'bool' ) {

			// Select field

			$conf = strToLower( $conf );

			if ( $conf === 'true' ) {
				$conf = $TEXT['enabled'];
				$toggle_options = '<option value="false">'.$TEXT['disable'].'</option>';

			} elseif ( $conf === 'false' ) {
				$conf = $TEXT['disabled'];
				$toggle_options = '<option value="true">'.$TEXT['enable'].'</option>';

			} else {
				$toggle_options = '<option value="true">'.$TEXT['enable'].'</option>';
				$toggle_options .= '<option value="false">'.$TEXT['disable'].'</option>';
			}

			$input = '<select name="'.$key.'" tabindex="'.$tabindex.'" style="width: 200px;">';
			$input .= '<option value="">-</option>';
			$input .= $toggle_options;
			$input .= '</select>';

		} else {

			// Input field

			if ( ! isset( $array['maxlen'] ) ) {
				$array['maxlen'] = '255';
			}

			if ( $array['maxlen'] === '5' ) {
				$style = 'style="width: 50px;"';
			} else {
				$style = '';
			}

			$input = '<input type="text" name="'.$key.'" '.$style.' maxlength="'.$array['maxlen'].'" tabindex="'.$tabindex.'" value="'.$input_value.'">';
		}

		$output .= '<tr>';
		// Parameter + bubble info
		$output .= '<th class="key">'.HTML::info_bubble( $array['name'], $array['desc'] ).'</th>';
		// Current value
		$output .= '<td><div class="'.$css.'">'.html_encode( $conf ).'</div></td>';
		// Input
		$output .= '<td>'.$input.'</td>';
		// Reset
		$output .= '<td class="icon">'.$reset.'</td>';
		$output .= '</tr>'.EOL;
	}

	return $output;
}

require 'views/table.php';

?>

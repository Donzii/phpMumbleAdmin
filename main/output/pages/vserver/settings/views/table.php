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

echo '<form method="post" action="" onSubmit="return validate_settings( this );">'.EOL;

echo '<input type="hidden" name="cmd" value="murmur_settings">'.EOL;
echo '<input type="hidden" name="setConf">'.EOL;

echo '<table id="vserver_settings">'.EOL;
echo '<tr class="invisible">';
echo '<th style="width: 150px;"></th>';
echo '<th></th>';
echo '<th style="width: 280px;"></th>'; // Width 280px for chrome
echo '<th class="icon"></th>';
echo '</tr>'.EOL;

echo table( $vserver_settings, $default_conf, $custom_conf );

echo '<tr><th colspan="4" class="txtR"><input type="submit" value="'.$TEXT['apply'].'"></th></tr>'.EOL;
echo '</table>'.EOL;
echo '</form>'.EOL.EOL;

// DEBUG - show hidden params
if ( PMA_DEBUG > 0 ) {

	unset( $custom_conf['welcometext'], $custom_conf['key'], $custom_conf['certificate'] );

	foreach( $vserver_settings as $key => $array ) {
		unset( $custom_conf[ $key ] );
	}

	if ( ! empty( $custom_conf ) ) {
		echo '<div class="debug oBox"><b>Hidden custom settings</b>:'.EOL;
		foreach( $custom_conf as $key => $value ) {
			echo '<div>'.$key.' => '.$value.'</div>';
		}
		echo '</div>'.EOL.EOL;
	}
}

?>

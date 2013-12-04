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

if ( ! $PMA->user->is_min( CLASS_ROOTADMIN ) ) {
	pma_illegal_operation();
}

$languages = PMA_helpers_options::get_languages();
$skins = PMA_helpers_options::get_skins();
$timezones = PMA_helpers_options::get_timezones();
$installed_locales = PMA_helpers_options::get_installed_locales();
if ( PMA_DEBUG > 1 ) {
	$installed_locales['DEBUG_TEST'] = 'DEBUG_TEST';
}
$locales_profiles = $PMA->config->get( 'installed_localesProfiles' );

// ToolBar
echo '<div class="toolbar">'.EOL;
echo '<a href="./" title="'.$TEXT['cancel'].'">'.HTML::img( IMG_CANCEL_22, 'button right' ).'</a>'.EOL;
echo '</div>'.EOL.EOL;

echo '<form method="post" action="" onSubmit="return form_is_modified( this );">'.EOL;
echo '<input type="hidden" name="cmd" value="config">';
echo '<input type="hidden" name="set_default_options">'.EOL;
echo '<table class="config oBox">'.EOL;

echo '<tr class="pad"><th class="title">'.$TEXT['default_options'].'</th></tr>';

// LANGUAGES
echo '<tr><th>'.$TEXT['default_lang'].'</th><td>';
echo '<select name="lang">';

foreach( $languages as $key => $array ) {

	$selected = HTML::selected( $array['dir'] === $PMA->config->get( 'default_lang' ) );

	echo '<option '.$selected.' value="'.$array['dir'].'">'.$array['name'].'</option>';
}
echo '</select>';
echo '</td></tr>'.EOL;

// SKINS
echo '<tr><th>'.$TEXT['default_style'].'</th><td>';
echo '<select name="skin">';

foreach( $skins as $key => $file ) {

	$name = str_replace( '.css', '', $file );

	$selected = HTML::selected( $file === $PMA->config->get( 'default_skin' ) );

	echo '<option '.$selected.' value="'.$file.'">'.$name.'</option>';
}
echo '</select>';
echo '</td></tr>'.EOL;

// TIMEZONE
echo '<tr><th>'.$TEXT['default_time'].'</th><td>';
echo '<select name="timezone">';

foreach ( $timezones as $continent => $array ) {

	echo '<option disabled="disabled">---</option>';

	foreach ( $array as $tz => $city ) {

		$selected = HTML::selected( $tz === $PMA->config->get( 'default_timezone' ) );

		echo '<option '.$selected.' value="'.$tz.'">'.$continent.' / '.$city.'</option>';
	}
}
echo '</select>';
echo '</td></tr>'.EOL;

// TIME
echo '<tr><th>'.$TEXT['default_time_format'].'</th>';
echo '<td><select name="time">';

foreach ( $time_options as $key => $value ) {

	$selected = HTML::selected( $value['options'] === $PMA->config->get( 'default_time' ) );

	echo '<option '.$selected.' value="'.$value['options'].'">'.$value['desc'].'</option>';
}
echo '</select></td></tr>'.EOL;

// DATE
echo '<tr><th>'.$TEXT['default_date_format'].'</th>';
echo '<td><select name="date">';
foreach ( $date_options as $key => $value ) {

	$selected = HTML::selected( $value['options'] === $PMA->config->get( 'default_date' ) );

	echo '<option '.$selected.' value="'.$value['options'].'">'.$value['desc'].'</option>';
}
echo '</select></td></tr>'.EOL;

// LOCALES PROFILES
echo '<tr><th>'.$TEXT['default_locales'].'</th>';
echo '<td><select name="locales">';

foreach ( $installed_locales as $key => $value ) {

	$selected = HTML::selected( $key === $PMA->config->get( 'default_installed_locales' ) );

	echo '<option '.$selected.' value="'.$key.'">'.$value.'</option>';
}
echo '</select></td></tr>'.EOL;

echo '<tr><th colspan="2"><input type="submit" value="'.$TEXT['apply'].'"></th></tr>'.EOL;

echo '</table>'.EOL;
echo '</form>'.EOL.EOL;

echo '<div style="margin: 10px;"></div>'.EOL;

// Add a locales profile
echo '<form method="post" action="" onSubmit="return validate_add_informations_locales( this );">'.EOL;
echo '<input type="hidden" name="cmd" value="config">';
echo '<input type="hidden" name="add_locales_profile">'.EOL;
echo '<table class="config oBox">'.EOL;
echo '<tr class="pad"><th class="title">'.$TEXT['add_locales_profile'].'</th></tr>'.EOL;
echo '<tr><th></th>';
echo '<td><select name="key">';
echo '<option>'.$TEXT['none'].'</option>';

foreach ( $installed_locales as $key => $value ) {

	if ( $key === 'default' ) {
		continue;
	}

	if ( ! isset( $locales_profiles[ $key ] ) ) {
		echo '<option value="'.$key.'">'.$value.'</option>';
	}
}
echo '</select></td></tr>'.EOL;
echo '<tr><th></th><td><input type="text" name="val"></td></tr>'.EOL;
echo '<tr><th colspan="2"><input type="submit" value="'.$TEXT['add'].'"></th>'.EOL;
echo '</table>'.EOL;
echo '</form>'.EOL.EOL;

echo '<div style="margin: 10px;"></div>'.EOL;

// Remove a locales profile
echo '<table class="config oBox">'.EOL;
echo '<tr><th>'.$TEXT['del_locales_profile'].'</th><td>';
echo '<form method="post" action="" onSubmit="return unchanged( this.delete_locales_profile );">';
echo '<input type="hidden" name="cmd" value="config">';
echo '<select name="delete_locales_profile">';
echo '<option>'.$TEXT['none'].'</option>';
foreach ( $locales_profiles as $key => $value ) {
	echo '<option value="'.$key.'">'.cut_long_str( $value, 20 ).' ( '.$key.' )</option>';
}
echo '</select>';
echo '<input type="submit" value="'.$TEXT['delete'].'" style="margin-left: 10px;"></form>';
echo '</td></tr>'.EOL;
echo '</table>'.EOL.EOL;


?>
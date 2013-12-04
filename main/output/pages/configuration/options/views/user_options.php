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

$languages = PMA_helpers_options::get_languages();
$skins = PMA_helpers_options::get_skins();
$timezones = PMA_helpers_options::get_timezones();
$locales_profiles = $PMA->config->get( 'installed_localesProfiles' );

$uptime_options[] = 1;
$uptime_options[] = 2;
$uptime_options[] = 3;

// ToolBar
echo '<div class="toolbar">'.EOL;
if ( $PMA->user->is_min( CLASS_ROOTADMIN ) ) {
	echo '<a href="?set_default_options" class="right">'.$TEXT['default_options'].'</a>'.EOL;
}
// Change personal pw
if ( $PMA->user->is( CLASS_SUPERADMIN ) ) {
	echo '<a href="?edit_SuperAdmin" title="'.$TEXT['change_your_pw'].'">'.HTML::img( IMG_KEY_22, 'button' ).'</a>'.EOL;
} elseif ( $PMA->user->admin_id !== NULL ) {
	echo '<a href="?change_your_password" title="'.$TEXT['change_your_pw'].'">'.HTML::img( IMG_KEY_22, 'button' ).'</a>'.EOL;
}
echo '</div>'.EOL.EOL;

echo '<form method="post" action="" onSubmit="return form_is_modified( this );">';
echo '<input type="hidden" name="cmd" value="config">';
echo '<input type="hidden" name="set_options">'.EOL;
echo '<table class="config oBox">'.EOL;

// LANGUAGES
echo '<tr><th>'.$TEXT['select_lang'].'</th><td>';
echo '<select name="lang">';

foreach( $languages as $key => $array ) {

	$selected = HTML::selected( $array['dir'] === $PMA->cookie->get( 'lang' ) );

	echo '<option '.$selected.' value="'.$array['dir'].'">'.$array['name'].'</option>';
}
echo '</select></td></tr>'.EOL;


// SKINS
echo '<tr><th>'.$TEXT['select_style'].'</th><td>';
echo '<select name="skin">';

foreach( $skins as $key => $file ) {

	$name = str_replace( '.css', '', $file );

	$selected = HTML::selected( $file === $PMA->cookie->get( 'skin' ) );

	echo '<option '.$selected.' value="'.$file.'">'.$name.'</option>';
}
echo '</select></td></tr>'.EOL;

// TIMEZONES
echo '<tr><th>'.$TEXT['select_time'].'</th><td>';
echo '<select name="timezone">'.EOL;

foreach ( $timezones as $continent => $array ) {

	echo '<option disabled="disabled">---</option>';

	foreach ( $array as $tz => $city ) {

		$selected = HTML::selected( $tz === $PMA->cookie->get( 'timezone' ) );

		echo '<option '.$selected.' value="'.$tz.'">'.$continent.' / '.$city.'</option>';
	}
}

echo '</select></td></tr>'.EOL;

// TIME
echo '<tr><th>'.$TEXT['time_format'].'</th><td>';
echo '<select name="time">';

foreach ( $time_options as $key => $value ) {

	$selected = HTML::selected( $value['options'] === $PMA->cookie->get( 'time' ) );

	echo '<option '.$selected.' value="'.$value['options'].'">'.$value['desc'].'</option>';
}
echo '</select></td></tr>'.EOL;

// DATE
echo '<tr><th>'.$TEXT['date_format'].'</th><td>';
echo '<select name="date">';

foreach ( $date_options as $key => $value ) {

	$selected = HTML::selected( $value['options'] === $PMA->cookie->get( 'date' ) );

	echo '<option '.$selected.' value="'.$value['options'].'">'.$value['desc'].'</option>';
}
echo '</select></td></tr>'.EOL;

// LOCALES PROFILES
echo '<tr><th>'.$TEXT['select_locales_profile'].'</th><td>';
echo '<select name="locales">';
echo '<option>'.$TEXT['default'].'</option>';

foreach ( $locales_profiles as $key => $value ) {

	$selected = HTML::selected( $key === $PMA->cookie->get( 'installed_localeFormat' ) );

	echo '<option '.$selected.' value="'.$key.'">'.$value.'</option>';
}
echo '</select></td></tr>'.EOL;


// UPTIME
echo '<tr><th>'.$TEXT['uptime_format'].'</th><td>';
echo '<select name="uptime">';

foreach ( $uptime_options as $value ) {

	$selected = HTML::selected( $value === $PMA->cookie->get( 'uptime' ) );

	echo '<option '.$selected.' value="'.$value.'">'.PMA_helpers_dates::uptime( 21686399, $value ).'</option>';
}
echo '</select></td></tr>'.EOL;

echo '<tr class="pad"><td class="hide"></td></tr>'.EOL;

// LOGIN
echo '<tr><th><label for="vserver_login">'.$TEXT['conn_login'].'';
echo HTML::info_bubble( HTML::img( IMG_INFO_16 ), $TEXT['conn_login_info'] ).'</label></th>';
echo '<td><input type="text" id="vserver_login" name="vserver_login" value="'.$PMA->cookie->get( 'vserver_login' ).'"></td></tr>'.EOL;

// Submit
echo '<tr><th colspan="2"><input type="submit" value="'.$TEXT['apply'].'"></th></tr>'.EOL;

echo '</table>'.EOL;
echo '</form>'.EOL.EOL;

?>
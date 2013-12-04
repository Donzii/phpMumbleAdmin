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

// Get $vserver_settings
require 'main/include/vars.vserver_settings.php';

sort_array_by( $vserver_settings, 'name' );

// Confirm word need to be at minimun 4 chars.
if ( strlen( $TEXT['confirm_word'] ) < 4 ) {
	$TEXT['confirm_word'] = 'confirm';
}

echo '<div class="toolbar">';
echo '<a href="./" title="'.$TEXT['cancel'].'">'.HTML::img( IMG_CANCEL_22, 'button right' ).'</a>'.EOL;
echo '</div>';

echo '<form method="POST" action="" onSubmit="return validate_mass_setting( this, \''.$TEXT['confirm_word'].'\' );">';

echo '<input type="hidden" name="cmd" value="overview">';
echo '<input type="hidden" name="mass_settings">';
echo '<input type="hidden" name="confirm_word" value="'.$TEXT['confirm_word'].'">';

echo '<table class="config oBox">';

echo '<tr class="pad"><th class="title">'.$TEXT['mass_settings'].'</th></tr>';

echo '<tr><th><select name="key"><option value="">'.$TEXT['select_setting'].'</option>';

foreach( $vserver_settings as $key => $array ) {
	echo '<option value="'.$key.'">'.$array['name'].'</option>';
}

echo '</select></th>';

echo '<td><textarea name="value" rows="6" cols="6"></textarea></td></tr>';

// Confirm
echo '<tr><th>'.sprintf( $TEXT['confirm_with_word'], $TEXT['confirm_word'] ).'</th><td><input type="text" name="confirm"></td></tr>';
echo '<tr><th colspan="2"><input type="submit"></th></tr>';

echo '</table>';

echo '</form>';

?>
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

function vlogs_amounts_options( $i ) {

	$size = PMA_config::instance()->get( 'vlogs_size' );

	$selected = HTML::selected( $i === $size );

	return '<option '.$selected.' value="'.$i.'">'.number_format( $i ).'</option>';
}

$vlogs_amounts_options = '';

for ( $i = 100; $i <= 900; $i += 100 ) {
	$vlogs_amounts_options .= vlogs_amounts_options( $i );
}

for ( $i = 1000; $i <= 9500; $i += 500 ) {
	$vlogs_amounts_options .= vlogs_amounts_options( $i );
}

for ( $i = 10*1000; $i <= 100*1000; $i += 5000 ) {
	$vlogs_amounts_options .= vlogs_amounts_options( $i );
}

echo '<form method="post" action="" onSubmit="return form_is_modified( this );">'.EOL;
echo '<input type="hidden" name="cmd" value="config">';
echo '<input type="hidden" name="set_settings_logs">'.EOL;

echo '<table class="config oBox">'.EOL;

// vlogs amounts
echo '<tr class="pad"><th class="title">'.$TEXT['vservers_logs'].'</th></tr>'.EOL;
echo '<tr><th>'.$TEXT['srv_logs_amount'].'</th><td>';
echo '<select name="murmur_logs_size">';
echo '<option value="-1">'.$TEXT['all'].'</option>';
echo $vlogs_amounts_options;
echo '</select>'.EOL;
echo '</td></tr>'.EOL;

// Activate vlogs for admins
echo '<tr><th><label for="activate_admins">'.$TEXT['activate_vservers_logs_for_adm'].'</label></th>';
echo '<td><input type="checkbox" '.HTML::chked( $PMA->config->get( 'vlogs_admins_active' ) ).' id="activate_admins" name="activate_admins"></td></tr>'.EOL;
echo '<tr><th><label for="hightlights">'.$TEXT['activate_adm_highlight_logs'].'</label></th>';

// Authorize vlogs highlights
echo '<td><input type="checkbox" '.HTML::chked( $PMA->config->get( 'vlogs_admins_highlights' ) ).' id="hightlights" name="adm_hightlights_logs"></td></tr>'.EOL;

// PMA LOGS
if ( $PMA->user->is( CLASS_SUPERADMIN ) ) {

	echo '<tr class="pad"><td class="hide"></td></tr>'.EOL;
	echo '<tr class="pad"><th class="title">'.$TEXT['pma_logs'].'</th><td class="unsafe i">'.$TEXT['pma_logs_infos'].'</td></tr>'.EOL;

	// keep time
	echo '<tr><th><label for="log_keep">'.$TEXT['pma_logs_clean'].'</label></th>';
	echo '<td><input type="text" maxlength="5" style="width: 40px" id="log_keep" name="log_keep" value="'.$PMA->config->get( 'pmaLogs_keep' ).'"> ';
	echo $TEXT['disable_function'].'</td></tr>'.EOL;

	// Log SA actions
	echo '<tr><th><label for="log_SA">'.$TEXT['logs_sa_actions'].'</label></th>';
	echo '<td><input type="checkbox" '.HTML::chked( $PMA->config->get( 'pmaLogs_SA_actions' ) ).' id="log_SA" name="log_SA"></td></tr>'.EOL;
}

echo '<tr><th colspan="2"><input type="submit" value="'.$TEXT['apply'].'"></th></tr>'.EOL;

echo '</table>'.EOL;
echo '</form>'.EOL.EOL;

?>
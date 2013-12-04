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

echo '<form method="post" action="" onSubmit="return form_is_modified( this );">'.EOL;
echo '<input type="hidden" name="cmd" value="config">';
echo '<input type="hidden" name="set_settings_autoban">'.EOL;
echo '<table class="config oBox">'.EOL;
echo '<tr class="pad"><th class="title"></th></tr>'.EOL;

// atempts
echo '<tr><th><label for="attempts">'.$TEXT['autoban_attemps'].'</label></th>';
echo '<td><input type="text" style="width: 50px" id="attempts" name="attempts" value="'.$PMA->config->get( 'autoban_attempts' ).'"> '.$TEXT['disable_function'].'</td></tr>'.EOL;

// time frame
echo '<tr><th><label for="timeFrame">'.$TEXT['autoban_frame'].'</label></th>';
echo '<td><input type="text" id="timeFrame" name="timeFrame" value="'.$PMA->config->get( 'autoban_frame' ).'"></td></tr>'.EOL;

// duration
echo '<tr><th><label for="duration">'.$TEXT['autoban_duration'].'</label></th>';
echo '<td><input type="text" id="duration" name="duration" value="'.$PMA->config->get( 'autoban_duration' ).'"></td></tr>'.EOL;
echo '<tr><th colspan="2"><input type="submit" value="'.$TEXT['apply'].'"></th></tr>'.EOL;
echo '</table>'.EOL;
echo '</form>'.EOL.EOL;

?>
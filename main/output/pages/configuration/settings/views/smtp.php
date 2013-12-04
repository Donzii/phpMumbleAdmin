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
echo '<input type="hidden" name="set_settings_smtp">'.EOL;
echo '<table class="config oBox">'.EOL;
echo '<tr class="pad"><th class="title"></th></tr>'.EOL;
// host
echo '<tr><th><label for="host">'.$TEXT['host'].'</label></th>';
echo '<td><input type="text" id="host" name="host" value="'.$PMA->config->get( 'smtp_host' ).'"></td></tr>'.EOL;
// port
echo '<tr><th><label for="port">'.$TEXT['port'].'</label></th>';
echo '<td><input type="text" id="port" name="port" style="width: 50px;" maxlength="5" value="'.$PMA->config->get( 'smtp_port' ).'"></td></tr>'.EOL;
// Default sender email
echo '<tr><th><label for="default_sender">'.$TEXT['default_sender_email'].'</label></th>';
echo '<td><input type="text" id="default_sender" name="default_sender" value="'.$PMA->config->get( 'smtp_default_sender_email' ).'"></td></tr>'.EOL;

if ( PMA_DEBUG > 0 ) {
	echo '<tr><th></th><td><a href="?cmd=config&amp;send_debug_email">Send a debug email</a></td></tr>'.EOL;
}

// Apply
echo '<tr><th colspan="2"><input type="submit" value="'.$TEXT['apply'].'"></th></tr>'.EOL;
echo '</table>'.EOL;
echo '</form>'.EOL.EOL;


?>
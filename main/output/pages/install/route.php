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

define( 'INSTALL_COMPLETED', $PMA->config->get( 'SA_login' ) !== '' && $PMA->config->get( 'SA_pw' ) !== '' );

$tab = $PMA->tabs->current();

// Note on ice errors
msg_box( $TEXT['install_note'], 'error', 'nobutton' );

if ( $tab === 'setup_SuperAdmin' ) {

	if ( INSTALL_COMPLETED ) {
		echo '<span class="safe b">'.$TEXT['install_success'].'<br>'.$TEXT['install_to_finish'].'</span>';
	}

	echo '<form class="actionBox medium" method="POST" action="" onSubmit="return validate_install( this );">'.EOL;
	echo '<input type="hidden" name="cmd" value="install">'.EOL;
	echo '<input type="hidden" name="setup_SuperAdmin">'.EOL;
	echo '<table class="config oBox">'.EOL;
	// Title
	echo '<tr><th class="title">'.$TEXT['setup_sa'].'</th></tr>'.EOL;
	// Login
	echo '<tr><th><label for="login">'.$TEXT['sa_login'].'</label></th><td><input type="text" id="login" name="login" value="'.$PMA->config->get( 'SA_login' ).'"></td></tr>'.EOL;
	echo '<tr class="pad"><td class="hide"></td></tr>'.EOL;
	// Password
	echo '<tr><th><label for="pw">'.$TEXT['new_pw'].'</label></th><td><input type="password" id="pw" name="new_pw"></td></tr>'.EOL;
	echo '<tr><th><label for="confirm_pw">'.$TEXT['confirm_pw'].'</label></th><td><input type="password" id="confirm_pw" name="confirm_new_pw"></td></tr>'.EOL;
	// Submit
	echo '<tr><th colspan="2"><input type="submit"></th></tr>'.EOL;
	echo '</table>'.EOL;
	echo '</form>'.EOL;

} elseif ( $tab === 'requirement' ) {

	echo PMA_output_files_requirement::get( $PMA->db->get_files() );

} else {
	echo '<br>'.$TEXT['install_welcome_msg'].EOL;
}

?>
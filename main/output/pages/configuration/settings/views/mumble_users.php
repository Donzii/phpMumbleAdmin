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

if ( isset( $_GET['pw_requests_options'] ) ) {

	// ToolBar
	echo '<div class="toolbar">'.EOL;
	// Back button
	echo '<a href="./" title="'.$TEXT['back'].'">'.HTML::img( IMG_CANCEL_22, 'button right' ).'</a>'.EOL;
	echo '</div>'.EOL.EOL;

	echo '<form method="post" action="" onSubmit="return form_is_modified( this );">'.EOL;
	echo '<input type="hidden" name="cmd" value="config">';
	echo '<input type="hidden" name="set_pw_requests_options">'.EOL;
	echo '<table class="config oBox">'.EOL;
	echo '<tr><th><label for="explicit_msg">'.$TEXT['activate_explicite_msg'].'</label></th>';
	echo '<td><input type="checkbox" '.HTML::chked( $PMA->config->get( 'pw_gen_explicit_msg' ) ).' id="explicit_msg" name="explicit_msg"></td></tr>'.EOL;
	echo '<tr><th><label for="sender_email">'.$TEXT['sender_email'].'</label></th>';
	echo '<td><input type="text" id="sender_email" name="sender_email" value="'.$PMA->config->get( 'pw_gen_sender_email' ).'"></td></tr>'.EOL;
	echo '<tr><th><label for="delay">'.$TEXT['pwgen_max_pending'].'</label></th>';
	echo '<td><input type="text" maxlength="3" style="width: 50px" id="delay" name="pending_delay" value="'.$PMA->config->get( 'pw_gen_pending' ).'"></td></tr>'.EOL;

	if ( PMA_DEBUG > 0 ) {
		echo '<tr><th></th><td><a href="?cmd=config&amp;send_debug_email=pw_gen_sender_email">Send a debug email</a></td></tr>'.EOL;
	}

	echo '<tr><th colspan="2"><input type="submit" value="'.$TEXT['apply'].'"></th></tr>'.EOL;

	echo '</table>'.EOL;
	echo '</form>'.EOL.EOL;

// DEFAULT: mumble users settings
} else {

	echo '<form method="post" action="" onSubmit="return form_is_modified( this );">'.EOL;
	echo '<input type="hidden" name="cmd" value="config">';
	echo '<input type="hidden" name="set_mumble_users">'.EOL;
	echo '<table class="config oBox">'.EOL;

	// SUPERUSERS
	echo '<tr class="pad"><th class="title">SuperUsers</th></tr>'.EOL;
	echo '<tr><th><label for="set1">'.$TEXT['activate_su_login'].'</label></th>';
	echo '<td><input type="checkbox" '.HTML::chked( $PMA->config->get( 'SU_auth' ) ).' id="set1" name="set1"></td></tr>'.EOL;
	echo '<tr><th><label for="set3">'.$TEXT['activate_su_modify_pw'].'</label></th>';
	echo '<td><input type="checkbox" '.HTML::chked( $PMA->config->get( 'SU_edit_user_pw' ) ).' id="set3" name="set3"></td></tr>'.EOL;
	echo '<tr><th><label for="set4">'.$TEXT['activate_su_vserver_start'].'</label></th>';
	echo '<td><input type="checkbox" '.HTML::chked( $PMA->config->get( 'SU_start_vserver' ) ).' id="set4" name="set4"></td></tr>'.EOL;

	// SUPER USERS_ru
	echo '<tr class="pad"><td class="hide"></td></tr>'.EOL;
	echo '<tr class="pad"><th class="title">SuperUser_ru</th></tr>'.EOL;
	echo '<tr><th><label for="set5">'.$TEXT['activate_su_ru'].'</label>';
	echo HTML::info_bubble( HTML::img( IMG_INFO_16 ), $TEXT['activate_su_ru_info'] ).'</th>';
	echo '<td><input type="checkbox" '.HTML::chked( $PMA->config->get( 'SU_ru_active' ) ).' id="set5" name="set5"></td></tr>'.EOL;

	// Registered users
	echo '<tr class="pad"><td class="hide"></td></tr>'.EOL;
	echo '<tr class="pad"><th class="title">'.$TEXT['reg_users'].'</th></tr>'.EOL;
	echo '<tr><th><label for="set6">'.$TEXT['activate_ru_login'].'</label></th>';
	echo '<td><input type="checkbox" '.HTML::chked( $PMA->config->get( 'RU_auth' ) ).' id="set6" name="set6"></td></tr>'.EOL;
	echo '<tr><th><label for="set7">'.$TEXT['activate_ru_del_account'].'</label></th>';
	echo '<td><input type="checkbox" '.HTML::chked( $PMA->config->get( 'RU_delete_account' ) ).' id="set7" name="set7"></td></tr>'.EOL;
	echo '<tr><th><label for="set8">'.$TEXT['activate_ru_modify_login'].'</label></th>';
	echo '<td><input type="checkbox" '.HTML::chked( $PMA->config->get( 'RU_edit_login' ) ).' id="set8" name="set8"></td></tr>'.EOL;
	echo '<tr><th><label for="set9">'.$TEXT['activate_pwgen'].'</label></th>';
	echo '<td><div style="float: right;"><a href="?pw_requests_options">'.$TEXT['tab_options'].'</a></div>';
	echo '<input type="checkbox" '.HTML::chked( $PMA->config->get( 'pw_gen_active' ) ).' id="set9" name="set9"></td></tr>'.EOL;

	echo '<tr><th colspan="2"><input type="submit" value="'.$TEXT['apply'].'"></th></tr>'.EOL;

	echo '</table>'.EOL;
	echo '</form>'.EOL.EOL;
}


?>
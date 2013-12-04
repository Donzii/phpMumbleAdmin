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

// toolbar
if ( $PMA->user->is_min( CLASS_SUPERUSER_RU ) ) {

	echo '<div class="toolbar">'.EOL;
	echo '<a href="?tab=registrations" title="'.$TEXT['back'].'">'.HTML::img( IMG_CANCEL_22, 'button right' ).'</a>'.EOL;
	echo '</div>'.EOL.EOL;
}

echo '<div id="mumble_registration" class="oBox">'.EOL;

// There is a bug with getTexture() before murmur 1.2.3
if ( $PMA->meta->int_version >= 123 ) {

	if ( $PMA->user->is_min( CLASS_ROOTADMIN ) OR ! $PMA->config->get( 'show_avatar_sa' ) ) {

		$avatar = new avatar( $getServer, $registration->id );

		echo '<div class="left">'.EOL;
		echo '<div id="avatar">'.EOL;
		echo $avatar->img();
		echo '</div>'.EOL;
		echo $avatar->delete_link();
		echo '</div>'.EOL;
	}
}

// Center
echo '<div class="center left">'.EOL;
echo '<table class="config">'.EOL;

// Login
echo '<tr><th>'.$TEXT['login'].'</th></tr>';
echo '<tr><td colspan="2" class="login">';

// user status button
$status = registered_is_online( $registration->id, $getUsers );

if ( $PMA->user->is_min( CLASS_SUPERUSER_RU ) ) {

	if ( $status['txt'] === 'on' ) {
		echo '<a href="?tab=channels&amp;userSession='.$status['url'].'">';
		echo HTML::img( IMG_SPACE_16, 'button on', $TEXT['user_is_online'] ).'</a>';
	} else {
		echo HTML::img( IMG_SPACE_16, 'button off', $TEXT['offline'] );
	}
} else {
	echo HTML::img( IMG_SPACE_16, $status['txt'].' button', $TEXT[ $status['txt'].'line'] );
}

echo $registration->name.'</td></tr>'.EOL;

// Email
if ( $registration->cert !== '' ) {
	// User with certificat => info text
	$bubble = HTML::info_bubble( HTML::img( IMG_INFO_16 ), $TEXT['cert_email_info'] );
} else {
	$bubble = '';
}
echo '<tr><th>'.$TEXT['email_addr'].$bubble.'</th></tr>';
echo '<tr><td colspan="2"><span class="email name"><a href="mailto:'.$registration->email.'" title="mailto:'.$registration->email.'">';
echo $registration->email.'</a></span></td></tr>'.EOL;

// Last activity ( come with murmur 1.2.3 )
if ( $PMA->meta->int_version >= 123 ) {

	echo '<tr><th>'.$TEXT['last_activity'].'</th></tr>';

	if ( $registration->last_activity === '' ) {

		echo '<tr><td colspan="2"></td></tr>'.EOL;

	} else {

		$ts = PMA_helpers_dates::datetime_to_timestamp( $registration->last_activity );

		echo '<tr><td colspan="2" title="'.PMA_helpers_dates::complet( $ts ).'">';
		echo '<span class="help">'.PMA_helpers_dates::uptime( PMA_TIME - $ts ).'</span></td></tr>'.EOL;
	}
}

// hash
if ( $PMA->user->is_min( CLASS_SUPERUSER_RU ) ) {
	echo '<tr><th>'.$TEXT['cert_hash'].'</th></tr><tr><td colspan="2">'.$registration->cert.'</td></tr>'.EOL;
}

// Comment
echo '<tr><th>'.$TEXT['comment'].'</th></tr>'.EOL;
echo '<tr><td colspan="2"><div class="description">'.$registration->desc.'</div></td></tr>'.EOL;
echo '</table>'.EOL;
echo '</div>'.EOL.EOL;
// Center - END

// LINKS MENU
echo '<ul class="menu left">'.EOL;

// Modify login
if ( $PMA->user->is_min( CLASS_SUPERUSER_RU ) OR $PMA->config->get( 'RU_edit_login' ) ) {
	echo '<li><a href="?change_login" onClick="return change_login( \''.$registration->name.'\' );">'.$TEXT['modify_login'].'</a></li>'.EOL;
}

// Modify password
if ( $PMA->user->is_min( CLASS_ADMIN ) OR $PMA->config->get( 'SU_edit_user_pw' ) OR $registration->own_account ) {
	echo '<li><a href="?change_password">'.$TEXT['modify_pw'].'</a></li>'.EOL;
}

// Modify email
echo '<li><a href="?change_email" onClick="return change_email( \''.$registration->email.'\');">'.$TEXT['modify_email'].'</a></li>'.EOL;

// Modify description
echo '<li><a href="?change_desc" onClick="return change_comment( \''.$desc_textarea.'\' );">'.$TEXT['modify_comm'].'</a></li>'.EOL;

// Delete account
if ( $registration->id > 0 ) {

	if ( $PMA->user->is_min( CLASS_SUPERUSER_RU ) OR $PMA->config->get( 'RU_delete_account' ) ) {

		echo '<li><a href="?delete_account" onClick="return del_account_sess( \''.$registration->name.'\' );">'.$TEXT['delete_acc'].'</a></li>'.EOL;
	}
}
echo '</ul>'.EOL.EOL;

echo '<div class="clear"></div>'.EOL;
echo '</div><!-- mumble_registration - END -->'.EOL.EOL;

?>

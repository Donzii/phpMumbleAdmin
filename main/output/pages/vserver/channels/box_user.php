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

// Get user session
$uSess = clone $getUsers[ $_SESSION['page_vserver']['uSess']['id'] ];

$JS->add_text( 'change_user_session_name', $TEXT['change_user_session_name'] );
$JS->add_text( 'kick_user', sprintf( $TEXT['kick_user'], $uSess->name ) );

$uSess->ip = ip_dec_to_str( $uSess->address );
$uSess->ip = $uSess->ip['ip'];

// getCertificateList comes with murmur 1.2.1.
if ( method_exists( 'Murmur_Server', 'getCertificateList' ) ) {

	$getCertificateList = $getServer->getCertificateList( $uSess->session );

	if ( ! empty( $getCertificateList ) ) {

		$uSess->cert_blob = array_dec_to_chars( $getCertificateList[0] );
		$uSess->cert_sha1 = sha1( $uSess->cert_blob );

	} else {
		$uSess->cert_sha1 = 'certificat disabled';
	}
}

$tabs = array( 'comment' => $TEXT['comment'], 'info' => $TEXT['infos'] );

// Change userSession tab if valid
if ( isset( $_GET['uTab'] ) && isset( $tabs[ $_GET['uTab'] ] ) ) {
	$_SESSION['page_vserver']['uTab'] = $_GET['uTab'];
}

// Default tab: information
if ( ! isset( $_SESSION['page_vserver']['uTab'] ) OR ! isset( $tabs[ $_SESSION['page_vserver']['uTab'] ] ) ) {
	$_SESSION['page_vserver']['uTab'] = 'info';
}

// Tabs
echo '<div class="tabmenu user">'.EOL;

$action_menu = new PMA_output_expand_menu( $TEXT['action'] );

$action_menu->add_link( '?action=kick_user', $TEXT['kick'], 'xchat/kick_16.png', 'onClick="return kick_user();"' );
$action_menu->add_link( '?action=ban_user', $TEXT['ban'], 'xchat/ban_16.png' );
if ( $PMA->meta->int_version >= 124 ) {
	$action_menu->add_link( '?action=change_user_session_name', $TEXT['change_user_session_name'], 'tango/group_16.png', 'onClick="return change_user_session_name();"' );
}
$action_menu->add_link( '?action=msg_user', $TEXT['send_msg'], IMG_MSG_16, 'onClick="return send_user_msg();"' );

if ( $count_getChannels > 1 ) {
	$action_menu->add_link( '?action=move_user', $TEXT['move'], IMG_UP_16 );
} else {
	$action_menu->add( $TEXT['move'] );
}

if ( $uSess->mute ) {
	$txt = $TEXT['unmute'];
	$img = 'user_unmute';
} else {
	$txt = $TEXT['mute'];
	$img = 'user_muted';
}
$action_menu->add_link( '?cmd=murmur_users_sessions&amp;muteUser', $txt, 'mumble/'.$img.'.png' );

if ( $uSess->deaf ) {
	$txt = $TEXT['undeafen'];
	$img = 'user_undeafen';
} else {
	$txt = $TEXT['deafen'];
	$img = 'user_deafened';
}
$action_menu->add_link( '?cmd=murmur_users_sessions&amp;deafUser', $txt, 'mumble/'.$img.'.png' );

// PrioritySpeaker come with murmur 1.2.3
if ( isset( $uSess->prioritySpeaker ) ) {

	if ( $uSess->prioritySpeaker ) {
		$txt = $TEXT['disable_priority'];
		$img = 'microphone-muted_16';
	} else {
		$txt = $TEXT['enable_priority'];
		$img = 'microphone_16';
	}
	$action_menu->add_link( '?cmd=murmur_users_sessions&amp;togglePrioritySpeaker', $txt, 'tango/'.$img.'.png' );
}

if ( $uSess->userid >= 0 ) {
	$action_menu->add_link( '?tab=registrations&amp;registration_id='.$uSess->userid, $TEXT['edit_account'], IMG_EDIT_16 );
} elseif ( isset( $uSess->cert_blob ) ) {
	$action_menu->add_link( '?cmd=murmur_users_sessions&amp;register_session', $TEXT['register_user'], IMG_ADD_16 );
}

echo $action_menu->output();

// Print menu tabs
foreach ( $tabs as $tab => $txt ) {

	if ( $tab === $_SESSION['page_vserver']['uTab'] ) {
		$css = ' selected';
	} else {
		$css = '';
	}
	echo '<a class="tab'.$css.'" href="?uTab='.$tab.'">'.$txt.'</a>'.EOL;
}

echo '</div><!-- tabmenu - END -->'.EOL.EOL;


if ( isset( $_GET['action'] ) ) {

	switch( $action = $_GET['action'] ) {

		case 'kick_user':
		case 'ban_user':
		case 'change_user_session_name':
		case 'msg_user':

			require 'actions/'.$action.'.php';
			break;


		case 'move_user':

			echo PMA_output_actionBox::select_channel( sprintf( $TEXT['move_user'], $uSess->name ) );
			break;
	}

} elseif ( $_SESSION['page_vserver']['uTab'] === 'info' ) {

	if ( isset( $_GET['uCert'] ) && isset( $uSess->cert_blob ) ) {

		require 'views/user_certificate.php';

	} else {

		require 'views/user_informations.php';
	}

} elseif ( $_SESSION['page_vserver']['uTab'] === 'comment' ) {

	require 'views/user_comment.php';
}

?>

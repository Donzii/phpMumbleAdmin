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

$JS->add_text( 'add_sub_channel', $TEXT['add_channel'] );
$JS->add_text( 'to_all_sub_channels', $TEXT['send_msg_to_all_sub'] );
$JS->add_text( 'del_channel', $TEXT['confirm_del_channel'] );

// Get the selected channel object
$CHANNEL = clone $getChannels[ $_SESSION['page_vserver']['cid'] ];

// Tabs
$CHANNEL->tabs = array( 'acl' => $TEXT['tab_acl'], 'group' => $TEXT['tab_groups'], 'property' => $TEXT['tab_properties'] );

// Change channel tab if valid
if ( isset( $_GET['cTab'] ) && isset( $CHANNEL->tabs[ $_GET['cTab'] ] ) ) {
	$_SESSION['page_vserver']['cTab'] = $_GET['cTab'];
}

// Default tab : property
if ( ! isset( $_SESSION['page_vserver']['cTab'] ) OR ! isset( $CHANNEL->tabs[ $_SESSION['page_vserver']['cTab'] ] ) ) {
	$_SESSION['page_vserver']['cTab'] = 'property';
}

// Count users currently in the channel
$CHANNEL->user_in = 0;

foreach( $getUsers as $obj ) {

	if ( $obj->channel === $CHANNEL->id ) {
		++$CHANNEL->user_in;
	}
}

//  $mumble_url can be not set if infoPanel is turned off
if ( ! isset( $mumble_url ) ) {
	$mumble_url = $getServer->url();
}

// Construct the channel deep to connect to a particular channel. ( /channel/Deep1Channel/Deep2Channel/etc.../ )
function deep_channel_url( $cid ) {

	global $getChannels;

	$url = '';

	while ( $cid > 0 ) {

		$obj = $getChannels[ $cid ];

		$url = $obj->name.'/'.$url;

		$cid = $obj->parent;
	}

	return rawUrlEncode( $url );
}

$deep = deep_channel_url( $CHANNEL->id );
$CHANNEL->url = str_replace( '/?version=', '/'.$deep.'?version=', $mumble_url );

// Tabs
echo '<div class="tabmenu channel">'.EOL;

$action_menu = new PMA_output_expand_menu( $TEXT['action'] );

// Connection to channel
if ( $CHANNEL->id > 0 ) {
	$action_menu->add_link( $CHANNEL->url, $TEXT['conn_to_channel'], IMG_CONN_16 );
} else {
	$action_menu->add( $TEXT['conn_to_channel'] );
}

// add sub channel
if ( ! $CHANNEL->temporary ) {
	$action_menu->add_link( '?action=add_channel', $TEXT['add_channel'], IMG_ADD_16, 'onClick="return add_sub_channel();"' );
} else {
	$action_menu->add( $TEXT['add_channel'] );
}

// send a message
$action_menu->add_link( '?action=msg_channel', $TEXT['send_msg'], IMG_MSG_16, 'onClick="return send_channel_msg();"' );

// move users out
if ( $CHANNEL->user_in > 0 && $count_getChannels > 1 ) {
	$action_menu->add_link( '?action=move_users_out', $TEXT['move_user_off_chan'], IMG_UP_16 );
} else {
	$action_menu->add( $TEXT['move_user_off_chan'] );
}

// Move users in
if ( $count_getUsers > 0 && $count_getUsers > $CHANNEL->user_in ) {
	$action_menu->add_link( '?action=move_users_in', $TEXT['move_user_in_chan'], IMG_UP_16 );
} else {
	$action_menu->add( $TEXT['move_user_in_chan'] );
}

// Link
if ( $count_getChannels > 1 && ( $count_getChannels - count( $CHANNEL->links ) ) > 1 ) {
	$action_menu->add_link( '?action=link_channel', $TEXT['link_channel'], 'tango/link_16.png' );
} else {
	$action_menu->add( $TEXT['link_channel'] );
}

// unlink ( memo: method is bugged before murmur 1.2.3 )
if ( empty( $CHANNEL->links ) OR $PMA->meta->int_version < 123 ) {
	$action_menu->add( $TEXT['unlink_channel'] );
} else {
	$action_menu->add_link( '?action=unlink_channel', $TEXT['unlink_channel'], IMG_CANCEL_16 );
}

// move channel
if ( $CHANNEL->id > 0 ) {
	$action_menu->add_link( '?action=move_channel', $TEXT['move_channel'], IMG_UP_16 );
} else {
	$action_menu->add( $TEXT['move_channel'] );
}

// Delete channel
if ( $CHANNEL->id > 0 ) {
	$action_menu->add_link( '?action=delete_channel', $TEXT['del_channel'], IMG_DELETE_16, 'onClick="return del_channel();"' );
} else {
	$action_menu->add( $TEXT['del_channel'] );
}

echo $action_menu->output();

foreach ( $CHANNEL->tabs as $tab => $txt ) {

	if ( $tab === $_SESSION['page_vserver']['cTab'] ) {
		$css = ' selected';
	} else {
		$css = '';
	}

	echo '<a class="tab'.$css.'" href="?cTab='.$tab.'">'.$txt.'</a>'.EOL;
}
echo '</div>'.EOL.EOL;
// MENU - END

if ( isset( $_GET['action'] ) ) {

	switch( $action = $_GET['action'] ) {

		case 'move_channel':
		case 'link_channel':
			echo PMA_output_actionBox::select_channel( $TEXT[ $action ] );
			break;

		case 'unlink_channel':
			echo PMA_output_actionBox::select_channel( $TEXT[ $action ], TRUE );
			break;

		case 'add_channel':
		case 'add_group':
		case 'delete_channel':
		case 'move_users_in':
		case 'move_users_out':
		case 'msg_channel':

			require 'actions/'.$action.'.php';

	}

} elseif ( $_SESSION['page_vserver']['cTab'] === 'acl' ) {

	require 'views/tab_ACL.php';

} elseif ( $_SESSION['page_vserver']['cTab'] === 'group' ) {

	require 'views/tab_group.php';

} elseif ( $_SESSION['page_vserver']['cTab'] === 'property' ) {

	require 'views/tab_property.php';
}

?>
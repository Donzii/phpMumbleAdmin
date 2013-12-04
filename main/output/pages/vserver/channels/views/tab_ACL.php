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

pma_load_language( 'vserver_acl' );

$getServer->getACL( $CHANNEL->id, $aclList, $groupList, $inherit );

// Deny SuperUser_ru to edit ACL owned by his uid on Root channel -> mark it as inherited.
if ( $PMA->user->is( CLASS_SUPERUSER_RU ) && $CHANNEL->id === 0 ) {

	foreach( $aclList as $key => $obj ) {

		if ( $obj->userid === $PMA->user->mumble_id && PMA_helpers_ACL::is_superuser_ru( $obj ) ) {
			$aclList[ $key ]->inherited = TRUE;
		}
	}
}

// Change acl ID
if ( isset( $_GET['acl'] ) ) {

	if ( $_GET['acl'] === 'deselect' ) {

		unset( $_SESSION['page_vserver']['aclID'] );

	} else {
		$_SESSION['page_vserver']['aclID'] = (int)$_GET['acl'];
	}
}

// Check for a valid acl ID
if ( isset( $_SESSION['page_vserver']['aclID'] ) ) {

	if ( $_SESSION['page_vserver']['aclID'] === -1 ) {
		// -1 is a valid acl ID
	} elseif ( ! isset( $aclList[ $_SESSION['page_vserver']['aclID'] ] ) ) {
		unset( $_SESSION['page_vserver']['aclID'] );
	}
}

if ( $inherit ) {
	$img = 'xchat/blue';
	$txt = $TEXT['inherit_parent_channel'];
} else {
	$img = 'pma/space';
	$txt = $TEXT['do_not_inherit'];
}

// ACL MENU
echo '<div class="toolbar small txtR">'.EOL;
if ( $CHANNEL->id > 0 ) {
	// Toggle inherit parent ACLs
	echo '<a href="?cmd=murmur_acl&amp;toggle_inherit_acl">'.HTML::img( $img.'.png', 'button left', $txt ).'</a>'.EOL;
}
// Add
echo '<a href="?cmd=murmur_acl&amp;add_acl">'.HTML::img( IMG_ADD_16, 'button', $TEXT['add_acl'] ).'</a>'.EOL;
echo '</div>'.EOL.EOL;

// ACL SELECTION
echo '<ul class="board">'.EOL;

// Default ACL
if ( isset( $_SESSION['page_vserver']['aclID'] ) && $_SESSION['page_vserver']['aclID'] === -1 ) {
	$css = 'inherited selected';
	$href = 'deselect';
} else {
	$css = 'inherited';
	$href = '-1';
}
echo '<li><a href="?acl='.$href.'" class="'.$css.'">'.HTML::img( 'tango/group_16.png' ).'All ( '.$TEXT['default_acl'].' )</a></li>'.EOL;

// ACLs list
foreach ( $aclList as $key => $obj ) {

	// Don't show inherited ACLs if inherit = FALSE
	// On Root channel, inherited ACLs are SuperUser_ru rule.
	if ( $CHANNEL->id > 0 && $obj->inherited && ! $inherit ) {
		continue;
	}

	$css = '';
	$href = $key;

	// Selected
	if ( isset( $_SESSION['page_vserver']['aclID'] ) && $key === $_SESSION['page_vserver']['aclID'] ) {

		$css = 'selected';
		$href = 'deselect';

		// Selected + inherited
		if ( $obj->inherited ) {
			$css .= ' inherited';
		}

	// inherited
	} elseif ( $obj->inherited ) {
		$css = 'inherited';
	}

	// Tokens
	if ( PMA_helpers_ACL::is_token( $obj ) OR PMA_helpers_ACL::is_deny_all_token( $obj ) ) {

		$img = 'gei/padlock_16';
		$name = $obj->group;

	// Group
	} elseif ( $obj->userid === -1 ) {

		$img = 'tango/group_16';
		$name = $obj->group;

	// user
	} elseif ( $obj->userid >= 0 ) {

		$img = 'mumble/user_auth';
		$name = $getServer->getRegistration( $obj->userid );
		$name = $name[0];

		// Show to SuperAdmins SuperUser_ru ACLs.
		if (
		$PMA->config->get( 'SU_ru_active' )
		&& $PMA->user->is_min( CLASS_ROOTADMIN )
		&& PMA_helpers_ACL::is_superuser_ru( $obj )
		) {
			$name .= ' ( '. pma_class_name( CLASS_SUPERUSER_RU ) .' )';
		}
	}

	echo '<li><a href="?acl='.$href.'" class="'.$css.'">'.HTML::img( $img.'.png' ).$name.'</a></li>'.EOL;
}

echo '</ul>'.EOL.EOL;

if ( isset( $_SESSION['page_vserver']['aclID'] ) ) {
	require 'tab_ACL_expand.php';
}

?>

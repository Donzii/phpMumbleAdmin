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

pma_load_language( 'vserver_group' );

$JS->add_text( 'add_group', $TEXT['add_group'] );

$getServer->getACL( $CHANNEL->id, $aclList, $groupList, $inherit );

// Construct the parent channel groups names
if ( $CHANNEL->id > 0 ) {

	$id = $getChannels[ $CHANNEL->id ]->parent;

	$getServer->getACL( $id, $foo, $parent_groupList, $foo );

} else {
	$parent_groupList = array();
}

$parent_group_names = array();

foreach( $parent_groupList as $obj ) {

	// Mumble seems to considere a modified group as inherited with this rule, so do like that.
	if ( $obj->inheritable ) {
		$parent_group_names[] = $obj->name;
	}
}

// Change group ID
if ( isset( $_GET['group'] ) ) {

	if ( $_GET['group'] === 'deselect' ) {

		unset( $_SESSION['page_vserver']['groupID'] );

	} else {
		$_SESSION['page_vserver']['groupID'] = $_GET['group'];
	}
}

// Check for a valid groupID
if ( isset( $_SESSION['page_vserver']['groupID'] ) ) {

	if ( ! isset( $groupList[ $_SESSION['page_vserver']['groupID'] ] ) ) {
		unset( $_SESSION['page_vserver']['groupID'] );
	}
}

// Add a group
echo '<div class="toolbar small txtR">'.EOL;
echo '<a onClick="return add_group();" href="?action=add_group">';
echo HTML::img( IMG_ADD_16, 'button', $TEXT['add_group'] ).'</a>'.EOL;
echo '</div>'.EOL;

// Select group menu
echo '<ul class="board">'.EOL;

if ( empty( $groupList ) ) {

	echo '<div class="empty">'.$TEXT['no_group'].'</div>';

} else {

	// uasort = preserve key
	uasort( $groupList, 'sort_obj_by_names' );

	foreach ( $groupList as $key => $obj ) {

		$css = '';
		$img = '';
		$href = '?group='.$key;
		$obj->modified = FALSE;

		// inherited, not modified
		if ( $obj->inherited ) {

			$img = 'inherited';

		// inherited modified
		} elseif ( in_array( $obj->name, $parent_group_names, TRUE ) ) {

			$obj->inherited = TRUE;
			$obj->modified = TRUE;
			$img = 'modified';
		}

		// created group
		if ( isset( $_SESSION['page_vserver']['groupID'] ) && $key == $_SESSION['page_vserver']['groupID'] ) {

			$css = 'selected';
			$href = '?group=deselect';
		}

		// Print group line menu
		echo '<li><a href="'.$href.'" class="'.$css.'">'.HTML::img( 'tango/group_16.png', $img ).html_encode( $obj->name ).'</a></li>'.EOL;
	}
}
echo '</ul>'.EOL.EOL;


if ( isset( $_SESSION['page_vserver']['groupID'] ) ) {
	require 'tab_group_expand.php';
}


?>

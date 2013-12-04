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

$GROUP = clone $groupList[ $_SESSION['page_vserver']['groupID'] ];

$getRegisteredUsers = $getServer->getRegisteredUsers( '' );

// expand menu
echo '<div class="toolbar small txtR">'.EOL;

// Delete, reset, none.
if ( $GROUP->inherited && ! $GROUP->modified ) {

	// inherited, not modified -> do nothing
	echo '<div class="left">'.$TEXT['inherited_group'].'</div>';

} else {

	if ( $GROUP->modified ) {

		$img = IMG_CLEAN_16;
		$title = $TEXT['reset_inherited_group'];

	} else {

		$GROUP->not_inherited = TRUE;
		$img = IMG_TRASH_16;
		$title = $TEXT['del_group'];
	}

	echo '<a href="?cmd=murmur_groups&amp;deleteGroup">'.HTML::img( $img, 'button left', $title ).'</a>'.EOL;
}

// Inherit
if ( $GROUP->inherit ) {
	$img = 'xchat/blue.png';
} else {
	$img = IMG_SPACE_16;
}
echo '<a href="?cmd=murmur_groups&amp;toggle_group_inherit">'.HTML::img( $img, 'button', $TEXT['inherit_parent_group'] ).'</a>'.EOL;

// Inheritable
if ( $GROUP->inheritable ) {
	$img = 'xchat/purple.png';
} else {
	$img = IMG_SPACE_16;
}
echo '<a href="?cmd=murmur_groups&amp;toggle_group_inheritable">'.HTML::img( $img, 'button', $TEXT['inheritable_sub'] ).'</a>'.EOL;

echo '</div>'.EOL;
// Menu - end

// Add user
echo '<form method="post" style="margin: 10px 0px;" action="" onSubmit="return unchanged( this.add_user );">'.EOL;
echo '<input type="hidden" name="cmd" value="murmur_groups">';
echo '<select id="add_user" name="add_user"><option value="">'.$TEXT['add_user_to_group'].'</option>';

foreach ( $getRegisteredUsers as $uid => $name ) {

	if (
		in_array( $uid, $GROUP->members, TRUE )
		OR in_array( $uid, $GROUP->remove, TRUE )
	) {
		continue;
	}
	echo '<option value="'.$uid.'">'.$uid.'# '.cut_long_str( $name, 40 ).'</option>';
}
echo '</select>';
echo '<input type="submit" value="'.$TEXT['add'].'" style="margin-left: 10px;"></form>'.EOL.EOL;

// Members menu
echo '<div><b>'.$TEXT['members'].'</b></div>'.EOL;

if ( count( $GROUP->add ) === 0 ) {

	echo '<div class="groupMembers"><div class="empty">'.$TEXT['no_member'].'</div></div>'.EOL;

} else {

	echo '<ul class="groupMembers">'.EOL;

	foreach ( $GROUP->add as $uid ) {

		$name = html_encode( $getRegisteredUsers[ $uid ] );

		echo '<li><a href="?cmd=murmur_groups&amp;removeMember='.$uid.'">';
		echo HTML::img( IMG_DELETE_16, 'button right', $TEXT['remove_member'] ).'</a>';
		echo '<span class="name">'.$name.'</span></li>'.EOL;
	}
	echo '</ul>'.EOL.EOL;
}

// No need to show inherited members and excluded members if the group is not inherited.
if ( isset( $GROUP->not_inherited ) ) {
	return;
}

// Inherited members menu
echo '<div><b>'.$TEXT['inherited_members'].'</b></div>'.EOL;

if ( count( $GROUP->members ) === count( $GROUP->add ) ) {

	echo '<div class="groupMembers"><div class="empty">'.$TEXT['no_member'].'</div></div>'.EOL;

} else {

	echo '<ul class="groupMembers">'.EOL;

	foreach ( $GROUP->members as $uid ) {

		$name = html_encode( $getRegisteredUsers[ $uid ] );

		// inherited member
		if ( ! in_array( $uid, $GROUP->add, TRUE ) ) {

			if ( ! $GROUP->inherit OR ! $GROUP->inherited ) {
				// disable inherited members
				echo '<li class="disabled" title="'.$TEXT['inherited_members'].'"><span class="name">'.$name.'</span></li>'.EOL;

			} else {
				echo '<li><a href="?cmd=murmur_groups&amp;excludeMember='.$uid.'">';
				echo HTML::img( IMG_DOWN_16, 'button right', $TEXT['exclude_inherited'] ).'</a><span class="name">'.$name.'</span></li>'.EOL;
			}
		}
	}
	echo '</ul>'.EOL.EOL;
}

// Excluded member menu
echo '<div class="b">'.$TEXT['excluded_members'].'</div>'.EOL;

if ( count( $GROUP->remove ) === 0 ) {
	echo '<div class="groupMembers"><div class="empty">'.$TEXT['no_excluded'].'</div></div>'.EOL;

} else {

	echo '<ul class="groupMembers">'.EOL;

	foreach ( $GROUP->remove as $uid ) {

		$name = html_encode( $getRegisteredUsers[ $uid ] );

		if ( ! $GROUP->inherit OR ! $GROUP->inherited ) {
			// disable inherited members
			echo '<li class="disabled" title="'.$TEXT['inherited_members'].'"><span class="name">'.$name.'</span></li>'.EOL;

		} else {
			echo '<li><a href="?cmd=murmur_groups&amp;removeExcluded='.$uid.'">';
			echo HTML::img( IMG_UP_16, 'button right', $TEXT['remove_excluded'] ).'</a>';
			echo '<span class="name">'.$name.'</span></li>'.EOL;
		}
	}
	echo '</ul>'.EOL.EOL;
}


?>

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

function print_acl_permissions( $obj, $bit, $desc, $inherited ) {

	$allow = bitmask_decompose( $obj->allow );
	$deny = bitmask_decompose( $obj->deny );

	$deny_checked = HTML::chked( in_array( $bit, $deny, TRUE ) );
	$allow_checked = HTML::chked( in_array( $bit, $allow, TRUE ) );

	if ( $inherited ) {
		$deny_chkbox = '<input type="checkbox" disabled="disabled" '.$deny_checked.'>';
		$allow_chkbox = '<input type="checkbox"  disabled="disabled" '.$allow_checked.'>';
	} else {
		$deny_chkbox = '<input type="checkbox" name="DENY['.$bit.']" value="'.$bit.'" '.$deny_checked.' onClick="uncheck( \'ALLOW['.$bit.']\' )">';
		$allow_chkbox = '<input type="checkbox" name="ALLOW['.$bit.']" value="'.$bit.'" '.$allow_checked.' onClick="uncheck( \'DENY['.$bit.']\' )">';
	}

	// Print table
	echo '<tr><th>'.$desc.'</th><td>'.$deny_chkbox.'</td><td>'.$allow_chkbox.'</td></tr>'.EOL;
}

$permissions[1] = $TEXT['acl_write'];
$permissions[2] = $TEXT['acl_traverse'];
$permissions[4] = $TEXT['acl_enter'];
$permissions[8] = $TEXT['acl_speak'];
$permissions[16] = $TEXT['acl_speak'];
$permissions[32] = $TEXT['acl_move'];
$permissions[64] = $TEXT['acl_make'];
$permissions[128] = $TEXT['acl_link'];
$permissions[256] = $TEXT['acl_wisp'];
$permissions[512] = $TEXT['acl_txt'];
$permissions[1024] = $TEXT['acl_temporary'];
$permissions_root[65536] = $TEXT['acl_kick'];
$permissions_root[131072] = $TEXT['acl_ban'];
$permissions_root[262144] = $TEXT['acl_register'];
$permissions_root[524288] = $TEXT['acl_register_self'];

$common_groups[] = 'all';
$common_groups[] = 'auth';
$common_groups[] = 'in';
$common_groups[] = 'sub';
$common_groups[] = 'out';
$common_groups[] = '~in';
$common_groups[] = '~sub';
$common_groups[] = '~out';

// Get the ACL object
if ( $_SESSION['page_vserver']['aclID'] === -1 ) {

	// Create the default "All" ACL
	$ACL = new Murmur_acl();
	$ACL->allow = 782;
	$ACL->deny = 984305;
	$ACL->inherited = TRUE;
	$ACL->applyHere = TRUE;
	$ACL->applySubs = TRUE;
	$ACL->userid = -1;
	$ACL->group = 'all';

} else {
	$ACL = $aclList[ $_SESSION['page_vserver']['aclID'] ];
}

if ( $ACL->inherited ) {
	$css = 'class="disabled"';
	$disabled = 'disabled="disabled"';
} else {
	$css = '';
	$disabled = '';
}

echo '<div class="toolbar small txtR">'.EOL;

// Remove, down, up
if ( $_SESSION['page_vserver']['aclID'] >= 0 && ! $ACL->inherited ) {
	echo '<a href="?cmd=murmur_acl&amp;delete_acl">'.HTML::img( IMG_TRASH_16, 'button left', $TEXT['del_rule'] ).'</a>'.EOL;
	echo '<a href="?cmd=murmur_acl&amp;down_acl">'.HTML::img( IMG_DOWN_16, 'button', $TEXT['down_rule'] ).'</a>'.EOL;
	echo '<a href="?cmd=murmur_acl&amp;up_acl">'.HTML::img( IMG_UP_16, 'button', $TEXT['up_rule'] ).'</a>'.EOL;
}

echo '</div>'.EOL.EOL;

// Permissions table
echo '<form id="ACL" '.$css.' method="post" action="" onSubmit="return form_is_modified( this );">'.EOL;
echo '<input type="hidden" name="cmd" value="murmur_acl">';

if ( ! $ACL->inherited ) {
	echo '<input type="hidden" name="edit_acl">'.EOL;
}

// Group
echo '<select id="groups" name="group" '.$disabled.' onChange="unselect( \'users\' )">';
echo '<option value="">'.$TEXT['select_group'].'</option>';

if ( ! $ACL->inherited ) {

	foreach ( $common_groups as $group ) {
		echo '<option value="'.$group.'">'.$group.'</option>';
	}

	// Custom groups
	if ( count( $groupList ) > 0 ) {

		echo '<option value="" disabled="disabled">-</option>';

		foreach ( $groupList as $group ) {
			echo '<option value="'.$group->name.'">'.cut_long_str( $group->name, 40 ).'</option>';
		}
	}
}
echo '</select>'.EOL;


// User
echo '<select id="users" name="user" '.$disabled.' onChange="unselect( \'groups\' )">';
echo '<option value="">'.$TEXT['select_user'].'</option>';

if ( ! $ACL->inherited ) {

	$getRegisteredUsers = $getServer->getRegisteredUsers( '' );

	foreach ( $getRegisteredUsers as $uid => $name ) {
		echo '<option value="'.$uid.'">'.$uid.' # '.cut_long_str( $name, 40 ).'</option>';
	}
}
echo '</select>'.EOL;

echo '<table>'.EOL;

// Apply to this channel
$checked = HTML::chked( $ACL->applyHere );
echo '<tr><th colspan="2"><label for="applyHere">'.$TEXT['apply_this_channel'].'</label></th><td>';
echo '<input type="checkbox" id="applyHere" name="applyHere" value="TRUE" '.$disabled.' '.$checked.'></td></tr>'.EOL;

// Apply to subs channels
$checked = HTML::chked( $ACL->applySubs );
echo '<tr><th colspan="2"><label for="applySubs">'.$TEXT['apply_sub_channel'].'</label></th><td>';
echo '<input type="checkbox" id="applySubs" name="applySubs" value="TRUE" '.$disabled.' '.$checked.'></td></tr>'.EOL;

echo '<tr><th>'.$TEXT['permissions'].'</th><th>'.$TEXT['deny'].'</th><th>'.$TEXT['allow'].'</th></tr>'.EOL;

foreach ( $permissions as $bit => $desc ) {
	print_acl_permissions( $ACL, $bit, $desc, $ACL->inherited );
}

if ( $CHANNEL->id === 0 ) {

	echo '<tr><th colspan="3">'.$TEXT['specific_root'].'</th></tr>';

	foreach ( $permissions_root as $bit => $desc ) {
		print_acl_permissions( $ACL, $bit, $desc, $ACL->inherited );
	}
}

echo '<tr><th colspan="3"><input type="submit" '.$disabled.' value="'.$TEXT['apply'].'"></th></tr>'.EOL;

echo '</table>'.EOL;
echo '</form>'.EOL.EOL;

?>

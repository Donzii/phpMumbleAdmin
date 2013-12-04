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

$is_default = ( DEFAULT_CHANNEL_ID === $CHANNEL->id );

$disabled = HTML::disabled( $is_default OR $CHANNEL->temporary );

if ( $CHANNEL->description === '' ) {
	$desc = '<div class="empty">'.$TEXT['no_comment'].'</div>';
} else {
	$desc = $CHANNEL->description;
}

$getServer->getACL( $CHANNEL->id, $aclList, $groupList, $inherit );

$password = '';
foreach ( $aclList as $obj ) {

	if ( ! $obj->inherited && PMA_helpers_ACL::is_token( $obj ) ) {

		$password = substr( $obj->group, 1 );
		break;
	}
}

echo '<form method="post" class="oBox" action="" onSubmit="return form_is_modified( this );">'.EOL;
echo '<input type="hidden" name="cmd" value="murmur_channel">'.EOL;
echo '<input type="hidden" name="channel_property">'.EOL;

// Default channel checkbox
echo '<div><label for="defaultchannel"><b>'.$TEXT['set_as_defaultchannel'].'</b></label>'.EOL;
echo '<input type="checkbox" id="defaultchannel" name="defaultchannel" '.HTML::chked( $is_default ).' '.$disabled.'></div>'.EOL;

// Channel name
echo '<div style="height: 40px; margin: 10px 0px;">'.EOL;
if ( $CHANNEL->id > 0 ) {
	echo '<div><b>'.$TEXT['channel_name'].'</b></div>'.EOL;
	echo '<div><input type="text" name="name" value="'.$CHANNEL->name.'"></div>'.EOL;
}
echo '</div>'.EOL;

// Channel description
echo '<div><b>'.$TEXT['channel_desc'].'</b></div>'.EOL;
echo '<div class="description">'.$desc.'</div>'.EOL;
echo '<div style="margin: 10px 0px;"><textarea name="description" cols="4" rows="6">'.$CHANNEL->description.'</textarea></div>'.EOL;

// Channel Password
echo '<div><b>'.$TEXT['channel_pw'].'</b></div>'.EOL;
echo '<div><input type="text" name="pw" value="'.$password.'"></div>'.EOL;

// Channel position
echo '<div><b>'.$TEXT['channel_pos'].'</b></div>'.EOL;
echo '<div><input type="text" name="position" value="'.$CHANNEL->position.'"></div>'.EOL;

echo '<div style="margin: 10px 0px; text-align: right;"><input type="submit" value="'.$TEXT['apply'].'"></div>'.EOL;
echo '</form>'.EOL.EOL;

?>

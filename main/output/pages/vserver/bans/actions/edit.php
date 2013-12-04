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

if ( ! isset( $getBans[ $_GET['edit_ban_id'] ] ) ) {
	pma_redirect();
}

$ban = $getBans[ $_GET['edit_ban_id'] ];

$ip = ip_dec_to_str( $ban->address );

if ( $ip['type'] === 'ipv4' ) {
	$mask = ip_mask_6to4( $ban->bits );
} else {
	$mask = $ban->bits;
}

// Certificate remove button
if ( $ban->hash !== '' ) {

	$hash = '<a href="?cmd=murmur_bans&amp;remove_ban_hash='.$_GET['edit_ban_id'].'">';
	$hash .= HTML::img( IMG_TRASH_16, 'button right', 'Remove ban certificate' ).'</a>';
	$hash .= $ban->hash;

} else {
	$hash = $TEXT['none'];
}

$start = PMA_helpers_dates::complet( $ban->start );

if ( $ban->duration !== 0 ) {

	$end = PMA_helpers_dates::complet( $ban->start + $ban->duration );
	$checked = FALSE;

} else {

	$end = $TEXT['permanent'];
	$checked = TRUE;
}

// ToolBar
echo '<div class="toolbar">'.EOL;
echo '<a href="./">'.HTML::img( IMG_CANCEL_22, 'button right', $TEXT['cancel'] ).'</a>'.EOL;
echo '</div>'.EOL.EOL;

echo '<form method="post" action="" id="setBan" onSubmit="return validate_ban( this );">'.EOL;

echo '<input type="hidden" name="cmd" value="murmur_bans">';
echo '<input type="hidden" name="edit_ban_id" value="'.$_GET['edit_ban_id'].'">'.EOL;

echo '<table class="config oBox">'.EOL;

// title
echo '<tr><th class="title">'.$TEXT['edit_ban'].'</th></tr>'.EOL;

// ip
echo '<tr><th><label for="ip2">'.$TEXT['ip_addr'].'</label></th>';
echo '<td><input type="text" id="ip2" name="ip" value="'.$ip['ip'].'"></td></tr>'.EOL;

// mask
echo '<tr><th><label for="mask">'.$TEXT['bitmask'];
echo HTML::info_bubble( HTML::img( IMG_INFO_16 ), $TEXT['bitmask_info'] ).'</label></th>';
echo '<td><input type="text" id="mask" name="mask" maxlength="3" style="width: 30px" value="'.$mask.'"></td></tr>'.EOL;

// user name
echo '<tr><th><label for="name">'.$TEXT['login'].'</label></th>';
echo '<td><input type="text" id="name" name="name" value="'.$ban->name.'"></td></tr>'.EOL;

// reason
echo '<tr><th><label for="reason">'.$TEXT['reason'].'</label></th>';
echo '<td><textarea id="reason" name="reason" cols="4" rows="6">'.$ban->reason.'</textarea></td></tr>'.EOL;

echo '<tr><th>'.$TEXT['cert_hash'].'</th><td>'.$hash.'</td></tr>'.EOL;

// Start
echo '<tr><th>'.$TEXT['started'].'</th><td>'.$start.'</td></tr>'.EOL;

// end
echo '<tr><th>'.$TEXT['end'].'</th><td>'.$end.'</td></tr>'.EOL;

// duration
echo '<tr><th colspan="2">'.PMA_output_bans::duration( $ban, $checked ).'</th></tr>'.EOL;

// Submit
echo '<tr><th colspan="2"><input type="submit" value="'.$TEXT['submit'].'"></th></tr>'.EOL;

echo '</table>'.EOL;
echo '</form>'.EOL.EOL;

?>

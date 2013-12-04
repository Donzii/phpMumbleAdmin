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

// ToolBar
echo '<div class="toolbar">'.EOL;
echo '<a href="./">'.HTML::img( IMG_CANCEL_22, 'button right', $TEXT['cancel'] ).'</a>'.EOL;
echo '</div>'.EOL.EOL;

echo '<form method="post" action="" id="setBan" onSubmit="return validate_ban( this );">'.EOL;
echo '<input type="hidden" name="cmd" value="murmur_bans">';
echo '<input type="hidden" name="addBan">'.EOL;
echo '<input type="hidden" name="hash">'.EOL;

echo '<table class="config oBox">'.EOL;
// title
echo '<tr><th class="title">'.$TEXT['add_ban'].'</th></tr>'.EOL;
// ip
echo '<tr><th><label for="ip">'.$TEXT['ip_addr'].'</label></th><td><input type="text" id="ip" name="ip"></td></tr>'.EOL;
echo '<tr><th colspan="2"><input type="submit" value="'.$TEXT['submit'].'"></th></tr>'.EOL;
echo '<tr class="pad"><td class="hide"></td></tr>'.EOL;
// mask
echo '<tr><th><label for="mask">'.$TEXT['bitmask'];
echo HTML::info_bubble( HTML::img( IMG_INFO_16 ), $TEXT['bitmask_info'] ).'</label></th>';
echo '<td><input type="text" id ="mask" name="mask" maxlength="3" style="width: 30px"></td></tr>'.EOL;
// user name
echo '<tr><th><label for="name">'.$TEXT['login'].'</label></th><td><input type="text" id="name" name="name"></td></tr>'.EOL;
// reason
echo '<tr><th><label for="reason">'.$TEXT['reason'].'</label></th>';
echo '<td><textarea id="reason" name="reason" cols="4" rows="6"></textarea></td></tr>'.EOL;
// duration
echo '<tr><th colspan="2">'.PMA_output_bans::duration().'</th></tr>'.EOL;

echo '</table>'.EOL;
echo '</form>'.EOL.EOL;

?>

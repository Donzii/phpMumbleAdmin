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


if ( $uSess->comment === '' ) {
	$comment = '<div class="empty">'.$TEXT['no_comment'].'</div>';
} else {
	$comment = $uSess->comment;
}

echo '<div class="oBox">'.EOL;
echo '<div class="description">'.$comment.'</div>'.EOL;
echo '<form method="post" action="" onSubmit="return unchanged( this.change_user_comment );">'.EOL;
echo '<input type="hidden" name="cmd" value="murmur_users_sessions">';
echo '<div style="margin: 10px 0px;"><textarea name="change_user_comment" cols="4" rows="6">';
echo html_encode( $uSess->comment, FALSE ).'</textarea></div>'.EOL;
echo '<div class="txtR"><input type="submit" value="'.$TEXT['modify'].'"></div>'.EOL;
echo '</form>'.EOL;
echo '</div>'.EOL.EOL;

?>

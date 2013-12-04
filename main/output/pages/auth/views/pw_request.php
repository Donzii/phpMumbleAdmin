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

echo '<form method="post" class="actionBox auth" action="" onSubmit="return validate_gen_passw( this );">'.EOL;
echo '<input type="hidden" name="cmd" value="pw_requests">';

echo '<h1><label for="login">'.$TEXT['gen_pw'].'</label></h1>';

echo '<table class="config oBox pad">'.EOL;

echo '<tr><th><label for="login">'.$TEXT['login'].'</label></th><td><input type="text" id="login" name="login"></td></tr>'.EOL;

echo '<tr><th><label for="server">'.$TEXT['server'].'</label></th><td>'.server_field().'</td></tr>'.EOL;
echo '<tr><th colspan="2"><input type="submit" value="'.$TEXT['submit'].'"></th></tr>'.EOL;

echo '</table>'.EOL;

echo '<div class="txtR b"><a href="./">'.$TEXT['cancel'].'</a></div>'.EOL;

echo '</form>'.EOL.EOL;

?>

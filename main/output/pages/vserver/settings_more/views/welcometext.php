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

echo '<div class="description">'.EOL;
echo $welcometext.EOL;
echo '</div>'.EOL;
echo '<form method="post" style="margin: 20px 0px;" action="" onSubmit="return unchanged( this.welcometext );">'.EOL;
echo '<input type="hidden" name="cmd" value="murmur_settings">'.EOL;
echo '<input type="hidden" name="setConf">'.EOL;
echo '<textarea name="welcometext" rows="10" cols="4">'.html_encode( $welcometext, FALSE ).'</textarea>'.EOL;
echo '<div style="margin: 10px 0px;" class="txtR"><input type="submit" value="'.$TEXT['apply'].'"></div>'.EOL;
echo '</form>'.EOL.EOL;

?>

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

$enable = $PMA->config->get( 'external_viewer_enable' );

echo '<form method="post" action="" onSubmit="return form_is_modified( this );">'.EOL;

echo '<input type="hidden" name="cmd" value="config">';
echo '<input type="hidden" name="set_settings_ext_viewer">'.EOL;

echo '<table class="config oBox">'.EOL;

echo '<tr class="pad"><th class="title"></th></tr>'.EOL;
echo '<tr><th><label for="enable">'.$TEXT['external_viewer_enable'].'</label></th>';
echo '<td>';
if ( $enable ) {
	echo '<div class="right">';
	echo '<a href="'.PMA_HTTP_HOST.PMA_HTTP_PATH.'?ext_viewer&amp;profile='.$PMA->user->profile_id.'&amp;server=*">';
	echo $TEXT['see_external_viewer'].'</a></div>';
}
echo '<input type="checkbox" '.HTML::chked( $enable ).' id="enable" name="enable"></td></tr>'.EOL;

echo '<tr><th><label for="width">'.$TEXT['external_viewer_width'].'</label></th>';
echo '<td><input type="text" id="width" name="width" value="'.$PMA->config->get( 'external_viewer_width' ).'"></td></tr>'.EOL;
echo '<tr><th><label for="height">'.$TEXT['external_viewer_height'].'</label></th>';
echo '<td><input type="text" id="height" name="height" value="'.$PMA->config->get( 'external_viewer_height' ).'"></td></tr>'.EOL;

echo '<tr><th><label for="vertical">'.$TEXT['external_viewer_vertical'].'</label></th>';
echo '<td><input type="checkbox" '.HTML::chked( $PMA->config->get( 'external_viewer_vertical' ) ).' id="vertical" name="vertical"></td></tr>'.EOL;
echo '<tr><th><label for="scroll">'.$TEXT['external_viewer_scroll'].'</label></th>';
echo '<td><input type="checkbox" '.HTML::chked( $PMA->config->get( 'external_viewer_scroll' ) ).' id="scroll" name="scroll"></td></tr>'.EOL;

echo '<tr><th colspan="2"><input type="submit" value="'.$TEXT['apply'].'"></th></tr>'.EOL;

echo '</table>'.EOL;
echo '</form>'.EOL.EOL;


?>
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

echo '<form method="post" action="" onSubmit="return form_is_modified( this );">'.EOL;

echo '<input type="hidden" name="cmd" value="config">'.EOL;
echo '<input type="hidden" name="set_settings_general">'.EOL;

echo '<table class="config oBox">'.EOL;

echo '<tr class="pad"><th class="title"></th></tr>'.EOL;

// Title
echo '<tr><th><label for="title">'.$TEXT['site_title'].'</label></th>';
echo '<td><input type="text" id="title" name="title" value="'.html_encode( $PMA->config->get( 'siteTitle' ), FALSE ).'">';

// Comment
echo '<tr><th><label for="comment">'.$TEXT['site_desc'].'</label></th>';
echo '<td><input type="text" id="comment" name="comment" value="'.html_encode( $PMA->config->get( 'siteComment' ), FALSE ).'">';

// Session logout
echo '<tr><th><label for="auto_logout">'.$TEXT['autologout'].'</label></th>';
echo '<td><input type="text" maxlength="2" style="width: 50px" id="auto_logout" name="auto_logout" value="'.$PMA->config->get( 'auto_logout' ).'"></td></tr>'.EOL;

// Check for update
echo '<tr><th><label for="check_update">'.$TEXT['autocheck_update'];
echo HTML::info_bubble( HTML::img( IMG_INFO_16 ), $TEXT['autocheck_update_info'] ).'</label></th>';
echo '<td><input type="text" maxlength="2" style="width: 50px" id="check_update" name="check_update" value="'.$PMA->config->get( 'update_check' ).'">';
echo '<a href="?cmd=config&amp;check_for_update" style="margin: 0px 20px;">'.$TEXT['check_update'].'</a>';
if ( PMA_DEBUG > 0 ) {
	echo '<a href="?cmd=config&amp;check_for_update=debug">debug</a>';
}
echo '</td></tr>'.EOL;

// Mumble version url
echo '<tr><th><label for="murmur_vers_url">'.$TEXT['inc_murmur_vers'].'</label>';
echo HTML::info_bubble( HTML::img( IMG_INFO_16 ), $TEXT['inc_murmur_vers_info'] ).'</th>';
echo '<td><input type="checkbox" '.HTML::chked( $PMA->config->get( 'murmur_version_url' ) ).' id="murmur_vers_url" name="murmur_vers_url"></td></tr>'.EOL;

echo '<tr class="pad"><td class="hide"></td></tr>'.EOL;

// Drop-down servers list
echo '<tr class="pad"><th class="title">'.$TEXT['srv_dropdown_list'].'</th></tr>'.EOL;
echo '<tr><th><label for="auth_list">'.$TEXT['activate_auth_dropdown'].'</label>';
echo HTML::info_bubble( HTML::img( IMG_INFO_16 ), $TEXT['activate_auth_dropdown_info'] ).'</th>';
echo '<td><input type="checkbox" '.HTML::chked( $PMA->config->get( 'ddl_auth_page' ) ).' id="auth_list" name="activate_for_auth"></td></tr>'.EOL;
echo '<tr><th><label for="refreshTime">'.$TEXT['refresh_ddl_cache'].'</label></th>';
echo '<td><input type="text" maxlength="6" style="width: 50px" id="refreshTime" name="refreshTime" value="'.$PMA->config->get( 'ddl_refresh' ).'"> ';
echo $TEXT['disable_function'].'</td></tr>'.EOL;
echo '<tr><th><label for="show_uptime">'.$TEXT['ddl_show_cache_uptime'].'</label></th>';
echo '<td><input type="checkbox" '.HTML::chked( $PMA->config->get( 'ddl_show_cache_uptime' ) ).' id="show_uptime" name="show_uptime"></td></tr>'.EOL;

echo '<tr class="pad"><td class="hide"></td></tr>'.EOL;

echo '<tr class="pad"><th class="title"></th></tr>'.EOL;
echo '<tr><th><label for="show_avatar_sa">'.$TEXT['show_avatar'].'</label></th>';
echo '<td><input type="checkbox" '.HTML::chked( $PMA->config->get( 'show_avatar_sa' ) ).' id="show_avatar_sa" name="show_avatar_sa"></td></tr>'.EOL;

// Apply
echo '<tr><th colspan="2"><input type="submit" value="'.$TEXT['apply'].'"></th></tr>'.EOL;
echo '</table>'.EOL;
echo '</form>'.EOL.EOL;

?>
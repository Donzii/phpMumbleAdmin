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

$overview = $PMA->config->get( 'table_overview' );
$users = $PMA->config->get( 'table_users' );
$bans = $PMA->config->get( 'table_bans' );

$tables_infos = $TEXT['tables_infos'];

$style = 'style="width: 40px"';

echo '<form method="post" action="" onSubmit="return form_is_modified( this );">'.EOL;

echo '<input type="hidden" name="cmd" value="config">';
echo '<input type="hidden" name="set_settings_tables">'.EOL;

echo '<table class="config oBox">'.EOL;

echo '<tr class="pad"><th class="title">'.$TEXT['tables'].'</th></tr>'.EOL;

echo '<tr><th><label for="overview">'.$TEXT['overview_table_lines'].'</label></th>';
echo '<td><input type="text" maxlength="4" '.$style.' id="overview" name="overview" value="'.$overview.'"> '.$tables_infos.'</td></tr>'.EOL;

echo '<tr><th><label for="users">'.$TEXT['users_table_lines'].'</label></th>';
echo '<td><input type="text" maxlength="4" '.$style.' id="users" name="users" value="'.$users.'"> '.$tables_infos.'</td></tr>'.EOL;

echo '<tr><th><label for="bans">'.$TEXT['ban_table_lines'].'</label></th>';
echo '<td><input type="text" maxlength="4" '.$style.' id="bans" name="bans" value="'.$bans.'"> '.$tables_infos.'</td></tr>'.EOL;

echo '<tr class="pad"><td class="hide"></td></tr>'.EOL;
// Overview table

echo '<tr class="pad"><th class="title">'.$TEXT['overview_table'].'</th></tr>'.EOL;
echo '<tr><th><label for="set1">'.$TEXT['enable_users_total'].'</label></th>';
echo '<td><input type="checkbox" '.HTML::chked( $PMA->config->get( 'show_total_users' ) ).' id="set1" name="set1">';
echo '<label for="set2" style="margin-left: 20px;">'.$TEXT['sa_only'].'</label>';
echo '<input type="checkbox" '.HTML::chked( $PMA->config->get( 'show_total_users_sa' ) ).' id="set2" name="set2">';
echo '</td></tr>'.EOL;

echo '<tr><th><label for="set3">'.$TEXT['enable_connected_users'].'</label></th>';
echo '<td><input type="checkbox" '.HTML::chked( $PMA->config->get( 'show_online_users' ) ).' id="set3" name="set3">';
echo '<label for="set4" style="margin-left: 20px;">'.$TEXT['sa_only'].'</label>';
echo '<input type="checkbox" '.HTML::chked( $PMA->config->get( 'show_online_users_sa' ) ).' id="set4" name="set4">';
echo '</td></tr>'.EOL;

echo '<tr><th><label for="set5">'.$TEXT['enable_vserver_uptime'].'</label></th>';
echo '<td><input type="checkbox" '.HTML::chked( $PMA->config->get( 'show_uptime' ) ).' id="set5" name="set5">';
echo '<label for="set6" style="margin-left: 20px;">'.$TEXT['sa_only'].'</label>';
echo '<input type="checkbox" '.HTML::chked( $PMA->config->get( 'show_uptime_sa' ) ).' id="set6" name="set6">';
echo '</td></tr>'.EOL;

echo '<tr><th colspan="2"><input type="submit" value="'.$TEXT['apply'].'"></th></tr>'.EOL;

echo '</table>'.EOL;
echo '</form>'.EOL.EOL;


?>
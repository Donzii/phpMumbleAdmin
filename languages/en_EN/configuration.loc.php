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

$TEXT['tab_options'] = 'Options';
$TEXT['tab_ICE'] = 'ICE';
$TEXT['tab_settings'] = 'Settings';
$TEXT['tab_debug'] = 'Debug';

// Tab options
$TEXT['select_lang'] = 'Select a language';
$TEXT['select_style'] = 'Select a style';
$TEXT['select_time'] = 'Select local time';
$TEXT['time_format'] = 'Time format';
$TEXT['date_format'] = 'Date format';
$TEXT['select_locales_profile'] = 'Select locales profile';
$TEXT['uptime_format'] = 'Uptime format';
$TEXT['conn_login'] = 'Connexion login';
$TEXT['conn_login_info'] = 'This option let you choose the login name you want to connect to servers';

$TEXT['default_options'] = 'Default options values';
$TEXT['default_lang'] = 'Default language';
$TEXT['default_style'] = 'Default style';
$TEXT['default_time'] = 'Default local time';
$TEXT['default_time_format'] = 'Default time format';
$TEXT['default_date_format'] = 'Default date format';
$TEXT['default_locales'] = 'Default locales informations';
$TEXT['add_locales_profile'] = 'Add a locales informations profile';
$TEXT['del_locales_profile'] = 'Delete a locales informations profile';

$TEXT['sa_login'] = 'SuperAdmin login';
$TEXT['change_your_pw'] = 'Change your password';
$TEXT['enter_your_pw'] = 'Enter your password';

// Tab ICE
$TEXT['profile_name'] = 'Profile name';
$TEXT['ICE_host'] = 'ICE interface host';
$TEXT['ICE_port'] = 'ICE interface port';
$TEXT['ICE_timeout'] = 'Timeout in seconds';
$TEXT['ICE_secret'] = 'ICE password';
$TEXT['slice_profile'] = 'Slice profile';
$TEXT['slice_php_file'] = 'Slice php file';
$TEXT['conn_url'] = 'Connection URL';
$TEXT['conn_url_info'] = 'PMA permit to connect to a virtual server with the IP set in "host" parameter. This parameter permit to override it with a hostname or an IP.';
$TEXT['public_profile'] = 'Public profile';
$TEXT['default_ICE_profile'] = 'Set as the default ICE profile';
$TEXT['add_ICE_profile'] = 'Add an ICE profile';
$TEXT['del_profile'] = 'Delete the profile';
$TEXT['confirm_del_ICE_profile'] = 'Do you confirm to delete this ICE profile ?';
$TEXT['enable_profile'] = 'Click to enable the profile';

// Tab settings
$TEXT['mumble_accounts'] = 'Mumble accounts';

$TEXT['disable_function'] = '0 disable this function';

$TEXT['site_title'] = 'Site title';
$TEXT['site_desc'] = 'Site description';
$TEXT['autologout'] = 'Auto-logout in minutes ( 5 - 30 )';
$TEXT['autocheck_update'] = 'Auto-check for update';
$TEXT['autocheck_update_info'] = 'in days: 0 - 31<br>0 disable this function';
$TEXT['check_update'] = 'Check for update';
$TEXT['inc_murmur_vers'] = 'Include the murmur version in connection URL';
$TEXT['inc_murmur_vers_info'] = 'An oldest mumble client will not be able to connect to servers with connection URL';

$TEXT['show_avatar'] = 'Show avatars to SuperAdmins only';

$TEXT['activate_su_login'] = 'Authorize SuperUsers to connect to PMA';
$TEXT['activate_su_modify_pw'] = 'Authorize SuperUsers to change registered users password';
$TEXT['activate_su_vserver_start'] = 'Authorize SuperUsers to start / stop the virtual server';
$TEXT['activate_su_ru'] = 'Activate SuperUser_ru class';
$TEXT['activate_su_ru_info'] = 'Give SuperUser rights to registered users under conditions ( See README.txt )';
$TEXT['reg_users'] = 'Registered users';
$TEXT['activate_ru_login'] = 'Authorize registered users to connect to PMA';
$TEXT['activate_ru_del_account'] = 'Authorize registered users to delete their account';
$TEXT['activate_ru_modify_login'] = 'Authorize registered users to change their login credentials';

$TEXT['vservers_logs'] = 'Virtual servers logs';
$TEXT['srv_logs_amount'] = 'Amount of logs PMA have to request to servers';
$TEXT['activate_vservers_logs_for_adm'] = 'Activate logs tab for admins and SuperUsers';
$TEXT['activate_adm_highlight_logs'] = 'Authorize admins and SuperUsers to highlight logs';

$TEXT['pma_logs'] = 'PMA logs';
$TEXT['pma_logs_infos'] = 'Options for SuperAdmin only';
$TEXT['logs_sa_actions'] = 'Log SuperAdmin actions ( RootAdmin excluded )';
$TEXT['pma_logs_clean'] = 'Clean old logs ( in days )';

$TEXT['tables'] = 'Tables';
$TEXT['overview_table_lines'] = 'Amount of lines for servers table';
$TEXT['users_table_lines'] = 'Amount of lines for registered users table';
$TEXT['ban_table_lines'] = 'Amount of lines for bans table';
$TEXT['tables_infos'] = '10 - 1000 ( 0 disable table pages )';

$TEXT['overview_table'] = 'Overview table';
$TEXT['enable_users_total'] = 'Show total of users';
$TEXT['enable_connected_users'] = 'Show connected users';
$TEXT['enable_vserver_uptime'] = 'Show vservers uptime';
$TEXT['sa_only'] = 'SuperAdmins only';

$TEXT['srv_dropdown_list'] = 'Servers drop-down list';
$TEXT['activate_auth_dropdown'] = 'Activate the servers drop-down list for the authentication page';
$TEXT['activate_auth_dropdown_info'] = 'This means that all accessible server IDs are printed in the HTML source code of the authentication page';
$TEXT['refresh_ddl_cache'] = 'Time in hour before automatically refresh the servers drop-down list cache';
$TEXT['ddl_show_cache_uptime'] =  'Show cache uptime';

$TEXT['autoban'] = 'Autoban';
$TEXT['autoban_attemps'] = 'Attempts limits';
$TEXT['autoban_frame'] = 'Attempts time frame ( in seconds )';
$TEXT['autoban_duration'] = 'Ban time ( in seconds )';

$TEXT['smtp_srv'] = 'Smtp server';
$TEXT['host'] = 'Host';
$TEXT['port'] = 'port';
$TEXT['default_sender_email'] = 'Default sender email';

$TEXT['external_viewer'] = 'External viewer';
$TEXT['see_external_viewer'] = 'See external viewer';
$TEXT['external_viewer_enable'] = 'Enable external viewers';
$TEXT['external_viewer_width'] = 'Viewers width';
$TEXT['external_viewer_height'] = 'Viewers height';
$TEXT['external_viewer_vertical'] = 'Align verticaly viewers';
$TEXT['external_viewer_scroll'] = 'Enable viewers scrollbars';

// Generate password options
$TEXT['activate_pwgen'] = 'Activate password generation by email';
$TEXT['activate_explicite_msg'] = 'Activate explicit error messages';
$TEXT['sender_email'] = 'Sender email';
$TEXT['pwgen_max_pending'] = 'Pending time for a password generation request ( 1 to 744 hour )';

 ?>
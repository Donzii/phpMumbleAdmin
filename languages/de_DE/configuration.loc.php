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

$TEXT['tab_options'] = 'Optionen';
$TEXT['tab_ICE'] = 'ICE';
$TEXT['tab_settings'] = 'Einstellungen';
$TEXT['tab_debug'] = 'Debug';

// Tab options
$TEXT['select_lang'] = 'Sprache auswählen';
$TEXT['select_style'] = 'Style auswählen';
$TEXT['select_time'] = 'Lokale Zeit auswählen';
$TEXT['time_format'] = 'Zeitformat';
$TEXT['date_format'] = 'Datumsformat';
$TEXT['select_locales_profile'] = 'Lokales Profil auswählen';
$TEXT['uptime_format'] = 'Uptime Format';
$TEXT['conn_login'] = 'Connect Login';
$TEXT['conn_login_info'] = 'Mit diese Option können Sie den Login-Namen, den Sie mit Servern verbinden, auswählen';

$TEXT['default_options'] = 'Standard Optionen';
$TEXT['default_lang'] = 'Standard Sprache';
$TEXT['default_style'] = 'Standard Style';
$TEXT['default_time'] = 'Standard Zeitzone';
$TEXT['default_time_format'] = 'Standard Zeitformat';
$TEXT['default_date_format'] = 'Stadard Datumsformat';
$TEXT['default_locales'] = 'Standard lokale Informationen';
$TEXT['add_locales_profile'] = 'Hinzufügen eines lokalen Informationsprofils';
$TEXT['del_locales_profile'] = 'Löschen eines lokalen Informationsprofils';

$TEXT['sa_login'] = 'SuperAdmin Login';
$TEXT['change_your_pw'] = 'Passwortänderung';
$TEXT['enter_your_pw'] = 'Bitte geben Sie Ihr Passwort ein';

// Tab ICE
$TEXT['profile_name'] = 'Profilname';
$TEXT['ICE_host'] = 'ICE interface host';
$TEXT['ICE_port'] = 'ICE interface port';
$TEXT['ICE_timeout'] = 'Timeout in Sekunden';
$TEXT['ICE_secret'] = 'ICE Passwort';
$TEXT['slice_profile'] = 'Slice Profil';
$TEXT['slice_php_file'] = 'Slice php Datei';
$TEXT['conn_url'] = 'Connection URL';
$TEXT['conn_url_info'] = 'PMA erlauben, auf einen virtüllen Server mit der IP in Parameter "host" zu verbinden. Dieser Parameter erlaubt es, mit einem Hostnamen oder einer IP überschreiben.';
$TEXT['public_profile'] = 'Oeffentliches Profil';
$TEXT['default_ICE_profile'] = 'Standard ICE-Profil';
$TEXT['add_ICE_profile'] = 'ICE-Profil hinzufügen';
$TEXT['del_profile'] = 'Löschen eines Profils';
$TEXT['confirm_del_ICE_profile'] = 'Sie sind sicher, dass Sie dieses ICE-Profil löschen möchten?';
$TEXT['enable_profile'] = 'Hier Klicken, um das Profil zu aktivieren';

// Tab settings
$TEXT['mumble_accounts'] = 'Mumble Accounts';

$TEXT['disable_function'] = '0 zum Deaktivieren dieser Funktion';

$TEXT['site_title'] = 'Seitentitel';
$TEXT['site_desc'] = 'Seitenbeschreibung';
$TEXT['autologout'] = 'Auto-Logout nach x Minuten ( 5 - 30 )';
$TEXT['autocheck_update'] = 'Automatischer Update-Check';
$TEXT['autocheck_update_info'] = 'in Tagen: 0 - 31<br>0 deaktiviert diese Funktion';
$TEXT['check_update'] = 'Nach einem Update suchen';
$TEXT['inc_murmur_vers'] = 'Hinzufügen der Mumbleversion in die Connection-URL';
$TEXT['inc_murmur_vers_info'] = 'Ein alter Mumble-Client kann dann nicht auf den Server über die URL joinen';

$TEXT['show_avatar'] = 'Avatare nur für SuperAdmins anzeigen';

$TEXT['activate_su_login'] = 'Erlaube Superuser den Zugang zum Panel';
$TEXT['activate_su_modify_pw'] = 'Erlaubten Superuser dürfen Kennwörter von registrierte Benutzer ändern';
$TEXT['activate_su_vserver_start'] = 'Erlaubte Superuser dürfen virtuelle Server starten/stoppen';
$TEXT['activate_su_ru'] = 'Aktiviere die SuperUser_ru Klasse';
$TEXT['activate_su_ru_info'] = 'Geben Sie Superuser die Rechte für registrierte Benutzer unter Bedingungen (siehe readme.txt)';
$TEXT['reg_users'] = 'Registrierte Benutzer';
$TEXT['activate_ru_login'] = 'Erlaube registrierten Benutzern den Zugang zum Panel';
$TEXT['activate_ru_del_account'] = 'Erlaube registrierten Benutzern, Ihren Account zu löschen';
$TEXT['activate_ru_modify_login'] = 'Erlaube registrierten Benutzern, Ihre Zugangsdaten zu ändern';

$TEXT['vservers_logs'] = 'Virtuelle Server Logs';
$TEXT['srv_logs_amount'] = 'Anzahl der Logs, die das Panel vom Server anzeigen soll';
$TEXT['activate_vservers_logs_for_adm'] = 'Aktiviere das Log-Tab für Admins und SuperUser';
$TEXT['activate_adm_highlight_logs'] = 'Erlaube Admins und SuperUsers das Hightlighten der Logs';

$TEXT['pma_logs'] = 'Panel Logs';
$TEXT['pma_logs_infos'] = 'Optionen nur für SuperAdmin';
$TEXT['logs_sa_actions'] = 'Log SuperAdmin Aktionen ( RootAdmin ausgeschlossen )';
$TEXT['pma_logs_clean'] = 'Säubere alte Logs ( in Tagen )';

$TEXT['tables'] = 'Tabelle';
$TEXT['overview_table_lines'] = 'Anzahl der Server-Tabellen';
$TEXT['users_table_lines'] = 'Anzahl der registrierten Benutzer-Tabelle';
$TEXT['ban_table_lines'] = 'Anzahl der Ban-Tabelle';
$TEXT['tables_infos'] = '10 - 1000 ( 0 deaktiviert die Tabellenseiten )';

$TEXT['overview_table'] = 'Tabellen-Uebersicht';
$TEXT['enable_users_total'] = 'Zeige die komplette Anzahl der Benutzer';
$TEXT['enable_connected_users'] = 'Zeige verbundene Benutzer';
$TEXT['enable_vserver_uptime'] = 'Zeige die vServer Uptime';
$TEXT['sa_only'] = 'nur für SuperAdmins';

$TEXT['srv_dropdown_list'] = 'Server DropDown Liste';
$TEXT['activate_auth_dropdown'] = 'Aktiviere die DropDown-Liste der Server auf der Loginseite';
$TEXT['activate_auth_dropdown_info'] = 'Dies bedeutet, dass alle erreichbaren Server-IDs in dem HTML-Qüllcode der Loginseite angezeigt werden!';
$TEXT['refresh_ddl_cache'] = 'Automatische Erneuerung der DropDown-Serverliste (Cache). Zeit in Stunden';
$TEXT['ddl_show_cache_uptime'] =  'Zeige Cache-Uptime';

$TEXT['autoban'] = 'Autoban';
$TEXT['autoban_attemps'] = 'Limit der Versuche';
$TEXT['autoban_frame'] = 'Zeitrahmen ( in Sekunden )';
$TEXT['autoban_duration'] = 'Ban-Zeit ( in Sekunden )';

$TEXT['smtp_srv'] = 'SMTP server';
$TEXT['host'] = 'Host';
$TEXT['port'] = 'port';
$TEXT['default_sender_email'] = 'Standard Absender-Emailadresse';

$TEXT['external_viewer'] = 'Externer Viewer';
$TEXT['see_external_viewer'] = 'Siehe Externer Viewer';
$TEXT['external_viewer_enable'] = 'Aktiviere Externer Viewer';
$TEXT['external_viewer_width'] = 'Viewer Breite';
$TEXT['external_viewer_height'] = 'Viewer Höhe';
$TEXT['external_viewer_vertical'] = 'Align verticaly viewers';
$TEXT['external_viewer_scroll'] = 'Aktiviere Scrollbars im Viewer';

// Generate password options
$TEXT['activate_pwgen'] = 'Aktiviere Passwortgenerierung per Email';
$TEXT['activate_explicite_msg'] = 'Aktivieren explizite Fehlermeldungen';
$TEXT['sender_email'] = 'Absender-Emailadresse';
$TEXT['pwgen_max_pending'] = 'Zeit für eine Passwort-Generierungs-Anfrage ( 1 bis 744 Stunden )';

 ?>
<?php

 /*
 * phpMumbleAdmin (PMA), web php administration tool for murmur (mumble server daemon).
 * Copyright (C) 2010 - 2015  Dadon David. PMA@ipnoz.net
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$TEXT['tab_admins'] = 'Admins';
$TEXT['tab_bans'] = 'Bans';
$TEXT['tab_pwRequests'] = 'Anfragen';
$TEXT['tab_whosOnline'] = 'Aktivität';
$TEXT['tab_logs'] = 'Logs';

$TEXT['subtab_all'] = 'All';

$TEXT['pw_request_pending'] = 'wartende Passwort-Anfragen';
$TEXT['ice_profile'] = 'PHP-ICE Profil';
$TEXT['sid'] = 'Server ID';
$TEXT['uid'] = 'User ID';
$TEXT['request_id'] = 'Anfragen ID';
$TEXT['sessions_infos'] = 'Session: %1$s - Bekannt: %2$s - Unbekannt: %3$s';
$TEXT['proxyed'] = 'Dieser Benutzer scheint einen Proxy zu nutzen.';
$TEXT['unauth'] = 'Unbekannt';

// Admins tab
$TEXT['add_admin'] = 'Admin hinzufügen';
$TEXT['del_admin'] = 'Admin löschen';
$TEXT['confirm_del_admin'] = 'Sicher, dass Sie den Admin löschen wollen?';
$TEXT['user_name'] = 'Benutzername';
$TEXT['registered_date'] = 'Datum der Registrierung';
$TEXT['last_conn'] = 'Letzter Connect';
$TEXT['profile_access'] = 'Profile Zugang';
$TEXT['full_access'] = '%s : Voller Zugriff'; // %s : ice profile name
$TEXT['srv_access'] = '%1$s : %2$d Server'; // %1$s : ice profile name %2$d = number of virtual server admin have access
$TEXT['enable_full_access'] = 'Aktiviere volle Rechte für dieses Profil';
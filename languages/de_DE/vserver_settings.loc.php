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

$TEXT['subtab_welcometext'] = 'Willkommenstext';
$TEXT['subtab_certificate'] = 'Zertifikat';

$TEXT['registername_info'] = 'Servername.<br />Diese Eingabe ist empfohlen, wenn Sie den Server in der globalen Serverliste registrieren wollen.';
$TEXT['host_info'] = 'Addresse des vServers.<br />Neustart benötigt';
$TEXT['port_info'] = 'Port des vServer.<br />Neustart benötigt';
$TEXT['password_info'] = 'Password für unregistrierte User. Leerlassen für einen öffentlichen Mumble-Server.';
$TEXT['timeout_info'] = 'Timeout in Sekunden, bevor der Server eine tote Verbindung beendet.';
$TEXT['bandwidth_info'] = 'Erlaubte maximale Bandbreite (in bits per second) der Clients.';
$TEXT['users_info'] = 'Maximale Anzahl von Clients.';
$TEXT['defaultchannel_info'] = 'Default Channel ID nachdem der Server betreten wurde.';
$TEXT['registerpassword_info'] = 'Password für die globale öffentliche Serverliste.';
$TEXT['registerhostname_info'] = 'DNS Hostname for the global public server list. It only needs to be set if you want your server to be addressed in the server list by it\'s hostname instead of by IP. The DNS must resolve on the internet or registration will fail.';
$TEXT['registerurl_info'] = 'Your website URL for the global public server list.';
$TEXT['username_info'] = 'Regular expression used to validate user names.';
$TEXT['channelname_info'] = 'Regular expression used to validate channel names.';
$TEXT['textmessagelength_info'] = 'Maximum length of text messages in characters.<br />0 for no limits.';
$TEXT['imagemessagelength_info'] = 'Maximum length of text messages in characters, with image data.<br />0 for no limits.';
$TEXT['allowhtml_info'] = 'Allow clients to use HTML in messages, user comments, and channel descriptions.';
$TEXT['bonjour_info'] = 'Bonjour service discovery';
$TEXT['certrequired_info'] = 'If this options is enabled, only clients which have a certificate are allowed to connect.';
$TEXT['usersperchannel_info'] = 'Maximum number of concurrent clients allowed for each channels.';
$TEXT['rememberchannel_info'] = 'If enabled, registered users will join the last channel they were in when they reconnect to the server.';
$TEXT['opusthreshold_info'] = 'Amount of users with Opus support needed to force Opus usage, in percent.<br />0 = Always enable Opus.<br />100 = enable Opus if it\'s supported by all clients.';
$TEXT['channelnestinglimit_info'] = 'Maximum depth of channel nesting. Note that some databases like MySQL using InnoDB will fail when operating on deeply nested channels.';
$TEXT['suggestpositional_info'] = 'Setting this to "enabled" will alert any user who does not have positional audio enabled that the server administrators recommend enabling it.<br />Setting it to "disabled" will have the opposite effect<br />If you do not care whether the user enables positional audio or not, set it to blank.';
$TEXT['suggestpushtotalk_info'] = 'Setting this to "enabled" will alert any user who does not have Push-To-Talk enabled that the server administrators recommend enabling it.<br />Setting it to "disabled" will have the opposite effect<br />If you do not care whether the user enables PTT or not, set it to blank.';

$TEXT['reset_param'] = 'Reset %s setting'; // %s = setting key
$TEXT['enable'] = 'Enable';
$TEXT['disable'] = 'Disable';
$TEXT['enabled'] = 'Enabled';
$TEXT['disabled'] = 'Disabled';
$TEXT['invalid_cert'] = 'Error: invalid certificate';
$TEXT['confirm_reset_cert'] = 'Do you confirm to reset the certificate?';
$TEXT['add_certificate'] = 'Add certificate and private key';


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

$TEXT['ice_error'] = 'ICE fatal error';
$TEXT['ice_error_unauth'] = 'PhpMumbleAdmin has encountered an error with ICE.<br />All activity is disabled during this error.<br />Please refer this to your admin.';
$TEXT['ice_error_common'] = 'PhpMumbleAdmin could not connect to ICE interface.';

// Explicit Ice errors
$TEXT['ice_module_not_found'] = 'php-ICE Modul nicht gefunden';
$TEXT['Ice_ConnectionRefusedException'] = 'Verbindung abgelehnt';
$TEXT['Ice_ConnectTimeoutException'] = 'Verbindungs-TimeOut';
$TEXT['Ice_ProfileNotFoundException'] = 'Slice Profile existiert nicht';
$TEXT['ice_no_slice_definition_found'] = 'Keine Slice-Definition gefunden';
$TEXT['Murmur_InvalidSecretException'] = 'Das Passwort ist falsch';
$TEXT['ice_invalid_slice_file'] = 'Ungültige slice Definitiondatei';
$TEXT['ice_could_not_load_Icephp_file'] = '"Ice.php" Datei nicht gefunden';
$TEXT['ice_invalid_murmur_version'] = 'Ungültige murmur Version';
$TEXT['Ice_DNSException'] = 'Host nicht gefunden';
$TEXT['Ice_UnknownErrorException'] = 'Unbekannter Fehler';

$TEXT['iceprofiles_admin_none'] = 'Sie sind nicht authorisiert, auf alle Server zugreifen. Bitte wenden Sie sich an den Admin';

// Messages
$TEXT['Murmur_InvalidChannelException'] = 'Dieser Channel existiert nicht oder wurde gelöscht.';
$TEXT['Murmur_InvalidServerException'] = 'Dieser Server existiert nicht.';
$TEXT['Murmur_InvalidSecretException_write'] = 'Du hast kein ICE Schreibzugang.<br />Bitte gibt das "icesecretwrite" Passwort richtig an.';
$TEXT['Murmur_InvalidSessionException'] = 'Dieser Benutzer ist nicht verbunden oder hat den Server verlassen.';
$TEXT['Murmur_InvalidUserException'] = 'Diese Registrierung gibt es nicht oder wurde gelöscht.';
$TEXT['Murmur_NestingLimitException'] = 'Channel-Grenze erreicht';
$TEXT['Murmur_ServerBootedException'] = 'Der Server wurde wegen eines Updates gestoppt. Die letzte Aktion wurde nicht gespeichert.';
$TEXT['Murmur_ServerFailureException'] = 'Der Server konnte nicht gestartet werden.<br />Überprüfe die Serverlogs';
$TEXT['Murmur_UnknownException'] = 'An unknown ICE error has occurred';
$TEXT['auth_error'] = 'Bestätigungsfehler';
$TEXT['auth_su_disabled'] = 'SuperUser Login deaktiviert';
$TEXT['auth_ru_disabled'] = 'Registrierte User-Anmeldung deaktiviert';
$TEXT['change_pw_error'] = 'Fehler, dass Passwort wurde nicht geändert';
$TEXT['change_pw_success'] = 'Das Passwort wurde geändert';
$TEXT['invalid_bitmask'] = 'Ungültige Eingabe';
$TEXT['invalid_channel_name'] = 'Ungültiger Channel-Name (Character)';
$TEXT['invalid_username'] = 'Ungültiger Benutzername';
$TEXT['invalid_certificate'] = 'Ungültiges Uertifikat';
$TEXT['auth_vserver_stopped'] = 'Der Server wurde gestoppt. Bitte versuchen Sie es später noch einmal.';
$TEXT['user_already_registered'] = 'Dieser Benutzer ist schon vorhanden - Er muss sich erneut zum Server verbinden, um seinen Status zum authentifizierten Benutzer zu ändern.';
$TEXT['children_channel'] = 'Sie können keinen HauptChannel in einen UnterChannel schieben.';
$TEXT['username_exists'] = 'Dieser Benutzer existiert bereits.';
$TEXT['gen_pw_mail_sent'] = 'Eine AktivierungsEmail wurde an Ihre Emailadresse verschickt.<br>Bitte folgen Sie den Anweisung, um ein neüs Passwort zu generieren.';
$TEXT['web_access_disabled'] = 'Web-Zugang zu diesem Server wurde deaktiviert.';
$TEXT['vserver_dont_allow_HTML'] = 'Der Server erlaubt keine HTML-Tags';
$TEXT['please_authenticate'] = 'Bitte anmelden';
$TEXT['iceProfile_sessionError'] = 'Ein Fehler ist während Ihrer Session aufgetreten - Zum Zweck der Sicherheit, melden Sie sich bitte neu an.';
$TEXT['gen_pw_authenticated'] = 'Sie müssen ausgeloggt sein, um ein neüs passwort zu generieren. Bitte melden Sie sich jetzt ab und versuchen Sie es erneut.';
$TEXT['certificate_modified_success'] = 'Zertifikat wurde erfolgreich geändert.<br>Bitte den Server neu starten.';
$TEXT['host_modified_success'] = 'Host Parameter wurde erfolgreich geändert.<br>Bitte den Server neu starten.';
$TEXT['port_modified_success'] = 'Port wurde erfolgreich geändert.<br>Bitte den Server neu starten.';
$TEXT['illegal_operation'] = 'Illegal Operation';
$TEXT['vserver_reset_success'] = 'Konfiguration wurde erfolgreich resetet';
$TEXT['new_su_pw'] = 'Neues Passwort für den SuperUser: %s'; // %s new SuperUser password
$TEXT['registration_deleted_success'] = 'Registrierung wurde gelöscht';
$TEXT['gen_pw_error'] = 'Es ist ein Fehler aufgetreten und die Passwort-Generierung wurde abgebrochen.';
$TEXT['gen_pw_invalid_server_id'] = 'Die Server-ID ist nicht korrekt und die Passwort-Generierung wurde abgebrochen.';
$TEXT['gen_pw_invalid_username'] = 'Der Username existiert nicht und die Passwort-Generierung wurde abgebrochen.';
$TEXT['gen_pw_su_denied'] = 'Passwort-Generierung für einen SuperUser-Account ist nicht erlaubt';
$TEXT['gen_pw_empty_email'] = 'Keine Emailadresse für diesen Account gefunden und die Passwort-Generierung wurde abgebrochen.';
$TEXT['new_pma_version'] = 'PhpMumbleAdmin %s wurde veröffentlicht!';  // %s = new PMA version
$TEXT['no_update_found'] = 'Kein Update vorhanden';
$TEXT['registration_created_success'] = 'Registrierung erfolgreich';
$TEXT['Ice_MemoryLimitException_logs'] = 'Die Server-Logs ind zu gross.<br>Bitte weisen Sie Ihren Admin an, die Logs zu kürzen.<br>PMA hat die Logs kurzeitig auf 100 zeilen geschrumpft.';
$TEXT['vserver_created_success'] = 'Virtueller Server wurde erstellt.';
$TEXT['vserver_deleted_success'] = 'Virtueller Server wurde gelöscht.';
$TEXT['refuse_cookies'] = 'Cookies sind deaktiviert!<br>Bitte erlauben Sie die Cookie-Nutzung um sich anzumelden!';
$TEXT['parameters_updated_success'] = 'Alle Server wurden mit Erfolg konfiguriert';
$TEXT['vserver_offline'] = 'Der Server ist offline.';
$TEXT['vserver_offline_info'] = 'Du kannst keine Channels, Registrierungen oder Bans editieren, wenn der Server offline ist.';
$TEXT['gen_pw_mail_sent'] = 'Eine AktivierungsEmail wurde an Ihre Emailadresse verschickt.<br>Bitte folgen Sie den Anweisung, um ein neüs Passwort zu generieren.';
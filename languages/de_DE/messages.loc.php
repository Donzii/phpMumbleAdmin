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

$TEXT['ice_error'] = 'ICE fatal error:';

// ice errors
$TEXT['ice_module_not_found'] = 'php-ICE Modul nicht gefunden';
$TEXT['ice_connection_refused'] = 'Verbindung abgelehnt';
$TEXT['ice_connection_timeout'] = 'Verbindungs-TimeOut';
$TEXT['ice_slice_profile_not_exists'] = 'Slice Profile existiert nicht';
$TEXT['ice_no_slice_definition_found'] = 'Keine Slice-Definition gefunden';
$TEXT['ice_invalid_secret'] = 'Das Passwort ist falsch';
$TEXT['ice_invalid_slice_file'] = 'Ungueltige slice Definitiondatei';
$TEXT['ice_icephp_not_found'] = '"Ice.php" Datei nicht gefunden';
$TEXT['ice_invalid_murmur_version'] = 'Ungueltige murmur Version';
$TEXT['ice_host_not_found'] = 'Host nicht gefunden';
$TEXT['ice_unknown_error'] = 'Unbekannter Fehler';

$TEXT['iceprofiles_admin_none'] = 'Sie sind nicht authorisiert, auf alle Server zugreifen. Bitte wenden Sie sich an den Admin';

$TEXT['ice_help_common'] = 'PhpMumbleAdmin konnte sich nicht mit dem ICE interface verbinden.';

$TEXT['ice_help_no_slice_definition_found'] = 'Dies bedeutet, dass kein Murmur.ice oder php-Slice geladen werden konnte';
$TEXT['ice_help_slice_32'] = 'Um das problem zu beheben, lesen Sie bitte diese Datei ( phpICE 3.2 )';
$TEXT['ice_help_ice34'] = 'Bitte lesen sie die README.txt => ICE 3.4';
$TEXT['ice_help_unauth'] = 'PhpMumbleAdmin hat einen Fehler mit ICE festgestellt.<br>Alle Aktivitaeten wurde waehrend des Fehlers deaktiviert.<br>Bitte wenden Sie sich an den Admin.';
$TEXT['ice_help_upgrade_murmur'] = 'PMA untersteutze nicht diese Version von murmur.<br>Aktualisieren Sie auf 1.2.0 oder hoeher.';
$TEXT['ice_help_slice_file'] = 'Murmur.ice oder slice-php Datei ist nicht kompatibel mit Ihrem murmur-Daemon. Das Profil wurde deaktiviert.';

// Messages
$TEXT['auth_error'] = 'Bestaetigungsfehler';
$TEXT['auth_su_disabled'] = 'SuperUser Login deaktiviert';
$TEXT['auth_ru_disabled'] = 'Registrierte User-Anmeldung deaktivert';
$TEXT['change_pw_error'] = 'Fehler, dass Passwort wurde nicht geaendert';
$TEXT['change_pw_success'] = 'Das Passwort wurde geaendert';
$TEXT['invalid_bitmask'] = 'Ungueltige Eingabe';
$TEXT['invalid_channel_name'] = 'Ungueltiger Channel-Name (Character)';
$TEXT['invalid_username'] = 'Ungueltiger Benutzername';
$TEXT['invalid_certificate'] = 'Ungueltiges Uertifikat';
$TEXT['auth_vserver_stopped'] = 'Der Server wurde gestoppt. Bitte versuchen Sie es spaeter noch einmal.';
$TEXT['user_already_registered'] = 'Dieser Benutzer ist schon vorhanden - Er muss sich erneut zum Server verbinden, um seinen Status zum authentifizierten Benutzer zu aendern.';
$TEXT['InvalidSessionException'] = 'Dieser Benutzer ist nicht verbunden oder hat den Server verlassen.';
$TEXT['InvalidChannelException'] = 'Dieser Channel existiert nicht oder wurde geloescht.';
$TEXT['InvalidUserException'] = 'Diese Registrierung gibt es nicht oder wurde geloescht.';
$TEXT['children_channel'] = 'Sie koennen keinen HauptChannel in einen UnterChannel schieben.';
$TEXT['ServerBootedException'] = 'Der Server wurde wegen eines Updates gestoppt. Die letzte Aktion wurde nicht gespeichert.';
$TEXT['invalid_secret_write'] = 'Sie haben keine ICE-Schreibrechte.<br>Bitte geben Sie das "icesecretwrite" Passwort in PMA ein.';
$TEXT['ServerFailureException'] = 'Der Server wurde nicht gestartet.<br>Bitte die ServerLogs checken.';
$TEXT['unknown_murmur_exception'] = 'Ein unbekannter ICE-Fehler ist aufgetreten.';
$TEXT['vserver_dont_exists'] = 'Der Server existiert nicht oder wurde geloescht.';
$TEXT['username_exists'] = 'Dieser Benutzer existiert bereits.';
$TEXT['gen_pw_mail_sent'] = 'Eine AktivierungsEmail wurde an Ihre Emailadresse verschickt.<br>Bitte folgen Sie den Anweisung, um ein neues Passwort zu generieren.';
$TEXT['web_access_disabled'] = 'Web-Zugang zu diesem Server wurde deaktiviert.';
$TEXT['vserver_dont_allow_HTML'] = 'Der Server erlaubt keine HTML-Tags';
$TEXT['please_authenticate'] = 'Bitte anmelden';
$TEXT['iceProfile_sessionError'] = 'Ein Fehler ist waehrend Ihrer Session aufgetreten - Zum Zweck der Sicherheit, melden Sie sich bitte neu an.';
$TEXT['gen_pw_authenticated'] = 'Sie muessen ausgeloggt sein, um ein neues passwort zu generieren. Bitte melden Sie sich jetzt ab und versuchen Sie es erneut.';
$TEXT['certificate_modified_success'] = 'Zertifikat wurde erfolgreich geaendert.<br>Bitte den Server neu starten.';
$TEXT['host_modified_success'] = 'Host Parameter wurde erfolgreich geaendert.<br>Bitte den Server neu starten.';
$TEXT['port_modified_success'] = 'Port wurde erfolgreich geaendert.<br>Bitte den Server neu starten.';
$TEXT['illegal_operation'] = 'Illegal Operation';
$TEXT['vserver_reset_success'] = 'Konfiguration wurde erfolgreich resetet';
$TEXT['new_su_pw'] = 'Neues Passwort fuer den SuperUser: %s'; // %s new SuperUser password
$TEXT['registration_deleted_success'] = 'Registrierung wurde geloescht';
$TEXT['gen_pw_error'] = 'Es ist ein Fehler aufgetreten und die Passwort-Generierung wurde abgebrochen.';
$TEXT['gen_pw_invalid_server_id'] = 'Die Server-ID ist nicht korrekt und die Passwort-Generierung wurde abgebrochen.';
$TEXT['gen_pw_invalid_username'] = 'Der Username existiert nicht und die Passwort-Generierung wurde abgebrochen.';
$TEXT['gen_pw_su_denied'] = 'Passwort-Generierung fuer einen SuperUser-Account ist nicht erlaubt';
$TEXT['gen_pw_empty_email'] = 'Keine Emailadresse fuer diesen Account gefunden und die Passwort-Generierung wurde abgebrochen.';
$TEXT['new_pma_version'] = 'PhpMumbleAdmin %s wurde veroeffentlicht!';  // %s = new PMA version
$TEXT['no_update_found'] = 'Kein Update vorhanden';
$TEXT['registration_created_success'] = 'Registrierung erfolgreich';
$TEXT['iceMemoryLimitException_logs'] = 'Die Server-Logs ind zu gross.<br>Bitte weisen Sie Ihren Admin an, die Logs zu kürzen.<br>PMA hat die Logs kurzeitig auf 100 zeilen geschrumpft.';
$TEXT['vserver_created_success'] = 'Virtueller Server wurde erstellt.';
$TEXT['vserver_deleted_success'] = 'Virtueller Server wurde geloescht.';
$TEXT['refuse_cookies'] = 'Cookies sind deaktiviert!<br>Bitte erlauben Sie die Cookie-Nutzung um sich anzumelden!';
$TEXT['parameters_updated_success'] = 'Alle Server wurden mit Erfolg konfiguriert';
$TEXT['NestingLimitException'] = 'Channel-Grenze erreicht';

?>
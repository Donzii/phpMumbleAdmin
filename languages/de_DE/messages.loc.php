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
$TEXT['ice_module_not_found'] = 'php-ICE module not found';
$TEXT['ice_connection_refused'] = 'Connection refused';
$TEXT['ice_connection_timeout'] = 'Connection timeout';
$TEXT['ice_slice_profile_not_exists'] = 'Slice profile do not exists';
$TEXT['ice_no_slice_definition_found'] = 'No slice definition found';
$TEXT['ice_invalid_secret'] = 'The password is incorrect';
$TEXT['ice_invalid_slice_file'] = 'Invalid slice definition file';
$TEXT['ice_icephp_not_found'] = '"Ice.php" file not found';
$TEXT['ice_invalid_murmur_version'] = 'Invalid murmur version';
$TEXT['ice_host_not_found'] = 'Host not found';
$TEXT['ice_unknown_error'] = 'Unknown error';

$TEXT['iceprofiles_admin_none'] = 'You are currently not allowed to access any servers. Please refer this error to your admin';

$TEXT['ice_help_common'] = 'PhpMumbleAdmin could not connect to ICE interface.';

$TEXT['ice_help_no_slice_definition_found'] = 'This means that none of Murmur.ice / php-Slice has been loaded';
$TEXT['ice_help_slice_32'] = 'To fix the problem, please read this file ( phpICE 3.2 )';
$TEXT['ice_help_ice34'] = 'Please take a look to README.txt => ICE 3.4';
$TEXT['ice_help_unauth'] = 'PhpMumbleAdmin has encountered an error with ICE.<br>All activity is disabled during this error.<br>Please refer this to your admin.';
$TEXT['ice_help_upgrade_murmur'] = 'PMA do not support this version of murmur.<br>You have to upgrade to 1.2.0 or higher.';
$TEXT['ice_help_slice_file'] = 'Murmur.ice or slice-php file is incompatible with the murmur daemon. The profile has been disabled.';

// Messages
$TEXT['auth_error'] = 'Authentication failure';
$TEXT['auth_su_disabled'] = 'SuperUser login disabled';
$TEXT['auth_ru_disabled'] = 'Registered user login disabled';
$TEXT['change_pw_error'] = 'Error, the password has not been changed';
$TEXT['change_pw_success'] = 'The password has been changed';
$TEXT['invalid_bitmask'] = 'Invalid mask';
$TEXT['invalid_channel_name'] = 'Invalid channel name character';
$TEXT['invalid_username'] = 'Invalid user name character';
$TEXT['invalid_certificate'] = 'Invalid certificate';
$TEXT['auth_vserver_stopped'] = 'The server is stopped for the moment - Please try later';
$TEXT['user_already_registered'] = 'This user is already registered - He must reconnect to the server to change his status to authenticated user';
$TEXT['InvalidSessionException'] = 'This user is not connected or has left the server';
$TEXT['InvalidChannelException'] = 'This channel do not exists or has been deleted';
$TEXT['InvalidUserException'] = 'This registration does not exist or has been deleted.';
$TEXT['children_channel'] = 'You can\'t move a channel to a child channel';
$TEXT['ServerBootedException'] = 'The server has been stopped during the update. Your last action has not been saved';
$TEXT['invalid_secret_write'] = 'You do not have ICE write access.<br>You must specify the "icesecretwrite" password to PMA';
$TEXT['ServerFailureException'] = 'The server couldn\'t start.<br>Please check the server logs';
$TEXT['unknown_murmur_exception'] = 'An unknown ICE error has occurred';
$TEXT['vserver_dont_exists'] = 'The server does not exist or has been deleted';
$TEXT['username_exists'] = 'This username already exists';
$TEXT['gen_pw_mail_sent'] = 'A confirmation email has been sent to your email address.<br>Please follow the instructions to generate a new password.';
$TEXT['web_access_disabled'] = 'Web access to the server is disabled';
$TEXT['vserver_dont_allow_HTML'] = 'The server does not allow HTML tags';
$TEXT['please_authenticate'] = 'Please sign-in';
$TEXT['iceProfile_sessionError'] = 'An error occured during your session - For security reason, please log in again';
$TEXT['gen_pw_authenticated'] = 'You must be unauthentified to process a password generation request. Please logout and retry';
$TEXT['certificate_modified_success'] = 'Certificate has been modified with success<br>You have to restart the server';
$TEXT['host_modified_success'] = 'Host parameter has been modified with success<br>You have to restart the server';
$TEXT['port_modified_success'] = 'Port has been modified with success<br>You have to restart the server';
$TEXT['illegal_operation'] = 'Illegal operation';
$TEXT['vserver_reset_success'] = 'Configuration has been reseted';
$TEXT['new_su_pw'] = 'New password for SuperUser: %s'; // %s new SuperUser password
$TEXT['registration_deleted_success'] = 'Registration has been deleted';
$TEXT['gen_pw_error'] = 'An error occured and PMA can\'t handle your password generation request';
$TEXT['gen_pw_invalid_server_id'] = 'The server ID is incorrect and PMA can\'t handle your password generation request';
$TEXT['gen_pw_invalid_username'] = 'The user name do not exists and PMA can\'t handle your password generation request';
$TEXT['gen_pw_su_denied'] = 'You can\'t request a password generation for SuperUser account';
$TEXT['gen_pw_empty_email'] = 'No email address for the account found and PMA can\'t handle your password generation request';
$TEXT['new_pma_version'] = 'PhpMumbleAdmin %s has been released';  // %s = new PMA version
$TEXT['no_update_found'] = 'No update found';
$TEXT['registration_created_success'] = 'Registration has been created';
$TEXT['iceMemoryLimitException_logs'] = 'The server logs are too big.<br>Please tell your admin to decrease logs requests.<br>PMA has forced a request for 100 lines.';
$TEXT['vserver_created_success'] = 'Virtual server has been created';
$TEXT['vserver_deleted_success'] = 'Virtual server has been deleted';
$TEXT['refuse_cookies'] = 'Cookies are disabled.<br>You must accept cookies to be able to authenticate.';
$TEXT['parameters_updated_success'] = 'All servers have been configured with success';
$TEXT['NestingLimitException'] = 'Channel nesting limit reached';

?>
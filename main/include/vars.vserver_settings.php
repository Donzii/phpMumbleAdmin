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

/**
* MEMO :
* murmur parameters MUST be in lower case
 ie: $vserver_settings['registername'] is OK
 *   $vserver_settings['registerName'] is not
 *
 * "right'" key:
 * SA: the parameter will be only available for SuperAdmins
 * SU : for all admins and SuperUsers
*/

$vserver_settings['registername'] = array(
	'right' => 'SU',
	'name' => 'Register name',
	'type' => 'string',
	'version' => 120,
	'order' => 1,
);
$vserver_settings['host'] = array(
	'right' => 'SA',
	'name' => 'Host',
	'type' => 'string',
	'version' => 120,
	'order' => 2,
);
$vserver_settings['port'] =	array(
	'right' => 'SA',
	'name' => 'Port',
	'type' => 'string',
	'maxlen' => '5',
	'version' => 120,
	'order' => 3,
);
$vserver_settings['password'] =	array(
	'right' => 'SU',
	'name' => 'Password',
	'type' => 'string',
	'version' => 120,
	'order' => 4,
);
$vserver_settings['timeout'] = array(
	'right' => 'SA',
	'name' => 'Timeout',
	'type' => 'integer',
	'maxlen' => '5',
	'version' => 120,
	'order' => 5,
);
$vserver_settings['bandwidth'] = array(
	'right' => 'SA',
	'name' => 'Bandwidth',
	'type' => 'integer',
	'version' => 120,
	'order' => 6,
);
$vserver_settings['users'] = array(
	'right' => 'SA',
	'name' => 'Users',
	'type' => 'integer',
	'maxlen' => '5',
	'version' => 120,
	'order' => 7,
);
// usersPerChannel come with murmur 1.2.1
$vserver_settings['usersperchannel'] = array(
	'right' => 'SU',
	'name' => 'Users per channel',
	'type' => 'integer',
	'maxlen' => '5',
	'version' => 121,
	'order' => 8,
);
// rememberChannel come with murmur 1.2.3
$vserver_settings['rememberchannel'] = array(
	'right' => 'SU',
	'name' => 'Remember channel',
	'type' => 'bool',
	'version' => 123,
	'order' => 9,
);
$vserver_settings['defaultchannel'] = array(
	'right' => 'SU',
	'name' => 'Default channel',
	'type' => 'integer',
	'maxlen' => '5',
	'version' => 120,
	'order' => 10,
);
$vserver_settings['registerpassword'] = array(
	'right' => 'SU',
	'name' => 'Register password',
	'type' => 'string',
	'version' => 120,
	'order' => 11,
);
$vserver_settings['registerhostname'] = array(
	'right' => 'SU',
	'name' => 'Register hostname',
	'type' => 'string',
	'version' => 120,
	'order' => 12,
);
$vserver_settings['registerurl'] = array(
	'right' => 'SU',
	'name' => 'Register URL',
	'type' => 'string',
	'version' => 120,
	'order' => 13,
);
$vserver_settings['username'] = array(
	'right' => 'SA',
	'name' => 'User name',
	'type' => 'string',
	'version' => 120,
	'order' => 14,
);
$vserver_settings['channelname'] = array(
	'right' => 'SA',
	'name' => 'Channel name',
	'type' => 'string',
	'version' => 120,
	'order' => 15,
);
$vserver_settings['textmessagelength'] = array(
	'right' => 'SA',
	'name' => 'Text message length',
	'type' => 'integer',
	'version' => 120,
	'order' => 16,
);
$vserver_settings['imagemessagelength'] = array(
	'right' => 'SA',
	'name' => 'Image message length',
	'type' => 'integer',
	'version' => 120,
	'order' => 17,
);
$vserver_settings['allowhtml'] = array(
	'right' => 'SA',
	'name' => 'Allow HTML',
	'type' => 'bool',
	'version' => 120,
	'order' => 18,
);
$vserver_settings['bonjour'] = array(
	'right' => 'SA',
	'name' => 'Bonjour',
	'type' => 'bool',
	'version' => 120,
	'order' => 19,
);
$vserver_settings['certrequired'] = array(
	'right' => 'SU',
	'name' => 'Certificate required',
	'type' => 'bool',
	'version' => 120,
	'order' => 20,
);

$vserver_settings['opusthreshold'] = array(
	'right' => 'SA',
	'name' => 'Opus threshold',
	'type' => 'integer',
	'version' => 124,
	'order' => 21,
);
$vserver_settings['channelnestinglimit'] = array(
	'right' => 'SA',
	'name' => 'Channel nesting limit',
	'type' => 'integer',
	'version' => 124,
	'order' => 22,
);
$vserver_settings['suggestpositional'] = array(
	'right' => 'SU',
	'name' => 'Suggest positional',
	'type' => 'bool',
	'version' => 124,
	'order' => 23,
);
$vserver_settings['suggestpushtotalk'] = array(
	'right' => 'SU',
	'name' => 'Suggest push-to-talk',
	'type' => 'bool',
	'version' => 124,
	'order' => 24,
);
$vserver_settings['welcometext'] = array(
	'right' => 'SU',
	'name' => 'Welcome text',
	'type' => 'string',
	'version' => 120,
	'order' => 25,
);
$vserver_settings['certificate'] = array(
	'right' => 'SU',
	'name' => 'Certificate',
	'type' => 'string',
	'version' => 120,
	'order' => 26,
);
$vserver_settings['key'] = array(
	'right' => 'SU',
	'name' => 'Certificate key',
	'type' => 'string',
	'version' => 120,
	'order' => 27,
);

// Sanity
foreach( $vserver_settings as $key => $array ) {

	if ( $array['version'] > PMA_meta::instance()->int_version ) {
		unset( $vserver_settings[ $key ] );
	}
}

/**
* MEMO : works only for the ini file
*
* sendversion
* suggestversion
*/

?>

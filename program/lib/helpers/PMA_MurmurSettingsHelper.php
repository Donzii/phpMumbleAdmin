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

if (! defined('PMA_STARTED')) { die('ILLEGAL: You cannot call this script directly !'); }

/**
* MEMO :
* murmur parameters MUST be in lower case
* example:
* $settings['registername'] is ok.
* $settings['registerName'] is not.
*
* "right" key:
* SA: the parameter will be available for SuperAdmins only.
* SU : the parameter will be available for all.
*
* Parameters which only works with the ini file, and not included here:
* sendversion, suggestversion
*
* @return array
*/
class PMA_MurmurSettingsHelper
{
    public static function get($murmurVersion)
    {
        $settings = array();

        $settings['registername'] = array(
            'right' => 'SU',
            'name' => 'Register name',
            'type' => 'string',
            'version' => 120,
            'order' => 1,
        );
        $settings['host'] = array(
            'right' => 'SA',
            'name' => 'Host',
            'type' => 'string',
            'version' => 120,
            'order' => 2,
        );
        $settings['port'] = array(
            'right' => 'SA',
            'name' => 'Port',
            'type' => 'string',
            'maxlen' => '5',
            'version' => 120,
            'order' => 3,
        );
        $settings['password'] = array(
            'right' => 'SU',
            'name' => 'Password',
            'type' => 'string',
            'version' => 120,
            'order' => 4,
        );
        $settings['timeout'] = array(
            'right' => 'SA',
            'name' => 'Timeout',
            'type' => 'integer',
            'maxlen' => '5',
            'version' => 120,
            'order' => 5,
        );
        $settings['bandwidth'] = array(
            'right' => 'SA',
            'name' => 'Bandwidth',
            'type' => 'integer',
            'version' => 120,
            'order' => 6,
        );
        $settings['users'] = array(
            'right' => 'SA',
            'name' => 'Users',
            'type' => 'integer',
            'maxlen' => '5',
            'version' => 120,
            'order' => 7,
        );
        // usersPerChannel come with murmur 1.2.1
        $settings['usersperchannel'] = array(
            'right' => 'SU',
            'name' => 'Users per channel',
            'type' => 'integer',
            'maxlen' => '5',
            'version' => 121,
            'order' => 8,
        );
        // rememberChannel come with murmur 1.2.3
        $settings['rememberchannel'] = array(
            'right' => 'SU',
            'name' => 'Remember channel',
            'type' => 'bool',
            'version' => 123,
            'order' => 9,
        );
        $settings['defaultchannel'] = array(
            'right' => 'SU',
            'name' => 'Default channel',
            'type' => 'integer',
            'maxlen' => '5',
            'version' => 120,
            'order' => 10,
        );
        $settings['registerpassword'] = array(
            'right' => 'SU',
            'name' => 'Register password',
            'type' => 'string',
            'version' => 120,
            'order' => 11,
        );
        $settings['registerhostname'] = array(
            'right' => 'SU',
            'name' => 'Register hostname',
            'type' => 'string',
            'version' => 120,
            'order' => 12,
        );
        $settings['registerurl'] = array(
            'right' => 'SU',
            'name' => 'Register URL',
            'type' => 'string',
            'version' => 120,
            'order' => 13,
        );
        $settings['username'] = array(
            'right' => 'SA',
            'name' => 'User name',
            'type' => 'string',
            'version' => 120,
            'order' => 14,
        );
        $settings['channelname'] = array(
            'right' => 'SA',
            'name' => 'Channel name',
            'type' => 'string',
            'version' => 120,
            'order' => 15,
        );
        $settings['textmessagelength'] = array(
            'right' => 'SA',
            'name' => 'Text message length',
            'type' => 'integer',
            'version' => 120,
            'order' => 16,
        );
        $settings['imagemessagelength'] = array(
            'right' => 'SA',
            'name' => 'Image message length',
            'type' => 'integer',
            'version' => 120,
            'order' => 17,
        );
        $settings['allowhtml'] = array(
            'right' => 'SA',
            'name' => 'Allow HTML',
            'type' => 'bool',
            'version' => 120,
            'order' => 18,
        );
        $settings['bonjour'] = array(
            'right' => 'SA',
            'name' => 'Bonjour',
            'type' => 'bool',
            'version' => 120,
            'order' => 19,
        );
        $settings['certrequired'] = array(
            'right' => 'SU',
            'name' => 'Certificate required',
            'type' => 'bool',
            'version' => 120,
            'order' => 20,
        );

        $settings['opusthreshold'] = array(
            'right' => 'SA',
            'name' => 'Opus threshold',
            'type' => 'integer',
            'version' => 124,
            'order' => 21,
        );
        $settings['channelnestinglimit'] = array(
            'right' => 'SA',
            'name' => 'Channel nesting limit',
            'type' => 'integer',
            'version' => 124,
            'order' => 22,
        );
        $settings['suggestpositional'] = array(
            'right' => 'SU',
            'name' => 'Suggest positional',
            'type' => 'bool',
            'version' => 124,
            'order' => 23,
        );
        $settings['suggestpushtotalk'] = array(
            'right' => 'SU',
            'name' => 'Suggest push-to-talk',
            'type' => 'bool',
            'version' => 124,
            'order' => 24,
        );
        $settings['welcometext'] = array(
            'right' => 'SU',
            'name' => 'Welcome text',
            'type' => 'string',
            'version' => 120,
            'order' => 25,
        );
        $settings['certificate'] = array(
            'right' => 'SU',
            'name' => 'Certificate',
            'type' => 'string',
            'version' => 120,
            'order' => 26,
        );
        $settings['key'] = array(
            'right' => 'SU',
            'name' => 'Certificate key',
            'type' => 'string',
            'version' => 120,
            'order' => 27,
        );
        // Sanity
        foreach ($settings as $key => $array) {
            if ($array['version'] > $murmurVersion) {
                unset($settings[$key]);
            }
        }
        return $settings;
    }
}

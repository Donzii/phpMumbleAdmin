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

$module->set('profileName', $PMA->userProfile['name']);
$module->set('isPublic', $PMA->userProfile['public']);
$module->set('host', $PMA->userProfile['host']);
$module->set('port', $PMA->userProfile['port']);
$module->set('timeout', $PMA->userProfile['timeout']);
$module->set('secret', $PMA->userProfile['secret']);
$module->set('httpAddr', $PMA->userProfile['http-addr']);

/**
* Setup connection.
*/
PMA_MurmurMetaHelper::connection();
/**
* Setup Iceinfos.
*/
$module->IceInfos = array();
/**
* Php Ice version.
*/
$module->IceInfos[] = array('php-Ice', PMA_ICE_STR);
/**
* Get murmur version.
*/
if ($PMA->meta->isConnected()) {
    $version = $PMA->meta->getVersion('txt');
} else {
    $version = 'Not connected';
}
$module->IceInfos[] = array('Murmur', $version);
/**
* Get slices infos.
*/
if (PMA_ICE_INT > 0 && PMA_ICE_INT < 30400) {
    $sliceProfilesFile = ini_get('ice.profiles');
    $module->IceInfos[] = array('ice.slice', ini_get('ice.slice'));
    $module->IceInfos[] = array('ice.profiles', $sliceProfilesFile);
}
/**
* Default profile button
*/
$module->addDefaultButton = (
    $PMA->meta->isConnected()
    && $PMA->userProfile['public'] === true
    && $PMA->userProfile['id'] !== $PMA->config->get('default_profile')
);
/**
* Delete profile button
*/
$module->addDeleteProfileButton = ($PMA->profiles->total() > 1);

/**
* Slices Ice profiles
*/
if (PMA_ICE_INT > 0) {
    if (PMA_ICE_INT < 30400) {
        // icePHP 3.2 / 3.3 - Setup $sliceProfilesList only if web master have activated slice profiles.
        if (is_readable($sliceProfilesFile)) {
            $sliceProfilesList = parse_ini_file($sliceProfilesFile, true);
            if ($PMA->config->get('debug') > 0) {
                // Add an invalid slice profile for debuging
                $sliceProfilesList['DEBUG_INVALID_PROFILE'] = array('ice.slice' => '');
            }
        } else {
            $PMA->messageError(
                'PMA has detected that slices profiles are activated but cannot read <b>"'
                .$sliceProfilesFile.'"</b>. Failed to get slice profile list.'
            );
        }
        if (isset($sliceProfilesList)) {
            $module->slicesIceProfiles = array();
            foreach ($sliceProfilesList as $name => $array) {
                if (! isset($array['ice.slice'])) {
                    continue;
                }
                $option = new stdClass();
                $option->name = $name;
                $option->select = ($PMA->userProfile['slice_profile'] === $name);
                $module->slicesIceProfiles[] = $option;
            }
        }
    /**
    * Slices php profiles
    */
    } else {
        $module->slicesPhpProfiles = array();
        if (PMA_ICE_INT >= 30400 && PMA_ICE_INT <= 30500) {
            $dir =PMA_DIR_SLICE_PHP_34;
            $custom = PMA_DIR_SLICE_PHP_CUSTOM_34;
            $info = ' - Ice 3.4';
        } else {
            $dir =PMA_DIR_SLICE_PHP_35;
            $custom = PMA_DIR_SLICE_PHP_CUSTOM_35;
            $info = ' - Ice 3.5';
        }
        /**
        * Scan directories.
        */
        $scan = scanDir($dir);
        $scan2 = @scanDir($custom);
        if (is_array($scan2)) {
            $scan = array_merge($scan, $scan2);
        }
        foreach ($scan as $filename) {
            if (substr($filename, -4) === '.php') {
                $name = substr($filename, 0, -4);
                $option = new stdClass();
                $option->name = $name.$info;
                $option->filename = $filename;
                $option->select = ($PMA->userProfile['slice_php'] === $filename);
                $module->slicesPhpProfiles[] = $option;
            }
        }
    }
}
/**
* Setup JS popups
*/
$PMA->widgets->newHiddenPopup('profileAdd');
if ($module->addDeleteProfileButton) {
    $PMA->widgets->newHiddenPopup('profileDelete');
}

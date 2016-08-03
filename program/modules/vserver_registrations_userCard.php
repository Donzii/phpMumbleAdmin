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

$module->set('login', $registration->name);
$module->set('email', $registration->email);
$module->certificat = $registration->cert;
$module->showCertificatHash = $PMA->user->isMinimum(PMA_USER_SUPERUSER_RU);
// Show avatar. Memo : There is a bug with getTexture() before murmur 1.2.3
$module->showAvatar = (
    $PMA->meta->getVersion('int') >= 123 &&
    ($PMA->user->isMinimum(PMA_USER_ROOTADMIN) OR ! $PMA->config->get('show_avatar_sa'))
);
$module->deleteAccount = (
    $registration->id > 0 &&
    ($PMA->user->isMinimum(PMA_USER_SUPERUSER_RU) OR $PMA->config->get('RU_delete_account'))
);

PMA_sandBoxHelper::create($registration->desc);
/**
* Setup regitered user status.
*/
$status = registered_is_online($registration->id, $module->onlineUsersList);
$module->statusLink = false;
if ($status['txt'] === 'on') {
    if ($PMA->user->isMinimum(PMA_USER_SUPERUSER_RU)) {
        $module->statusLink = true;
        $module->set('statusUrl', $status['url']);
    }
    $module->statusText = $TEXT['online'];
    $module->statusCss = 'on';
} else {
    $module->statusText = $TEXT['offline'];
    $module->statusCss = 'off';
}
/**
* Setup last activity.
* Memo : Last activity come with murmur 1.2.3
*/
if ($PMA->meta->getVersion('int') >= 123) {
    if (is_int($registration->last_activity)) {
        $ts = PMA_datesHelper::datetimeToTimestamp($registration->last_activity);
        $module->set('lastActivity', PMA_datesHelper::uptime(time() - $ts));
        $module->set('lastActivityTitle', strftime($PMA->dateTimeFormat, $ts));
    } else {
        $module->set('lastActivity', '');
        $module->set('lastActivityTitle', '');
    }
}
/**
* Check PHP memory limit (>32M).
* getTexture return huge array with big avatars images (almost 20M for an avatar of 128k, 60M for 450K).
* A minimum of 32M memory limit is required, 64M to avoid any kind of php fatal error on memory limit.
* MEMO:
* Mumble refuse avatar superior than 500K, even with "imagemessagelength" > 1310720 (1.28M).
*/
if ($module->showAvatar) {
    $srvLimit = (int)$prx->getParameter('imagemessagelength');
    $phpLimit = getIntegerMemoryLimit();
    if ($srvLimit <= 131072) {
        if ($phpLimit < (32*1000*1024)) {
            $module->showAvatar = false;
            $PMA->debugError('Php memory limit is too low (<32M). Displaying avatar has been disabled.');
        }
    } else {
        if ($phpLimit < (64*1000*1024)) {
            $module->showAvatar = false;
            $PMA->debugError('Php memory limit is too low (<64M). Displaying avatar has been disabled.');
        }
    }
}
/**
* Construct avatar.
*/
if ($module->showAvatar) {
    $start = microtime();
    $texture = $prx->getTexture($registration->id);
    $PMA->debug('getTexture duration '.PMA_statsHelper::duration($start), 3);

    $module->avatar = new PMA_MumbleAvatar($texture);
    $module->avatar->setProfile_id($PMA->cookie->get('profile_id'));
    $module->avatar->setServer_id($module->vserverID);
    $module->avatar->setUser_id($registration->id);

    $start = microtime();
    $module->avatar->constructSRC();
    $PMA->debug('constructSRC duration '.PMA_statsHelper::duration($start), 3);
}
/**
* Setup avatar delete link
*/
$module->deleteAvatar = ($module->showAvatar && ! $module->avatar->isEmpty());
/**
* Setup popups.
*/
$PMA->widgets->newHiddenPopup('mumbleRegistrationEditor');
if ($module->deleteAvatar) {
    $PMA->widgets->newHiddenPopup('mumbleRegistrationDeleteAvatar');
}
if ($module->deleteAccount) {
    $PMA->widgets->newHiddenPopup('mumbleRegistrationDelete');
}

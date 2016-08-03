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
* Check for auto-update.
*/
if ($PMA->user->isMinimum(PMA_USER_ROOTADMIN)) {
    $updates = new PMA_updates();
    $updates->setCacheParameters($PMA->app->get('updates'));
    if ($updates->isAutoCheckRequired($PMA->config->get('update_check'))) {
        $PMA->debug('Auto-check PMA updates requested.');
        if ($updates->check()) {
            $PMA->message(array('new_pma_version', $updates->get('current_version')));
        }
        $PMA->app->set('updates', $updates->getCacheParameters());
    }
}
/**
* Get ICE connection
*/
PMA_MurmurMetaHelper::connection();
if (! $PMA->meta->isConnected()) {
    throw new PMA_moduleException();
}
/**
* Get servers datas from $prx
*/
$module->allServers = $PMA->meta->getAllServers();
$module->allBootedServers = $PMA->meta->getBootedServers();
/**
* Admins sanity:
* remove servers that an admin dont have access.
*/
if ($PMA->user->is(PMA_USER_ADMIN)) {
    $all = array();
    $booted = array();
    foreach ($module->allServers as $prx) {
        if ($PMA->user->checkServerAccess($prx->getSid())) {
            $all[] = $prx;
            if (in_array($prx, $module->allBootedServers)) {
                $booted[] = $prx;
            }
        }
    }
    $module->allServers = $all;
    $module->allBootedServers = $booted;
}
/**
* Get murmur version.
*/
$murmur = sprintf($TEXT['murmur_vers'], '<b>'.$PMA->meta->getVersion('str').'</b>');
/**
* Get uptime and "started at".
*/
$uptime = $PMA->meta->getUptime();
if (is_int($uptime)) {
    $ts = time() - $uptime;
    $date = strftime($PMA->cookie->get('date'), $ts);
    $time = strftime($PMA->cookie->get('time'), $ts);
    $uptime = PMA_datesHelper::uptime($uptime);
    $uptime = ' (<span class="help" title="'.sprintf($TEXT['started_at'], $date, $time).'">'.$uptime.'</span>)';
}
/**
* Count servers.
*/
$totalAll = count($module->allServers);
$totalBooted = count($module->allBootedServers);
$totalServers = sprintf($TEXT['total_srv'], '<mark>'.$totalBooted.'</mark> / <b>'.$totalAll.'</b>');
/**
* Count users.
*/
if (
    $PMA->config->get('show_total_users') &&
    ($PMA->user->isMinimum(PMA_USER_ROOTADMIN) OR ! $PMA->config->get('show_total_users_sa'))
) {
    $totalUsers = 0;
    foreach ($module->allBootedServers as $prx) {
        $totalUsers += count($prx->getUsers());
    }
    $totalUsers = sprintf($TEXT['total_users'], '<mark>'.$totalUsers.'</mark>');
}
/**
* Setup info panel.
*/
$PMA->skeleton->addInfoPanel($murmur.$uptime);
$PMA->skeleton->addInfoPanel($totalServers);
if (isset($totalUsers)) {
    $PMA->skeleton->addInfoPanel($totalUsers);
}
if (isset($updates) && $updates->get('new_version')) {
    $text = sprintf($TEXT['pma_available'], $updates->get('current_version'));
    $isUpdate = '<a href="'.PMA_updates::DOWNLOAD_URL.'">'.$text.'</a>';
    $PMA->skeleton->addInfoPanel($isUpdate , 'occasional right');
}

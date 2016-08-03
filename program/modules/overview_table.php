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

$PMA->widgets->newWidget('widget_tablePagingMenu');
/**
* Setup toolbar buttons parameters
*/
$module->defaultSettingsButton = $PMA->user->isMinimum(PMA_USER_ROOTADMIN);
$module->addServerButton = $PMA->user->isMinimumAdminFullAccess();
$module->sendMessageButton = $PMA->user->isMinimum(PMA_USER_ADMIN);
$module->userCanDelete = $PMA->user->isMinimumAdminFullAccess();
/**
* Setup captions
*/
$PMA->skeleton->addCaption(IMG_SPACE_16, $TEXT['srv_active'], 'button on');
$PMA->skeleton->addCaption(IMG_SPACE_16, $TEXT['srv_inactive'], 'button off');
$PMA->skeleton->addCaption('images/gei/hot_16.png', $TEXT['reset_srv_info'], 'button');
$PMA->skeleton->addCaption(IMG_CONN_16, $TEXT['conn_to_srv'], 'button');
$PMA->skeleton->addCaption('images/xchat/red_16.png', $TEXT['webaccess_on'], 'button');
$PMA->skeleton->addCaption(IMG_2_DELETE_16, $TEXT['webaccess_off'], 'button');
$PMA->skeleton->addCaption(IMG_TRASH_16, $TEXT['del_srv'], 'button');
/**
* Setup common connection url parameters.
*/
$connectionUrl = new PMA_MurmurUrl();
$connectionUrl->setCustomLogin($PMA->cookie->get('vserver_login'));
$connectionUrl->setDefaultLogin($PMA->user->login);
$connectionUrl->setGuestLogin('Guest_'.genRandomChars(5));
$connectionUrl->setCustomHttpAddr($PMA->userProfile['http-addr']);
if ($PMA->config->get('murmur_version_url')) {
    $connectionUrl->setMurmurVersion($PMA->meta->getVersion('str'));
}
/**
* Setup table
*/
$tableNav = $PMA->router->getTableNavigation('key');

$module->table = new PMA_table_overview($module->allServers);
$module->table->setTimeFormat($PMA->cookie->get('time'));
$module->table->setDateFormat($PMA->cookie->get('date'));
$module->table->setMaxPerPage($PMA->config->get('table_overview'));
$module->table->setBooted($module->allBootedServers);
$module->table->setConnectionUrl($connectionUrl);
$module->table->setShowOnlineUsers(
    $PMA->config->get('show_online_users') &&
    ($PMA->user->isMinimum(PMA_USER_ROOTADMIN) OR ! $PMA->config->get('show_online_users_sa'))
);
$module->table->setShowUptime(
    method_exists('Murmur_Server', 'getUptime') &&
    $PMA->config->get('show_uptime') &&
    ($PMA->user->isMinimum(PMA_USER_ROOTADMIN) OR ! $PMA->config->get('show_uptime_sa'))
);
$module->table->setUserCanDelete($module->userCanDelete);
if (isset($_SESSION['page_vserver']['id'])) {
    $module->table->setSelectedSID($_SESSION['page_vserver']['id']);
}
$module->table->setNavigation($tableNav);

$module->table->contructDatas();

$module->table->sortColumn('status', 'S', true);
$module->table->sortColumn('key', 'id', true);
/**
* Setup popups
*/
if ($module->addServerButton) {
    $PMA->widgets->newHiddenPopup('serverAdd');
}
if ($module->sendMessageButton) {
    $PMA->widgets->newHiddenPopup('serversMessage');
}
$PMA->widgets->newHiddenPopup('serverReset');
if ($module->userCanDelete) {
    $PMA->widgets->newHiddenPopup('serverDelete');
}

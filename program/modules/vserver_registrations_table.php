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

// UserLastActive come with murmur 1.2.3
$module->displayLastActivity = ($PMA->meta->getVersion('int') >= 123);

$PMA->widgets->newHiddenPopup('mumbleRegistrationAdd');
$PMA->widgets->newHiddenPopup('mumbleRegistrationDeleteID');
$PMA->widgets->newWidget('widget_tablePagingMenu');
/**
* Setup captions
*/
$PMA->skeleton->addCaption(IMG_SPACE_16, $TEXT['user_is_online'], 'button on');
$PMA->skeleton->addCaption(IMG_SPACE_16, $TEXT['offline'], 'button off');
$PMA->skeleton->addCaption('images/mumble/comment.png', $TEXT['have_a_comm']);
$PMA->skeleton->addCaption(IMG_OK_16, $TEXT['have_a_cert']);
$PMA->skeleton->addCaption(IMG_TRASH_16, $TEXT['delete_acc'], 'button');

$tableNav = $PMA->router->getTableNavigation('uid');

$module->table = new PMA_table_murmurRegistrations($getRegisteredUsers);
$module->table->setMaxPerPage($PMA->config->get('table_users'));
$module->table->setConnectedUsers($module->onlineUsersList);
$module->table->setDateTimeFormat($PMA->dateTimeFormat);
$module->table->setNavigation($tableNav);
$module->table->setPrx($prx);

if (isset($_SESSION['search']['registrations'])) {
    // Search only for users logins.
    $module->table->search($_SESSION['search']['registrations']);
}

$module->table->sortColumn('status', 'S', true);
$module->table->sortColumn('uid', 'id', true);
$module->table->sortColumn('login', $TEXT['login']);
$module->table->sortColumn('email', $TEXT['email_addr']);
if ($module->displayLastActivity) {
    $module->table->sortColumn('lastActivity', $TEXT['last_activity']);
}
$module->table->sortColumn('comment', 'C', true);
$module->table->sortColumn('hash', 'H', true);

$module->table->contructDatas();

/**
* Setup search widget
*/
$PMA->widgets->newWidget('widget_search');

$searchWidget = new PMA_searchWidget();
$searchWidget->setCMDroute('murmur_registrations');
$searchWidget->setCMDname('registrations_search');
if (isset($_SESSION['search']['registrations'])) {
    $searchWidget->setSearchValue($_SESSION['search']['registrations']);
    $searchWidget->setTotalFound($module->table->searchFound);
    $searchWidget->setRemoveSearchHREF('?cmd=murmur_registrations&amp;reset_registrations_search');
}

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

$tableNav = $PMA->router->getTableNavigation('id');

$admins = array();
foreach($PMA->admins->getAllDatas() as $admin) {
    if ($PMA->user->isSuperior($admin['class'])) {
        // Admin access datas
        $admin['accessText'] = array();
        foreach ($admin['access'] as $id => $servers) {
            $name = $PMA->profiles->getName($id);
            if ($servers === '*') {
                $text = sprintf($TEXT['full_access'], $name);
            } else {
                $count = count(explode(';', $servers));
                $text = sprintf($TEXT['srv_access'], $name, $count);
            }
            $admin['accessText'][] = $text;
        }
        $admins[] = $admin;
    }
}

$module->table = new PMA_table_admins($admins);
$module->table->setDateTimeFormat($PMA->dateTimeFormat);
$module->table->setNavigation($tableNav);

$module->table->contructDatas();

$module->table->sortColumn('class', $TEXT['class']);
$module->table->sortColumn('id', 'id', true);
$module->table->sortColumn('login', $TEXT['login']);
$module->table->sortColumn('last_conn', $TEXT['last_conn']);
/**
* Setup popups
*/
$PMA->widgets->newHiddenPopup('adminAdd');
$PMA->widgets->newHiddenPopup('adminDelete');



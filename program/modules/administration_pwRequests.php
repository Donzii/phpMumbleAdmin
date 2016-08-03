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

$pwRequests = new PMA_datas_pwRequests();

$tableNav = $PMA->router->getTableNavigation('end');

$module->table = new PMA_table_pwRequets($pwRequests->getAllDatas());
$module->table->setTimeFormat($PMA->cookie->get('time'));
$module->table->setDateFormat($PMA->cookie->get('date'));
$module->table->setNavigation($tableNav);

$module->table->contructDatas();

$module->table->sortColumn('end', $TEXT['end']);
$module->table->sortColumn('login', $TEXT['login']);
$module->table->sortColumn('ip', $TEXT['ip_addr']);
$module->table->sortColumn('profile_id', 'iid', true);

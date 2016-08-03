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

$tableNav = $PMA->router->getTableNavigation('ip');

$module->table = new PMA_table_bans($PMA->bans->getAllDatas());
$module->table->setDateTimeFormat($PMA->dateTimeFormat);
$module->table->setNavigation($tableNav);

$module->table->contructDatas();

$module->table->sortColumn('ip', $TEXT['ip_addr']);
$module->table->sortColumn('start', $TEXT['started']);
$module->table->sortColumn('duration', $TEXT['end']);
$module->table->sortColumn('comment', $TEXT['comment']);

//$PMA->bans->add('127.0.0.1', 99999, 'TEST');

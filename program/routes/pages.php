<?php

 /**
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

switch ($PMA->user->class) {
    case PMA_USER_SUPERADMIN:
    case PMA_USER_ROOTADMIN:
        $PMA->router->page->addRoute('configuration');
        $PMA->router->page->addRoute('administration');
        $PMA->router->page->addRoute('overview');
        $PMA->router->page->addRoute('vserver');
        $PMA->router->page->setDefaultRoute('overview');
        break;
    case PMA_USER_ADMIN:
        $PMA->router->page->addRoute('configuration');
        $PMA->router->page->addRoute('overview');
        $PMA->router->page->addRoute('vserver');
        $PMA->router->page->setDefaultRoute('overview');
        break;
    case PMA_USER_SUPERUSER:
    case PMA_USER_SUPERUSER_RU:
    case PMA_USER_MUMBLE:
        $PMA->router->page->addRoute('configuration');
        $PMA->router->page->addRoute('vserver');
        $PMA->router->page->setDefaultRoute('vserver');
        break;
    case PMA_USER_UNAUTH:
        $PMA->router->page->addRoute('authentication');
        break;
    case PMA_USER_INSTALLATION:
        $PMA->router->page->addRoute('installation');
        break;
}
$PMA->router->checkNavigation('page');


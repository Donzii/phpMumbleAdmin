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

$PMA->router->tab->addRoute('options');
if ($PMA->user->isMinimum(PMA_USER_ROOTADMIN)) {
    $PMA->router->tab->addRoute('ICE');
    $PMA->router->tab->addRoute('settings');
    $PMA->router->tab->addRoute('debug');
}
$PMA->router->checkNavigation('tab');

switch($PMA->router->getRoute('tab')) {

    case 'options':

        if (isset($_GET['set_default_options'])) {
            $PMA->modules->enable('config_options_defaultsEditor');
        } elseif (isset($_GET['edit_SuperAdmin'])) {
            $PMA->widgets->newPopup('optionsSuperAdminEditor');
        } elseif (isset($_GET['change_your_password'])) {
            $PMA->widgets->newPopup('optionsPasswordEditor');
        } else {
            $PMA->modules->enable('config_options_miscEditor');
        }
        break;

    case 'ICE':
        if (isset($_GET['delete_profile'])) {
            $PMA->widgets->newPopup('profileDelete');
        } elseif (isset($_GET['add_profile'])) {
            $PMA->widgets->newPopup('profileAdd');
        } else {
            $PMA->modules->enable('config_Ice_profileEditor');
        }
        break;

    case 'settings':

        $PMA->router->subtab->addRoute('general');
        $PMA->router->subtab->addRoute('mumbleUsers');
        $PMA->router->subtab->addRoute('autoban');
        $PMA->router->subtab->addRoute('logs');
        $PMA->router->subtab->addRoute('tables');
        $PMA->router->subtab->addRoute('smtp');
        $PMA->router->subtab->addRoute('extViewer');
        $PMA->router->checkNavigation('subtab');

        switch($PMA->router->getRoute('subtab')) {
            case 'general':
                $PMA->modules->enable('config_settings_general');
            break;
            case 'mumbleUsers':
                if (isset($_GET['pw_requests_options'])) {
                    $PMA->modules->enable('config_settings_pwRequestsOptions');
                } else {
                    $PMA->modules->enable('config_settings_mumbleUsers');
                }
            break;
            case 'autoban':
                $PMA->modules->enable('config_settings_autoban');
            break;
            case 'logs':
                $PMA->modules->enable('config_settings_logs');
            break;
            case 'tables':
                $PMA->modules->enable('config_settings_tables');
            break;
            case 'smtp':
                $PMA->modules->enable('config_settings_smtp');
            break;
            case 'extViewer':
                $PMA->modules->enable('config_settings_extViewer');
            break;
        }
        break;

    case 'debug':

        $PMA->router->subtab->addRoute('options');
        $PMA->router->subtab->addRoute('files');
        $PMA->router->subtab->addRoute('languagesDiff');
        $PMA->router->subtab->addRoute('languagesCompare');
        $PMA->router->checkNavigation('subtab');

        switch($PMA->router->getRoute('subtab')) {
            case 'options':
                $PMA->modules->enable('config_debug_options');
            break;
            case 'files':
                $PMA->modules->enable('config_debug_files');
            break;
            case 'languagesDiff':
                $PMA->modules->enable('config_debug_languagesDiff');
            break;
            case 'languagesCompare':
                $PMA->modules->enable('config_debug_languagesCompare');
            break;
        }
        break;
}

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

$widget = new serversPanelWidget();

$widget->displayServersList = false;
$widget->displayRefreshButton = false;
$widget->displayServerName = false;

if ($PMA->user->isMinimum(PMA_USER_ADMIN)) {

    $cache = PMA_serversCacheHelper::get('htEnc');

    if (isset($cache['vservers'])) {
        $widget->displayServersList = true;
        $widget->serversList = array();

        if ($PMA->user->isMinimum(PMA_USER_ROOTADMIN)) {
            $widget->displayRefreshButton = true;
            if ($PMA->config->get('ddl_show_cache_uptime')) {
                $uptime = PMA_datesHelper::uptime(time() - $cache['cache_time']);
                $TEXT['select_server'] .= ' ('.$uptime.')';
            }
        }

        foreach ($cache['vservers'] as $array) {
            $id = $array['id'];
            if ($PMA->user->is(PMA_USER_ADMIN) && ! $PMA->user->checkServerAccess($id)) {
                // Don't add servers that's admins haven't access
                continue;
            }
            if (! isset($widget->firstServerID)) {
                $widget->firstServerID = $id;
            }
            if (isset($current_vserver_reached)) {
                $widget->nextServerID = $id;
                unset($current_vserver_reached);
            }
            $widget->lastServerID = $id;

            $data = new stdClass();
            $data->css = '';
            $data->disabled = false;
            $data->id = $id;
            $data->text = $id.'# '.$array['name'];

            // Disallow to select the virtual server where we are.
            if ($PMA->router->getRoute('page') === 'vserver' && $id === $_SESSION['page_vserver']['id']) {
                $current_vserver_reached = true;
                $data->css = 'selected disabled';
                $data->disabled = true;
                if (isset($prev)) {
                    $widget->NextServerID = $prev;
                }
            } elseif (isset($_SESSION['page_vserver']['id']) && $id === $_SESSION['page_vserver']['id']) {
                $data->css = 'selected';
            }
            $prev = $id;

            $widget->serversList[] = $data;
        }

        // PREV / NEXT buttons
        if ($PMA->router->getRoute('page') === 'vserver' && count($widget->serversList) > 1) {
            // First
            if ($_SESSION['page_vserver']['id'] !== $widget->firstServerID) {
                $widget->setButton(1, $widget->firstServerID, 'images/tango/page_first_16.png');
            }
            // previous
            if (isset($widget->NextServerID)) {
                $widget->setButton(2, $widget->NextServerID, 'images/tango/page_prev_16.png');
            }
            // next
            if (isset($widget->nextServerID)) {
                $widget->setButton(3, $widget->nextServerID, 'images/tango/page_next_16.png');
            }
            // Last
            if ($_SESSION['page_vserver']['id'] !== $widget->lastServerID) {
                $widget->setButton(4, $widget->lastServerID, 'images/tango/page_last_16.png');
            }
        }
    }
}

if (isset($module->vserverName)) {

    $widget->displayServerName = true;

    $widget->set('serverID', $module->vserverID);
    $widget->set('serverName', $module->vserverName);
    $widget->infoPanelSrc = $PMA->cookie->get('infoPanel') ? IMG_2_DELETE_16 : IMG_2_ADD_16;
    $widget->infoPanelJs = SUHOSIN_COOKIE_ENCRYPT ? '' : 'onClick="return toggleInfoPanel();"';
}

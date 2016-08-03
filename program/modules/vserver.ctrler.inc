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
* *******************************************
* Common controller for vserver modules.
* *******************************************
*/

/**
* Server ID
*/
// if (isset($_GET['sid']) && ctype_digit($_GET['sid'])) {
//     $_GET['sid'] =  (int)$_GET['sid'];
//     if (isset($_SESSION['page_vserver']['id']) && $_SESSION['page_vserver']['id'] !== $_GET['sid']) {
//         unset($_SESSION['page_vserver']);
//         if ($PMA->router->page->isNewUserRoute()) {
//             // Keep tab if we use vserver navigation.
//             $PMA->router->tab->setCurrentRoute('channels');
//         }
//     }
//     $_SESSION['page_vserver']['id'] = $_GET['sid'];
// }

/**
* Force SuperUsers and registered users to access to their own vserver.
*/
if ($PMA->user->is(PMA_USERS_MUMBLE)) {
    $_SESSION['page_vserver']['id'] = $PMA->user->mumbleSID;
}
/**
* Setup server ID.
*/
$module->vserverID = $_SESSION['page_vserver']['id'];
/**
* Check if an admin has access to the current server id.
*/
if ($PMA->user->is(PMA_USER_ADMIN)) {
    if (! $PMA->user->checkServerAccess($module->vserverID)) {
        $PMA->router->page->setCurrentRoute('overview');
        $PMA->message('Access_denied_to_vserver '.$module->vserverID);
        unset($_SESSION['page_vserver']);
        $PMA->redirection();
    }
}
/**
* Setup Ice connection.
*/
PMA_MurmurMetaHelper::connection();
if (! $PMA->meta->isConnected()) {
    throw new PMA_moduleException();
}

/**
* Setup $prx.
*/
if (is_null($prx = $PMA->meta->getServer($module->vserverID))) {
    if ($PMA->user->isMinimum(PMA_USER_ADMIN)) {
        $PMA->router->page->setCurrentRoute('overview');
        unset($_SESSION['page_vserver']);
    } else {
        // Logout mumble users
        $PMA->logout();
    }
    $PMA->messageError('Murmur_InvalidServerException');
    $PMA->redirection();
}
/**
* Setup vserver name.
* Setup booted flag.
*/
$module->vserverName = $prx->getParameter('registername');
$module->vserverIsBooted = $prx->isRunning();
/**
* Setup SuperUserRu rights.
* Get online user list.
*/
if ($module->vserverIsBooted) {
    if ($PMA->user->is(PMA_USERS_REGISTERED)) {
        if ($PMA->config->get('SU_auth') && $PMA->config->get('SU_ru_active')) {
            $prx->getACL(0, $aclList, $groupList, $inherit);
            // Memo: redirection is required on class update.
            if (PMA_MurmurAclHelper::isSuperUserRu($PMA->user->mumbleUID, $aclList)) {
                if ($PMA->user->is(PMA_USER_MUMBLE)) {
                    $PMA->user->setClass(PMA_USER_SUPERUSER_RU);
                    $PMA->redirection();
                }
            } else {
                if ($PMA->user->is(PMA_USER_SUPERUSER_RU)) {
                    $PMA->user->setClass(PMA_USER_MUMBLE);
                    $PMA->redirection();
                }
            }
        } else {
            if ($PMA->user->is(PMA_USER_SUPERUSER_RU)) {
                $PMA->user->setClass(PMA_USER_MUMBLE);
                $PMA->redirection();
            }
        }
    }
    /**
    * Get online user list.
    */
    $module->onlineUsersList = $prx->getUsers();
    $module->totalOnlineUsers = count($module->onlineUsersList);
}
/**
* Setup infoPanel
*/
$module->showInfoPanel = ($PMA->cookie->get('infoPanel') OR ! $module->vserverIsBooted);
if ($module->showInfoPanel) {
    /**
    * InfoPanel : Start / stop button
    */
    if (
        $PMA->user->isMinimum(PMA_USER_ADMIN) OR
        $PMA->user->is(PMA_USERS_SUPERUSERS) && $PMA->config->get('SU_start_vserver')
    ) {
        $url = '?cmd=overview&amp;toggle_server_status='.$module->vserverID;
        if ($module->vserverIsBooted) {
            $css = 'on';
            $title = $TEXT['srv_active'];
        } else {
            $css = 'off';
            $title = '';
        }
        $PMA->skeleton->addInfoPanel('<a href="'.$url.'" title="'.$title.'"><img src="'.IMG_SPACE_16.'" class="button '.$css.'" alt="" /></a>');
        if (! $module->vserverIsBooted) {
            $PMA->skeleton->addInfoPanel($TEXT['srv_inactive'], 'occasional');
        }
    }
    /**
    * InfoPanel : connection button.
    */
    if ($module->vserverIsBooted) {
        $module->connectionUrl = new PMA_MurmurUrl();
        $module->connectionUrl->setCustomLogin($PMA->cookie->get('vserver_login'));
        $module->connectionUrl->setDefaultLogin($PMA->user->login);
        $module->connectionUrl->setGuestLogin('Guest_'.genRandomChars(5));
        $module->connectionUrl->setServerPassword($prx->getParameter('password'));
        $module->connectionUrl->setCustomHttpAddr($PMA->userProfile['http-addr']);
        $module->connectionUrl->setDefaultHttpAddr($prx->getParameter('host'));
        $module->connectionUrl->setPort($prx->getParameter('port'));
        if ($PMA->config->get('murmur_version_url')) {
            $module->connectionUrl->setMurmurVersion($PMA->meta->getVersion('str'));
        }
        $url = $module->connectionUrl->getUrl();
        $PMA->skeleton->addInfoPanel('<a href="'.$url.'"><img src="'.IMG_CONN_16.'" class="button" title="'.$TEXT['conn_to_srv'].'" alt="" /></a>', 'right');
        /**
        * InfoPanel : Get server uptime and started at.
        */
        $uptime = $prx->getUptime();
        if (is_int($uptime)) {
            $ts = time() - $uptime;
            $date = strftime($PMA->cookie->get('date'), $ts);
            $time = strftime($PMA->cookie->get('time'), $ts);
            $uptime = PMA_datesHelper::uptime($uptime);
            $uptime = '<span class="help" title="'.sprintf($TEXT['started_at'], $date, $time).'">'.$uptime.'</span>';
            $PMA->skeleton->addInfoPanel(sprintf($TEXT['fill_uptime'], $uptime));
        }
        /**
        * InfoPanel : total of users.
        */
        $total = $module->totalOnlineUsers;
        $max = $prx->getParameter('users');
        $PMA->skeleton->addInfoPanel(sprintf($TEXT['fill_users'], HTML::onlineUsers($total, $max)));
    }
}
/**
* Error message if the vserver is not booted.
*/
if (! $module->vserverIsBooted) {
    if ($PMA->user->isMinimum(PMA_USER_SUPERUSER_RU)) {
        $PMA->messageError('vserver_offline_info');
    } else {
        $PMA->messageError('vserver_offline');
    }
}

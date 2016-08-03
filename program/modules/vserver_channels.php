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
* Setup action menu class.
*/
class channelsMenuStruc
{
    public $href;
    public $img = IMG_SPACE_16;
    public $text;
    public $js;
}

/**
* Booted server is required.
*/
if (! $module->vserverIsBooted) {
    throw new PMA_moduleException();
}
/**
* Setup viewer widget.
*/
$PMA->widgets->newWidget('widget_viewer');
$PMA->skeleton->addCssFile('viewer.css');
/**
* Load language variables.
*/
pmaLoadLanguage('vserver_channels');
/**
* Get datas from $prx.
*/
$getTree = $prx->getTree();
$module->channelsList = $prx->getChannels();
$module->defaultChannelID = (int)$prx->getParameter('defaultchannel');
/**
* Setup actionMenu list.
* Setup channel state ( user or channel viewBox )
* Setup the total of channels.
*/
$module->actionMenu = array();
$totalChannels = count($module->channelsList);
/**
* Add occasional info panel : total of channels
*/
if ($module->showInfoPanel) {
    $PMA->skeleton->addInfoPanel(sprintf($TEXT['fill_channels'], '<mark>'.$totalChannels.'</mark>' ), 'occasional');
}
/**
* Setup captions
*/
$PMA->skeleton->addCaption('images/other/home_16.png', $TEXT['img_defaultchannel']);
$PMA->skeleton->addCaption('images/pma/tree_link_with.png', $TEXT['img_linked']);
$PMA->skeleton->addCaption('images/pma/tree_link_direct.png', $TEXT['img_link_direct']);
$PMA->skeleton->addCaption('images/pma/tree_link_indirect.png', $TEXT['img_link_undirect']);
$PMA->skeleton->addCaption('images/gei/padlock_16.png', $TEXT['img_channel']);
$PMA->skeleton->addCaption('images/tango/clock_16.png', $TEXT['img_temp']);
$PMA->skeleton->addCaption('images/mumble/comment.png', $TEXT['img_comment']);
$PMA->skeleton->addCaption('images/mumble/user_auth.png', $TEXT['img_auth']);
$PMA->skeleton->addCaption('images/xchat/red_16.png', $TEXT['img_recording']);
$PMA->skeleton->addCaption('images/tango/microphone_16.png', $TEXT['img_priorityspeaker']);
$PMA->skeleton->addCaption('images/mumble/user_suppressed.png', $TEXT['img_supressed']);
$PMA->skeleton->addCaption('images/mumble/user_muted.png', $TEXT['img_muted']);
$PMA->skeleton->addCaption('images/mumble/user_deafened.png', $TEXT['img_deafened']);
$PMA->skeleton->addCaption('images/mumble/user_selfmute.png', $TEXT['img_mute']);
$PMA->skeleton->addCaption('images/mumble/user_selfdeaf.png', $TEXT['img_deaf']);
/**
* Route channels.
*/
if (isset($_GET['channel']) && ctype_digit($_GET['channel'])) {
    $_GET['channel'] = (int)$_GET['channel'];
    // Remove acl id & group id if we change channel id
    if (isset($_SESSION['page_vserver']['cid']) && $_SESSION['page_vserver']['cid'] !== $_GET['channel']) {
        unset($_SESSION['page_vserver']['aclID'], $_SESSION['page_vserver']['groupID']);
    }
    $_SESSION['page_vserver']['cid'] = $_GET['channel'];
    // Remove user session id
    unset($_SESSION['page_vserver']['uSess']);
}
/**
* Route users sessions.
*/
if (isset($_GET['userSession'])) {
    list($id, $name) = explode('-', rawUrlDecode($_GET['userSession']), 2);
    $_SESSION['page_vserver']['uSess']['id'] = (int)$id;
    $_SESSION['page_vserver']['uSess']['name'] = $name;
    unset(
        $_SESSION['page_vserver']['cid'],
        $_SESSION['page_vserver']['aclID'],
        $_SESSION['page_vserver']['groupID']
    );
}
/**
* Check for valid channel ID
*/
if (isset($_SESSION['page_vserver']['cid'])) {
    if (! isset($module->channelsList[$_SESSION['page_vserver']['cid']])) {
        $PMA->messageError('Murmur_InvalidChannelException');
        unset(
            $_SESSION['page_vserver']['cid'],
            $_SESSION['page_vserver']['aclID'],
            $_SESSION['page_vserver']['groupID']
        );
    }
}
/**
* Check for valid user session ID
*/
if (isset($_SESSION['page_vserver']['uSess'])) {
    if (! isset($module->onlineUsersList[$_SESSION['page_vserver']['uSess']['id']])) {
        /**
        * User is not online anymore, search for a reconnection.
        */
        foreach ($module->onlineUsersList as $user) {
            if ($user->name === $_SESSION['page_vserver']['uSess']['name']) {
                $_SESSION['page_vserver']['uSess']['id'] = $user->session;
                $new_session_found = true;
                break;
            }
        }
        if (! isset($new_session_found)) {
            $PMA->messageError('Murmur_InvalidSessionException');
            unset($_SESSION['page_vserver']['uSess']);
        }
    }
}
/**
* Default route :
* Root channel, this is the only thing we are sure to find in a vserver.
*/
if (! isset($_SESSION['page_vserver']['cid']) && ! isset($_SESSION['page_vserver']['uSess'])) {
    $_SESSION['page_vserver']['cid'] = 0;
}
/**
* Setup viewer state and get channel or user object.
*/
if (isset($_SESSION['page_vserver']['cid'])) {
    $module->viewerState = 'channel';
    $module->channelObj = clone $module->channelsList[$_SESSION['page_vserver']['cid']];
} else {
    $module->viewerState = 'user';
    $module->sessionObj = clone $module->onlineUsersList[$_SESSION['page_vserver']['uSess']['id']];
}

$viewerBoxWidget = new stdClass();
$viewerBoxWidget->type = 'widget';
$viewerBoxWidget->id = null;

/**
* Channel menu
*/
if ($PMA->user->isMinimum(PMA_USER_SUPERUSER_RU)) {
    if (isset($_SESSION['page_vserver']['cid'])) {

        $PMA->router->subtab->addRoute('acl');
        $PMA->router->subtab->addRoute('groups');
        $PMA->router->subtab->addRoute('properties');
        $PMA->router->subtab->setDefaultRoute('properties');
        $PMA->router->checkNavigation('subtab');
        /**
        * Count users currently in the channel
        */
        $usersInChannel = 0;
        foreach ($module->onlineUsersList as $user) {
            if ($user->channel === $module->channelObj->id) {
                ++$usersInChannel;
            }
        }
        /**
        * Menu : connection to channel
        */
        $menu = new channelsMenuStruc();
        $menu->text = $TEXT['conn_to_channel'];
        if ($module->channelObj->id > 0) {
            $menu->href = $module->connectionUrl->getChannelUrl($module->channelsList, $module->channelObj->id);
            $menu->img = IMG_CONN_16;
        }
        $module->actionMenu[] = $menu;
        /**
        * Menu : add sub-channel
        */
        $menu = new channelsMenuStruc();
        $menu->text = $TEXT['add_channel'];
        if (! $module->channelObj->temporary) {
            $menu->href = '?action=add_channel';
            $menu->img = IMG_ADD_16;
            $menu->js = 'onClick="return popup(\'channelAdd\');"';
            $PMA->widgets->newHiddenPopup('channelAdd');
        }
        $module->actionMenu[] = $menu;
        /**
        * Menu : send a message
        */
        $menu = new channelsMenuStruc();
        $menu->href = '?action=messageToChannel';
        $menu->text = $TEXT['send_msg'];
        $menu->img = IMG_MSG_16;
        $menu->js = 'onClick="return popup(\'channelMessage\');"';
        $PMA->widgets->newHiddenPopup('channelMessage');
        $module->actionMenu[] = $menu;
        /**
        * Menu : move users out the channel
        */
        $menu = new channelsMenuStruc();
        $menu->text = $TEXT['move_user_off_chan'];
        if ($usersInChannel > 0 && $totalChannels > 1) {
            $menu->href = '?action=move_users_out&amp;viewerAction';
            $menu->img = IMG_UP_16;
        }
        $module->actionMenu[] = $menu;
        /**
        * Menu : Move users in the channel.
        */
        $menu = new channelsMenuStruc();
        $menu->text = $TEXT['move_user_in_chan'];
        if ($module->totalOnlineUsers > $usersInChannel) {
            $menu->href = '?action=move_users_in&amp;viewerAction';
            $menu->img = IMG_UP_16;
            $menu->js = 'onClick="return popup(\'channelMoveIn\');"';
            $PMA->widgets->newHiddenPopup('channelMoveIn');
        }
        $module->actionMenu[] = $menu;
        /**
        * Menu : link channel to others
        */
        $menu = new channelsMenuStruc();
        $menu->text = $TEXT['link_channel'];
        if ($totalChannels - count($module->channelObj->links) > 1) {
            $menu->href = '?action=link_channel&amp;viewerAction';
            $menu->img = 'images/tango/link_16.png';
        }
        $module->actionMenu[] = $menu;
        /**
        * Menu : unlink channel to others
        * Memo : Method is bugged before murmur 1.2.3.
        */
        $menu = new channelsMenuStruc();
        $menu->text = $TEXT['unlink_channel'];
        if (! empty($module->channelObj->links) && $PMA->meta->getVersion('int') >= 123) {
            $menu->href = '?action=unlink_channel&amp;viewerAction';
            $menu->img = IMG_CANCEL_16;
        }
        $module->actionMenu[] = $menu;
        /**
        * Menu : move channel
        */
        $menu = new channelsMenuStruc();
        $menu->text = $TEXT['move_channel'];
        if ($module->channelObj->id > 0) {
            $menu->href = '?action=move_channel&amp;viewerAction';
            $menu->img = IMG_UP_16;
        }
        $module->actionMenu[] = $menu;
        /**
        * Menu : delete channel
        */
        $menu = new channelsMenuStruc();
        $menu->text = $TEXT['del_channel'];
        if ($module->channelObj->id > 0) {
            $menu->href = '?action=delete_channel';
            $menu->img = IMG_DELETE_16;
            $menu->js = 'onClick="return popup(\'channelDelete\');"';
            $PMA->widgets->newHiddenPopup('channelDelete');
        }
        $module->actionMenu[] = $menu;

        /**
        * Setup widgets
        */
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'add_channel':
                    $viewerBoxWidget->type = 'popup';
                    $viewerBoxWidget->id = 'channelAdd';
                    break;
                case 'link_channel':
                    $viewerBoxWidget->type = 'widget';
                    $viewerBoxWidget->id = 'vserver_channels_link';
                    break;
                case 'unlink_channel':
                    $viewerBoxWidget->type = 'widget';
                    $viewerBoxWidget->id = 'vserver_channels_unlink';
                    break;
                case 'move_channel':
                    $viewerBoxWidget->type = 'widget';
                    $viewerBoxWidget->id = 'vserver_channels_move';
                    break;
                case 'add_group':
                    $viewerBoxWidget->type = 'popup';
                    $viewerBoxWidget->id = 'channelGroupAdd';
                    break;
                case 'delete_channel':
                    $viewerBoxWidget->type = 'popup';
                    $viewerBoxWidget->id = 'channelDelete';
                    break;
                case 'move_users_in':
                    $viewerBoxWidget->type = 'popup';
                    $viewerBoxWidget->id = 'channelMoveIn';
                    break;
                case 'move_users_out':
                    $viewerBoxWidget->type = 'popup';
                    $viewerBoxWidget->id = 'channelMoveOut';
                    break;
                case 'messageToChannel':
                    $viewerBoxWidget->type = 'popup';
                    $viewerBoxWidget->id = 'channelMessage';
                    break;
            }
        } else {
            switch($PMA->router->getRoute('subtab')) {
                case 'acl':
                    $viewerBoxWidget->type = 'widget';
                    $viewerBoxWidget->id = 'vserver_channels_ACLEditor';
                    break;
                case 'groups':
                    $viewerBoxWidget->type = 'widget';
                    $viewerBoxWidget->id = 'vserver_channels_groupEditor';
                    break;
                case 'properties':
                    $viewerBoxWidget->type = 'widget';
                    $viewerBoxWidget->id = 'vserver_channels_channelEditor';
                    break;
            }
        }
    /**
    * User menu
    */
    } elseif (isset($_SESSION['page_vserver']['uSess'])) {
        /**
        * Setup user ip.
        */
        $module->sessionObj->ip = PMA_ipHelper::decimalTostring($module->sessionObj->address);
        $module->sessionObj->ip = $module->sessionObj->ip['ip'];
        /**
        * Setup user certificate.
        * Memo : getCertificateList comes with murmur 1.2.1.
        */
        if (method_exists('Murmur_Server', 'getCertificateList')) {
            $certificatesList = $prx->getCertificateList($module->sessionObj->session);
            if (! empty($certificatesList)) {
                $module->sessionObj->certBlob = decimalArrayToChars($certificatesList[0]);
                $module->sessionObj->certSha1 = sha1($module->sessionObj->certBlob);
            } else {
                $module->sessionObj->certSha1 = 'No certificate found';
            }
        }

        $PMA->router->subtab->addRoute('comment');
        $PMA->router->subtab->addRoute('certificate');
        $PMA->router->subtab->addRoute('infos');
        $PMA->router->subtab->setDefaultRoute('infos');
        $PMA->router->checkNavigation('subtab');

        /**
        * Menu : kick user
        */
        $menu = new channelsMenuStruc();
        $menu->href = '?action=kick_user';
        $menu->text = $TEXT['kick'];
        $menu->img = 'images/xchat/kick_16.png';
        $menu->js = 'onClick="return popup(\'userKick\');"';
        $PMA->widgets->newHiddenPopup('userKick');
        $module->actionMenu[] = $menu;
        /**
        * Menu : ban user
        */
        if (isset($module->sessionObj->certBlob)) {
            $menu = new channelsMenuStruc();
            $menu->href = '?action=ban_user';
            $menu->text = $TEXT['ban'];
            $menu->img = 'images/xchat/ban_16.png';
            $menu->js = 'onClick="return popup(\'userBan\');"';
            $PMA->widgets->newHiddenPopup('userBan');
            $module->actionMenu[] = $menu;
        }
        /**
        * Menu : modify session login.
        * Since murmur 1.2.4, it's possible to modify user session login
        */
        if ($PMA->meta->getVersion('int') >= 124) {
            $menu = new channelsMenuStruc();
            $menu->href = '?action=modifyUserSessionLogin';
            $menu->text = $TEXT['modify_user_session_name'];
            $menu->img = 'images/tango/group_16.png';
            $menu->js = 'onClick="return popup(\'userSessionLogin\');"';
            $PMA->widgets->newHiddenPopup('userSessionLogin');
            $module->actionMenu[] = $menu;
        }
        /**
        * Menu : Message to user
        */
        $menu = new channelsMenuStruc();
        $menu->href = '?action=messageToUser';
        $menu->text = $TEXT['send_msg'];
        $menu->img = IMG_MSG_16;
        $menu->js = 'onClick="return popup(\'userMessage\');"';
        $PMA->widgets->newHiddenPopup('userMessage');
        $module->actionMenu[] = $menu;
        /**
        * Menu : move user
        */
        $menu = new channelsMenuStruc();
        $menu->text = $TEXT['move'];
        if ($totalChannels > 1) {
            $menu->href = '?action=move_user&amp;viewerAction';
            $menu->img = IMG_UP_16;
        }
        $module->actionMenu[] = $menu;
        /**
        * Menu : mute user
        */
        $menu = new channelsMenuStruc();
        $menu->href = '?cmd=murmur_users_sessions&amp;muteUser';
        if ($module->sessionObj->mute) {
            $menu->text = $TEXT['unmute'];
            $menu->img = 'images/mumble/user_unmute.png';
        } else {
            $menu->text = $TEXT['mute'];
            $menu->img = 'images/mumble/user_muted.png';
        }
        $module->actionMenu[] = $menu;
        /**
        * Menu : deafen user
        */
        $menu = new channelsMenuStruc();
        $menu->href = '?cmd=murmur_users_sessions&amp;deafUser';
        if ($module->sessionObj->deaf) {
            $menu->text = $TEXT['undeafen'];
            $menu->img = 'images/mumble/user_undeafen.png';
        } else {
            $menu->text = $TEXT['deafen'];
            $menu->img = 'images/mumble/user_deafened.png';
        }
        $module->actionMenu[] = $menu;
        /**
        * Menu : priority speaker
        * PrioritySpeaker come with murmur 1.2.3
        */
        if (isset($module->sessionObj->prioritySpeaker)) {
            $menu = new channelsMenuStruc();
            $menu->href = '?cmd=murmur_users_sessions&amp;togglePrioritySpeaker';
            if ($module->sessionObj->prioritySpeaker) {
                $menu->text = $TEXT['disable_priority'];
                $menu->img = 'images/tango/microphone-muted_16.png';
            } else {
                $menu->text = $TEXT['enable_priority'];
                $menu->img = 'images/tango/microphone_16.png';
            }
            $module->actionMenu[] = $menu;
        }
        /**
        * Menu : go to user registration
        */
        if ($module->sessionObj->userid >= 0) {
            $menu = new channelsMenuStruc();
            $menu->href = '?tab=registrations&amp;mumbleRegistration='.$module->sessionObj->userid;
            $menu->text = $TEXT['edit_account'];
            $menu->img = IMG_EDIT_16;
            $module->actionMenu[] = $menu;
        /**
        * Menu : add user registration
        */
        } elseif (isset($module->sessionObj->certBlob)) {
            $menu = new channelsMenuStruc();
            $menu->href = '?cmd=murmur_users_sessions&amp;register_session';
            $menu->text = $TEXT['register_user'];
            $menu->img = IMG_ADD_16;
            $module->actionMenu[] = $menu;
        }

        /**
        * Setup widgets
        */
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'kick_user':
                    $viewerBoxWidget->type = 'popup';
                    $viewerBoxWidget->id = 'userKick';
                    break;
                case 'ban_user':
                    $viewerBoxWidget->type = 'popup';
                    $viewerBoxWidget->id = 'userBan';
                    break;
                case 'modifyUserSessionLogin':
                    $viewerBoxWidget->type = 'popup';
                    $viewerBoxWidget->id = 'userSessionLogin';
                    break;
                case 'messageToUser':
                    $viewerBoxWidget->type = 'popup';
                    $viewerBoxWidget->id = 'userMessage';
                    break;
                case 'move_user':
                    $viewerBoxWidget->type = 'widget';
                    $viewerBoxWidget->id = 'vserver_channels_userMove';
                    break;
            }
        } else {
            switch($PMA->router->getRoute('subtab')) {
                case 'infos':
                    $viewerBoxWidget->type = 'widget';
                    $viewerBoxWidget->id = 'vserver_channels_userInformations';
                    break;
                case 'certificate':
                    $viewerBoxWidget->type = 'widget';
                    $viewerBoxWidget->id = 'vserver_channels_userCertificate';
                    $PMA->widgets->newWidget('widget_certificate');
                    $PMA->widgets->setLowPriority('widget_certificate');
                    break;
                case 'comment':
                    $viewerBoxWidget->type = 'widget';
                    $viewerBoxWidget->id = 'vserver_channels_userComment';
                    break;
            }
        }
    }
}

if ($PMA->user->is(PMA_USER_MUMBLE)) {
    $viewerBoxWidget->type = 'widget';
    $viewerBoxWidget->id = 'vserver_channels_mumbleUser';
}

if ($viewerBoxWidget->type === 'widget') {
    $PMA->widgets->newWidget($viewerBoxWidget->id);
    $module->viewerBoxWidgetPath = $PMA->widgets->getView($viewerBoxWidget->id);
} else {
    $PMA->widgets->newPopup($viewerBoxWidget->id);
    $module->viewerBoxWidgetPath = $PMA->widgets->getView($viewerBoxWidget->id);
}

/**
* Setup the channel viewer for admins and mumble users.
*/
if ($PMA->user->is(PMA_USER_MUMBLE)) {
    /**
    * Mumble user viewer.
    */
    $viewer = new PMA_MurmurViewer();
    $viewer->enableOption('channelSelection');
    $viewer->enableOption('usersSelection');

} elseif ($PMA->user->isMinimum(PMA_USER_SUPERUSER_RU)) {
    /**
    * Admins viewer.
    */
    $viewer = new PMA_MurmurViewerAdmin();
    $viewer->enableOption('channelSelection');
    $viewer->enableOption('usersSelection');
    /**
    * Actions with the viewer.
    */
    if (isset($_GET['viewerAction'])) {

        $viewer->css = 'action';
        $viewer->disableOption('usersSelection');

        switch($_GET['action']) {
            case 'move_user':
                $user = $module->onlineUsersList[$_SESSION['page_vserver']['uSess']['id']];
                $viewer->setParam('channelHREF', '?cmd=murmur_users_sessions&amp;move_user_to');
                // Disable selected user channel.
                $viewer->disableChannelID($user->channel);
                break;
           case 'move_users_out':
                // Disable to select current channel.
                $viewer->disableChannelID($_SESSION['page_vserver']['cid']);
                $viewer->setParam('channelHREF', '?action=move_users_out&amp;viewerAction&amp;to');
                if (isset($_GET['to']) && ctype_digit($_GET['to'])) {
                    // Disable to select all channels.
                    $viewer->disableOption('channelSelection');
                    $viewer->setParam('selectedMoveTo', (int)$_GET['to']);
                }
                break;
            case 'move_users_in':
                $viewer->disableOption('channelSelection');
                break;
            case 'move_channel':
                $chan = $module->channelsList[$_SESSION['page_vserver']['cid']];
                $viewer->setParam('channelHREF', '?cmd=murmur_channel&amp;move_channel_to');
                $allChildrensID = $viewer->getAllChildrensID($getTree, $_SESSION['page_vserver']['cid']);
                // Disable to select current, parent and all childrens channels
                $viewer->disableChannelID($chan->id);
                $viewer->disableChannelID($chan->parent);
                foreach ($allChildrensID as $id) {
                    $viewer->disableChannelID($id);
                }
                break;
            case 'link_channel':
                $chan = $module->channelsList[$_SESSION['page_vserver']['cid']];
                $viewer->setParam('channelHREF', '?cmd=murmur_channel&amp;link_channel');
                // Disable to select current, and all direct link channels
                foreach ($chan->links as $id) {
                    $viewer->disableChannelID($id);
                }
                $viewer->disableChannelID($chan->id);
                break;
            case 'unlink_channel':
                $chan = $module->channelsList[$_SESSION['page_vserver']['cid']];
                $viewer->setParam('channelHREF', '?cmd=murmur_channel&amp;unlink_channel');
                // Disable all channels which are not directs links
                foreach ($module->channelsList as $id => $obj) {
                    if (! in_array($id, $chan->links, true)) {
                        $viewer->disableChannelID($id);
                    }
                }
                break;
        }
    }
}
/**
* Common setup for admins and mumble users.
*/
$viewer->setParam('serverName', $module->vserverName);
$viewer->setParam('defaultChanID', $module->defaultChannelID);
$viewer->enableOption('showStatusIcons');
$viewer->enableOptionShowPasswords($prx);
$viewer->enableOption('showChannelsLinks');
/**
* Setup the selected channel or user session.
*/
if (isset($_SESSION['page_vserver']['cid'])) {
    $viewer->setParam('selectedChanID', $_SESSION['page_vserver']['cid']);
    $viewer->setupSelectedChannelLinks($module->channelsList);
} elseif (isset($_SESSION['page_vserver']['uSess'])) {
    $viewer->setParam('selectedUserSessID', $_SESSION['page_vserver']['uSess']['id']);
}

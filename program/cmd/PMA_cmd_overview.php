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

class PMA_cmd_overview extends PMA_cmd
{
    private $profile_id;
    private $logsInfos;

    public function process()
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_SUPERUSER_RU)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }

        $this->getMurmurMeta();

        $profile = $this->PMA->userProfile;

        // Common log infos
        $this->logsInfos = 'profile: '.$profile['id'].'# server id: ';

        $this->profile_id = $profile['id'];
        $this->profile_host = $profile['host'];
        $this->profile_port = $profile['port'];

        if (isset($this->PARAMS['add_vserver'])) {
            $this->addNewServer();
        } elseif (isset($this->PARAMS['toggle_server_status'])) {
            $this->toggleServerStatus($this->PARAMS['toggle_server_status']);
        } elseif (isset($this->PARAMS['confirm_stop_sid'])) {
            $this->confirmStopServerID($this->PARAMS['confirm_stop_sid']);
        } elseif (isset($this->PARAMS['toggle_web_access'])) {
            $this->toggleWebAccess($this->PARAMS['toggle_web_access']);
        } elseif (isset($this->PARAMS['delete_vserver_id'])) {
            $this->deleteServerID($this->PARAMS['delete_vserver_id']);
        } elseif (isset($this->PARAMS['messageToServers'])) {
            $this->sendMessageToAllServers($this->PARAMS['messageToServers']);
        } elseif (isset($this->PARAMS['refreshServerList'])) {
            $this->refreshServersList();
        } elseif (isset($this->PARAMS['serverReset'])) {
            $this->resetServerParameters($this->PARAMS['serverReset']);
        } elseif (isset($this->PARAMS['mass_settings'])) {
            $this->setMassSettings();
        }
    }

    /**
    * Common refreshServersCache helper
    */
    private function refreshServersCache()
    {
        $cache = PMA_serversCacheHelper::get('normal', true);
        $cache = PMA_serversCacheHelper::get('htEnc', true);
    }

    /**
    * Common sanity on toggle server status helper
    */
    private function sanityOnToggle($sid)
    {
        if (! ctype_digit($sid) && ! is_int($sid)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }
        // Check if SuperUser have authorization to start / stop his vserver.
        if (! $this->PMA->config->get('SU_start_vserver') && $this->PMA->user->is(PMA_USERS_SUPERUSERS)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }
        // Set $sid
        if ($this->PMA->user->isMinimum(PMA_USER_ADMIN)) {
            $sid = (int)$sid;
        } else {
            $sid = $this->PMA->user->mumbleSID;
        }
        // Check current admin rights for the server id.
        if ($this->PMA->user->is(PMA_USER_ADMIN)) {
            if (! $this->PMA->user->checkServerAccess($sid)) {
                $this->messageError('illegal_operation');
                $this->throwException();
            }
        }
        return $sid;
    }

    private function addNewServer()
    {
        if (! $this->PMA->user->isMinimumAdminFullAccess()) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }
        $prx = $this->PMA->meta->newServer();
        $this->logUserAction('Virtual server created ('.$this->logsInfos.$prx->id().' )');
        $this->message('vserver_created_success');
        $prx->setConf('boot', 'false');
        if (isset($this->PARAMS['new_su_pw'])) {
            $prx->setSuperuserPassword($pw = genRandomChars(16));
            $this->messageError(array('new_su_pw', $pw));
        }
        $this->refreshServersCache();
    }

    /**
    * Start / stop the vserver
    */
    private function toggleServerStatus($sid)
    {
        $sid = $this->sanityOnToggle($sid);
        $prx = $this->getServerPrx($sid);

        if ($prx->isRunning()) {
            $getUsers = $prx->getUsers();
            // Check if the virtual server is empty or display a warning msg.
            if (empty($getUsers)) {
                $prx->stop();
                $this->logUserAction('Server stopped ('.$this->logsInfos.$sid.' )');
                $prx->setConf('boot', 'false');
            } else {
                // Server is not empty, redirect to the confirmation message.
                $this->setRedirection('?confirmStopSrv='.$sid);
                $this->throwException();
            }
        } else {
            $prx->start();
            $this->logUserAction('Server started ('.$this->logsInfos.$sid.' )');
            $prx->setConf('boot', '');
        }
    }

    private function confirmStopServerID($sid)
    {
        if (! isset($this->PARAMS['confirmed'])) {
            $this->messageError('confirmed_is_required');
            $this->throwException();
        }

        $sid = $this->sanityOnToggle($sid);
        $prx = $this->getServerPrx($sid);

        if (! $prx->isRunning()) {
            $this->messageError('Murmur_ServerBootedException');
            $this->throwException();
        }
        $message = $this->PARAMS['msg'];
        if ($message !== '') {
            if (! $this->PMA->user->isMinimumAdminFullAccess()) {
                $message = $prx->removeHtmlTags($message, $stripped);
                if ($stripped) {
                    $this->messageError('vserver_dont_allow_HTML');
                }
            }
            $message = $prx->URLtoHTML($message);
            $prx->sendMessageChannel(0, true, $message);
        }

        if (isset($this->PARAMS['kickAllUsers'])) {
            $prx->kickAllUsers();
        }

        $prx->stop();
        $this->logUserAction('Server stopped ('.$this->logsInfos.$sid.' )');
        $prx->setConf('boot', 'false');
    }

    private function toggleWebAccess($sid)
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_ADMIN)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }

        if (! ctype_digit($sid)) {
            $this->messageError('invalid_numerical');
            $this->throwException();
        }

        $sid = (int)$sid;

        // Check current admin rights for the virtual server
        if ($this->PMA->user->is(PMA_USER_ADMIN)) {
            if (! $this->PMA->user->checkServerAccess($sid)) {
                $this->messageError('illegal_operation');
                $this->throwException();
            }
        }

        $prx = $this->getServerPrx($sid);

        if ($prx->getParameter('PMA_permitConnection') !== 'true') {
            $prx->setConf('PMA_permitConnection', 'true');
            $this->logUserAction('Web access enabled ('.$this->logsInfos.$sid.' )');
        } else {
            // Delete the parameter
            $prx->setConf('PMA_permitConnection', '');
            $this->logUserAction('Web access disabled ('.$this->logsInfos.$sid.' )');
        }
        $this->refreshServersCache();
    }

    private function deleteServerID($sid)
    {
        if (! $this->PMA->user->isMinimumAdminFullAccess()) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }

        if (! isset($this->PARAMS['confirmed'])) {
            $this->throwException();
        }

        if (! ctype_digit($sid)) {
            $this->messageError('invalid_numerical');
            $this->throwException();
        }

        $sid = (int)$sid;

        $prx = $this->getServerPrx($sid);

        // You can't delete a running virtual server, so stop it.
        if ($prx->isRunning()) {
            $prx->kickAllUsers();
            $prx->stop();
        }

        $prx->delete();

        $this->logUserAction('Virtual server deleted ('.$this->logsInfos.$sid.' )');
        $this->message('vserver_deleted_success');

        $this->PMA->admins = new PMA_datas_admins();
        $this->PMA->admins->deleteServerIdsAccess($this->profile_id, $sid);

        $this->refreshServersCache();

        // Unset $_SESSION['page_vserver'] if we deleted the server in session.
        if (isset($_SESSION['page_vserver']['id']) && $_SESSION['page_vserver']['id'] === $sid) {
            unset($_SESSION['page_vserver']);
        }
    }

    private function sendMessageToAllServers($message)
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_ADMIN)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }
        if ($message === '') {
            $this->messageError('empty_message_not_allowed');
            $this->throwException();
        }
        $booted = $this->PMA->meta->getBootedServers();

        foreach ($booted as $prx) {
            if ($this->PMA->user->is(PMA_USER_ADMIN)) {
                if (! $this->PMA->user->checkServerAccess($prx->getSid())) {
                    continue;
                }
            }
            if ($this->PMA->user->is(PMA_USERS_ADMINS)) {
                $message = $prx->removeHtmlTags($message, $stripped);
            }
            $message = $prx->URLtoHTML($message);
            $prx->sendMessageChannel(0, true, $message);
        }
    }

    private function refreshServersList()
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_ROOTADMIN)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }
        $this->setRedirection('referer');
        $this->refreshServersCache();
    }

    private function resetServerParameters($sid)
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_ADMIN)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }

        // Action cancelled
        if (! isset($this->PARAMS['confirmed'])) {
            $this->throwException();
        }

        if (! ctype_digit($sid)) {
            $this->messageError('invalid_numerical');
            $this->throwException();
        }

        $sid = (int)$sid;

        // Check current admin rights for the virtual server
        if ($this->PMA->user->is(PMA_USER_ADMIN)) {
            if (! $this->PMA->user->checkServerAccess($sid)) {
                $this->messageError('illegal_operation');
                $this->throwException();
            }
        }

        $prx = $this->getServerPrx($sid);

        if (! $prx->isRunning()) {
            $prx->start();
        }

        $prx->kickAllUsers();

        // DELETE ALL CHANNELS
        $channels = $prx->getChannels();
        foreach ($channels as $chan) {
            if ($chan->id !== 0 && $chan->parent === 0) {
                $prx->removeChannel($chan->id);
            }
        }

        // RESET ROOT CHANNEL PROPERTIES
        $root = $prx->getChannelState( 0);
        $root->name = 'Root';
        $root->links = array();
        $root->description = '';
        $root->position = 0;
        // Workaround for the 1.2.0 murmur bug with Root channel state
        if ($this->PMA->meta->getVersion('int') === 120) {
            $root->parent = 0;
        }
        $prx->setChannelState($root);

        // RESET ROOT CHANNEL ACL
        $aclList = array();

        $aclList[1] = new Murmur_ACL();
        $aclList[1]->group = 'admin';
        $aclList[1]->userid = -1;
        $aclList[1]->applyHere = true;
        $aclList[1]->applySubs = true;
        $aclList[1]->inherited = false;
        $aclList[1]->allow = Murmur_PermissionWrite;
        $aclList[1]->deny = 0;

        $aclList[2] = new Murmur_ACL();
        $aclList[2]->group = 'auth';
        $aclList[2]->userid = -1;
        $aclList[2]->applyHere = true;
        $aclList[2]->applySubs = true;
        $aclList[2]->inherited = false;
        $aclList[2]->allow = Murmur_PermissionMakeTempChannel;
        $aclList[2]->deny = 0;

        $aclList[3] = new Murmur_ACL();
        $aclList[3]->group = 'all';
        $aclList[3]->userid = -1;
        $aclList[3]->applyHere = true;
        $aclList[3]->applySubs = false;
        $aclList[3]->inherited = false;
        $aclList[3]->allow = Murmur_PermissionRegisterSelf;
        $aclList[3]->deny = 0;

        // RESET ROOT CHANNEL GROUPES
        $groupList = array();

        $groupList[1] = new Murmur_Group();
        $groupList[1]->name = 'admin';
        $groupList[1]->inherited = false;
        $groupList[1]->inherit = true;
        $groupList[1]->inheritable = true;
        $groupList[1]->add = array();
        $groupList[1]->members = array();
        $groupList[1]->remove = array();

        $prx->setACL(0, $aclList, $groupList, false);

        // RESET VIRTUAL SERVER PARAMETERS
        $getAllConf = $prx->getAllConf();
        foreach ($getAllConf as $key => $value) {
            $prx->setConf($key, '');
        }

        // DELETE ALL REGISTERED ACCOUNTS
        $getRegisteredUsers = $prx->getRegisteredUsers('');
        foreach ($getRegisteredUsers as $uid => $name) {
            if ($uid !== 0) {
                $prx->unregisterUser($uid);
            }
        }

        // Reset SuperUser registration
        $reset_su[0] = 'SuperUser';
        $reset_su[1] = '';
        $reset_su[2] = '';
        $prx->updateRegistration( 0, $reset_su);

        // New SuperUser password
        if (isset($this->PARAMS['new_su_pw'])) {
            // New superadmin password
            $prx->setSuperuserPassword($pw = genRandomChars(16));
            $this->messageError(array('new_su_pw', $pw));
        }

        // DELETE ALL BANS
        $prx->setBans(array());

        // END
        $prx->stop();
        $prx->setConf('boot', 'false');

        if (isset($_SESSION['page_vserver']['id']) && $_SESSION['page_vserver']['id'] === $sid) {
            unset($_SESSION['page_vserver']);
        }

        $this->logUserAction('Virtual server reseted ('.$this->logsInfos.$sid.' )');
        $this->message('vserver_reset_success');
    }

    private function setMassSettings()
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_ROOTADMIN)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }
        $this->setRedirection('referer');

        if ($this->PARAMS['confirm'] !== $this->PARAMS['confirm_word']) {
            $this->messageError('invalid_confirm_word');
            $this->throwException();
        }

        $settings = PMA_MurmurSettingsHelper::get($this->PMA->meta->getVersion('int'));

        // Check for a valid parameter
        if (! isset($settings[$this->PARAMS['key']])) {
            $this->messageError('invalid_setting_parameter');
            $this->throwException();
        }

        $vservers = $this->PMA->meta->getAllServers();

        foreach ($vservers as $prx) {
            $prx->setConf($this->PARAMS['key'], $this->PARAMS['value']);
        }
        $this->message('parameters_updated_success');
    }
}

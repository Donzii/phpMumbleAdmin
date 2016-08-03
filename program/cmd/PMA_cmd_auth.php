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

class PMA_cmd_auth extends PMA_cmd
{
    private $login;
    private $password;
    private $sid;
    private $ip;

    public function __construct()
    {
        parent::__construct();
        $this->ip = $_SERVER['REMOTE_ADDR'];
    }

    public function process()
    {
        /**
        * Sanity.
        */
        if (! $this->PMA->user->is(PMA_USER_UNAUTH)) {
            $this->messageError('already_authenticated');
            $this->throwException();
        }
        /**
        * New autoban attemps.
        */
        $this->autoBanAttempts();
        /**
        * Empty login is always an error.
        */
        if (! isset($this->PARAMS['login']) OR $this->PARAMS['login'] === '') {
            $this->log('auth.error', 'empty login');
            $this->throwCommonAuthError();
        }
        /**
        * Empty password is always an error too.
        */
        if (! isset($this->PARAMS['password']) OR $this->PARAMS['password'] === '') {
            $this->log('auth.error', 'empty password');
            $this->throwCommonAuthError();
        }
        /**
        * Server id sanity.
        */
        if (! isset($this->PARAMS['server_id'])) {
            $this->PARAMS['server_id'] = '';
        }
        if ($this->PARAMS['server_id'] !== '' && ! ctype_digit($this->PARAMS['server_id'])) {
            $this->log('auth.error', 'invalid server id "'.$this->PARAMS['server_id'].'"');
            $this->throwCommonAuthError();
        }
        /**
        * Setup auth variables.
        */
        $this->login = $this->PARAMS['login'];
        $this->password = $this->PARAMS['password'];
        $this->sid = $this->PARAMS['server_id'];
        /**
        * Process authentication
        */
        if ($this->sid === '') {
            if ($this->login === $this->PMA->config->get('SA_login')) {
                $this->authSuperAdmin();
            } else {
                $this->authAdmins();
            }
        } elseif (ctype_digit($this->sid)) {
            $this->authMumbleUsers();
        } else {
            $this->throwCommonAuthError();
        }
    }

    /**
    * Auth error helper.
    */
    private function throwCommonAuthError()
    {
        $this->messageError('auth_error');
        $this->throwException();
    }

    /**
    * Auth SuperAdmin.
    */
    private function authSuperAdmin()
    {
        if (PMA_passwordHelper::check($this->password, $this->PMA->config->get('SA_pw'))) {
            $this->PMA->user->setClass(PMA_USER_SUPERADMIN);
            $this->PMA->user->setLogin($this->PMA->config->get('SA_login'));
            $this->PMA->user->setAuthIP($this->ip);
            $this->PMA->cookie->requestUpdate();
            $this->log('auth.info', 'Successful login for SuperAdmin');
        } else {
            $this->log('auth.error', 'Password error for SuperAdmin');
            $this->throwCommonAuthError();
        }
    }

    /**
    * Auth PMA admins.
    */
    private function authAdmins()
    {
        $this->PMA->admins = new PMA_datas_admins();
        $adm = $this->PMA->admins->auth($this->login, $this->password);
        if (is_array($adm)) {
            $this->PMA->user->setClass($adm['class']);
            $this->PMA->user->setLogin($adm['login']);
            $this->PMA->user->setAdminID($adm['id']);
            $this->PMA->user->setAuthIP($this->ip);
            // Update last connection timestamp.
            $adm['last_conn'] = time();
            $this->PMA->admins->modify($adm);
            $this->PMA->cookie->requestUpdate();
            $this->log('auth.info', 'Successful login for '.pmaGetClassName($adm['class']).' "'.$this->login.'"');
        } elseif ($adm === 1) {
            $this->log('auth.error',  'Password error for admin "'.$this->login.'"');
            $this->throwCommonAuthError();
        } else {
            $this->log('auth.error', 'Login error: no admin "'.$this->login.'" found.');
            $this->throwCommonAuthError();
        }
    }

    /**
    * Auth Mumble users.
    */
    private function authMumbleUsers()
    {
        $allowOfflineAuth = $this->PMA->config->get('allowOfflineAuth');
        $allowSuperUserAuth = $this->PMA->config->get('SU_auth');
        $allowMumbleUsersAuth = $this->PMA->config->get('RU_auth');
        $allowSuperUserRuClass = $this->PMA->config->get('SU_ru_active');
        $isSuperUserRu = false;

        if (! $allowSuperUserAuth && ! $allowMumbleUsersAuth) {
            $this->throwCommonAuthError();
        }
        $this->getMurmurMeta();
        $sid = (int)$this->sid;
        $profile = $this->PMA->userProfile;

        // Common pmaLogs infos
        $logsInfos = ' ( profile: '.$profile['id'].'# server id: '.$sid.' -  login: '.$this->login.' )';

        if (is_null($prx = $this->PMA->meta->getServer($sid))) {
            $this->log('auth.error', 'Server id do not exists'.$logsInfos);
            $this->throwCommonAuthError();
        }
        // Check web access
        if ($prx->getParameter('PMA_permitConnection') !== 'true') {
            $this->log('auth.warn', 'Web access denied'.$logsInfos);
            $this->messageError('web_access_disabled');
            $this->throwException();
        }
        $isRunning = $prx->isRunning();
        // Start the server if stopped.
        if (! $isRunning) {
            if ($allowOfflineAuth) {
                $prx->start();
            } else {
                $this->messageError('vserver_offline');
                $this->throwException();
            }
        }
        // verifyPassword return user ID on successfull authentification, else
        // -1 for failed authentication and -2 for unknown usernames.
        $MumbleID = $prx->verifyPassword($this->login, $this->password);
        // Get registration before stop the vserver
        if ($MumbleID >= 0) {
            $user_registration = $prx->getRegistration($MumbleID);
        }
        // Check if registered user have SuperUserRu rights
        if ($allowSuperUserAuth && $allowSuperUserRuClass) {
            $prx->getACL(0, $aclList, $groupList, $inherit);
            $isSuperUserRu = PMA_MurmurAclHelper::isSuperUserRu($MumbleID, $aclList);
        }
        // Stop the server if it was stopped.
        if (! $isRunning) {
            $prx->stop();
        }
        // PASSWORD ERROR
        if ($MumbleID === -1) {
            $this->log('auth.error', 'Password error:'.$logsInfos);
            $this->throwCommonAuthError();
        }
        // INVALID LOGIN
        if ($MumbleID === -2) {
            $this->log('auth.error', 'Login error:'.$logsInfos);
            $this->throwCommonAuthError();
        }
        // Check if SuperUser connection is authorized.
        if ($MumbleID === 0 && ! $allowSuperUserAuth) {
            $this->log('auth.warn', 'SuperUsers authentication not allowed'.$logsInfos);
            $this->messageError('auth_su_disabled');
            $this->throwException();
        }
        // Check if registered user connection is authorized, but let connect SuperUser_ru anyway.
        if ($MumbleID > 0 && ! $allowMumbleUsersAuth && ! $isSuperUserRu) {
            $this->log('auth.warn', 'Registered users authentication not allowed'.$logsInfos);
            $this->messageError('auth_ru_disabled');
            $this->throwException();
        }
        // Succesfull login, setup the session
        if ($MumbleID === 0) {
            $this->PMA->user->setClass(PMA_USER_SUPERUSER);
        } elseif ($isSuperUserRu) {
            $this->PMA->user->setClass(PMA_USER_SUPERUSER_RU);
        } else {
            $this->PMA->user->setClass(PMA_USER_MUMBLE);
        }
        $this->PMA->user->setLogin($user_registration[0]);
        $this->PMA->user->setAuthProfileID($profile['id']);
        $this->PMA->user->setAuthProfileHost($profile['host']);
        $this->PMA->user->setAuthProfilePort($profile['port']);
        $this->PMA->user->setMumbleSID($sid);
        $this->PMA->user->setMumbleUID($MumbleID);
        $this->PMA->user->setAuthIP($this->ip);

        $this->log('auth.info', 'Successful login for '.pmaGetClassName($this->PMA->user->class).$logsInfos);
        $this->PMA->cookie->requestUpdate();
    }
}

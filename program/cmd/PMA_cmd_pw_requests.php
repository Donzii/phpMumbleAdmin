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

class PMA_cmd_pw_requests extends PMA_cmd
{
    private $prx;

    private $profile;
    private $sid;
    private $login;
    private $uid;

    private $infos;

    private $serverIsTemporaryStarted = false;
    private $showExplicitErrors;

    public function process()
    {
        $this->autoBanAttempts();

        /**
        * Sanity :
        * The options must be enable
        * User must accept cookies
        * SuperUser OR Registered user web access must be actived.
        */
        if (
            ! $this->PMA->config->get('pw_gen_active')
            OR ! $this->PMA->cookie->userAcceptCookies()
            OR (! $this->PMA->config->get('SU_auth') && ! $this->PMA->config->get('RU_auth'))
        ) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }

        /**
        * Init
        */
        $this->getMurmurMeta();

        $this->showExplicitErrors = $this->PMA->config->get('pw_gen_explicit_msg');

        $this->profile = $this->PMA->userProfile;
        $this->sid = (int)$this->PARAMS['server_id'];

        $this->login = $this->PARAMS['login'];

        // Common logs infos
        $this->logsInfos = ' ( profile: '.$this->profile['id'].'# server id: '.$this->sid.' - login: '.$this->login.' )';

        if (is_null($this->prx = $this->PMA->meta->getServer($this->sid))) {
            $this->log('pwGen.error', 'Invalid server id'.$this->logsInfos);
            $this->explicitError('gen_pw_invalid_server_id');
        }

        // Check if web access is enable
        if ($this->prx->getParameter('PMA_permitConnection') !== 'true') {
            $this->log('pwGen.warn', 'Web access is disabled'.$this->logsInfos);
            $this->messageError('web_access_disabled');
            $this->throwException();
        }

        $this->isRunning = $this->prx->isRunning();

        // Start the virtual server if it's stopped.
        // WARN: Here, do not return to cmd/cmd.php before we stop the vserver at the end of this script.
        if (! $this->isRunning) {
            $this->prx->start();
            $this->serverIsTemporaryStarted = true;
        }

        $this->uid = $this->get_user_id();

        $this->PMA->pw_requests = new PMA_datas_pwRequests();
        $this->PMA->pw_requests->deleteIdenticalRequests($this->profile['id'], $this->profile['host'], $this->profile['port'], $this->sid, $this->uid);

        $this->email = $this->get_user_email();

        $this->send_email();
    }

    protected function throwException($key = '')
    {
        // Stop the virtual server if it was temporary started.
        if ($this->serverIsTemporaryStarted) {
            $this->prx->stop();
        }
        parent::throwException($key);
    }

    private function explicitError($key)
    {
        if ($this->showExplicitErrors) {
            $this->messageError($key);
            $this->throwException();
        } else {
            // Show unexplicit common error
            $this->messageError('gen_pw_error');
            $this->throwException();
        }
    }

    private function get_user_id()
    {
        // Memo: getRegisteredUsers() return all occurence of a search.
        // example: for "ipnoz", it's return ipnozer, ipnozer2 etc...
        $search = $this->prx->getRegisteredUsers($this->login);

        $user = strtolower($this->login);

        foreach ($search as $uid => $login) {
            if (strtolower($login) === $user) {
                // User login exists, keep user ID.
                $mumble_id = $uid;
                break;
            }
        }

        // user not found
        if (! isset($mumble_id)) {
            $this->log('pwGen.error', 'User not found'.$this->logsInfos);
            $this->explicitError('gen_pw_invalid_username');
        // SuperUser
        } elseif ($mumble_id === 0) {
            $this->log('pwGen.error', 'SuperUser is denied'.$this->logsInfos);
            $this->explicitError('gen_pw_su_denied');
        // User found and valid
        } elseif ($mumble_id > 0) {
            return $mumble_id;
        // Unknown error
        } else {
            $this->debugError(__method__ .' Unknown error');
            $this->log('pwGen.error', 'Unknown error'.$this->logsInfos);
            $this->messageError('gen_pw_error');
            $this->throwException();
        }
    }

    private function get_user_email()
    {
        // Fetch user email
        $registration = $this->prx->getRegistration($this->uid);

        if (isset($registration[1])) {
            $email = $registration[1];
        } else {
            $email = '';
        }

        if ($email === '') {
            $this->log('pwGen.warn', 'empty email'.$this->logsInfos);
            $this->explicitError('gen_pw_empty_email');
        }
        return $email;
    }

    private function send_email()
    {
        $pending = $this->PMA->config->get('pw_gen_pending');

        global $TEXT;
        pmaLoadLanguage('confirmPasswordRequest');

        // Construct the new pw request
        $new_request['id'] = $this->PMA->pw_requests->getUniqueID();
        $new_request['start'] = time();
        $new_request['end'] = time() + $pending * 3600;
        $new_request['ip'] = $_SERVER['REMOTE_ADDR'];
        $new_request['profile_id'] = $this->profile['id'];
        $new_request['profile_host'] = $this->profile['host'];
        $new_request['profile_port'] = $this->profile['port'];
        $new_request['sid'] = $this->sid;
        $new_request['uid'] = $this->uid;
        $new_request['login'] = $this->login;

        $URL = PMA_HTTP_HOST.PMA_HTTP_PATH.'?confirm_pw_request='.$new_request['id'];

        /**
        * Setup mail object
        */
        $mail = new PMA_mail();
        $mail->setHost($this->PMA->config->get('smtp_host'));
        $mail->setPort($this->PMA->config->get('smtp_port'));
        $mail->setDefaultSender($this->PMA->config->get('smtp_default_sender_email'));
        $mail->setXmailer(PMA_NAME);
        $mail->setFrom($this->PMA->config->get('pw_gen_sender_email'));
        $mail->addTo($this->email, $this->login);
        $mail->setSubject($TEXT['pw_mail_title']);
        $mail->setMessage(
            sprintf($TEXT['pw_mail_body'],
                $_SERVER['HTTP_HOST'],
                $this->profile['name'],
                $this->prx->getParameter('registername'),
                $URL,
                $pending
            )
        );
        /**
        * Send mail
        */
        $mail->send_mail();
        foreach ($mail->smtpDialogues as $dial) {
            $this->debug($dial->debug);
        }
        if ($mail->smtpError) {
            $this->log('smtp.error', $mail->smtpErrorMessage);
        }

        if ($mail->smtpError) {
            $this->messageError('gen_pw_error');
            $this->throwException();
        } else {
            $this->message('gen_pw_mail_sent');
            $this->log('pwGen.info', 'Mail sent'.$this->logsInfos);
            $this->PMA->pw_requests->add($new_request);
        }
    }
}

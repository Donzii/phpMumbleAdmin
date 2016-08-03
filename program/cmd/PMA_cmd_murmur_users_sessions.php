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

class PMA_cmd_murmur_users_sessions extends PMA_cmd
{
    private $prx;
    // User session id
    private $id;

    public function process()
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_SUPERUSER_RU)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }

        $this->getMurmurMeta();
        $this->prx = $this->getServerPrx($_SESSION['page_vserver']['id']);

        $this->id = $_SESSION['page_vserver']['uSess']['id'];

        if (isset($this->PARAMS['kick'])) {
            $this->kick($this->PARAMS['kick']);
        } elseif (isset($this->PARAMS['move_user_to'])) {
            $this->moveUserTo($this->PARAMS['move_user_to']);
        } elseif (isset($this->PARAMS['modify_user_session_name'])) {
            $this->modifySessionName($this->PARAMS['modify_user_session_name']);
        } elseif (isset($this->PARAMS['muteUser'])) {
            $this->toggleMute();
        } elseif (isset($this->PARAMS['deafUser'])) {
            $this->toggleDeaf();
        } elseif (isset($this->PARAMS['togglePrioritySpeaker'])) {
            $this->togglePrioritySpeaker();
        } elseif (isset($this->PARAMS['send_msg'])) {
            $this->sendMessage($this->PARAMS['send_msg']);
        } elseif (isset($this->PARAMS['register_session'])) {
            $this->registerUserSession();
        } elseif (isset($this->PARAMS['change_user_comment'])) {
            $this->changeUserComment($this->PARAMS['change_user_comment']);
        }
    }

    private function kick($message)
    {
        $this->prx->kickUser($this->id, $message);
        unset($_SESSION['page_vserver']['uSess']);
    }

    private function moveUserTo($id)
    {
        if (! ctype_digit($id)) {
            $this->messageError('invalid_numerical');
            $this->throwException();
        }

        $id = (int)$id;

        $USER = $this->prx->getState($this->id);

        if ($USER->channel !== $id) {
            $USER->channel = $id;
            $this->prx->setState($USER);
        }
    }

    private function modifySessionName($name)
    {
        // Change session name come with murmur 1.2.4
        if ($this->PMA->meta->getVersion('int') < 124) {
            $this->messageError('murmur_124_required');
            $this->throwException();
        }

        $USER = $this->prx->getState($this->id);

        if ($USER->name !== $name && $name !== '') {
            $USER->name = $name;
            $this->prx->setState($USER);
            $_SESSION['page_vserver']['uSess']['name'] = $name;
        }
    }

    private function toggleMute()
    {
        $USER = $this->prx->getState($this->id);

        if ($USER->mute) {
            $USER->mute = false;
            $USER->deaf = false;
        } else {
            $USER->mute = true;
        }
        $this->prx->setState($USER);
    }

    private function toggleDeaf()
    {
        $USER = $this->prx->getState($this->id);
        $USER->deaf = ! $USER->deaf;
        $this->prx->setState($USER);
    }

    private function togglePrioritySpeaker()
    {
        $USER = $this->prx->getState($this->id);

        // Priority speaker come with murmur 1.2.3
        if (! isset($USER->prioritySpeaker) OR $this->PMA->meta->getVersion('int') < 123) {
            $this->messageError('murmur_123_required');
            $this->throwException();
        }
        $USER->prioritySpeaker = ! $USER->prioritySpeaker;
        $this->prx->setState($USER);
    }

    private function sendMessage($message)
    {
        $this->setRedirection('referer');
        if ($message === '') {
            $this->messageError('empty_message_not_allowed');
            $this->throwException();
        }
        if (! $this->PMA->user->isMinimum(PMA_USER_ROOTADMIN)) {
            $message = $this->prx->removeHtmlTags($message, $stripped);
            if ($stripped) {
                $this->messageError('vserver_dont_allow_HTML');
            }
        }
        $message = $this->prx->URLtoHTML($message);
        $this->prx->sendMessage($this->id, $message);
    }

    private function registerUserSession()
    {
        // getCertificateList method come with murmur 1.2.1
        if (! method_exists('Murmur_Server', 'getCertificateList') OR $this->PMA->meta->getVersion('int') < 121) {
            $this->messageError('murmur_121_required');
            $this->throwException();
        }

        $USER = $this->prx->getState($this->id);

        $certificatesList = $this->prx->getCertificateList($this->id);

        if (empty($certificatesList)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }

        $sha1 = sha1(decimalArrayToChars($certificatesList[0]));

        $newuser = array(0 => $USER->name, 3 => $sha1);

        try {
            $this->prx->registerUser($newuser);
        } catch (Murmur_InvalidUserException $e) {
            $this->messageError('user_already_registered');
            $this->throwException();
        }

        $this->message('registration_created_success');
    }

    private function changeUserComment($comment)
    {
        $user = $this->prx->getState($this->id);

        if ($comment !== $user->comment) {
            // Memo: remove eol to avoid a bug when we modify user comment via js textarea.
            // $comment = replaceEOL($comment);
            $user->comment = $this->prx->removeHtmlTags($comment, $stripped);
            if ($stripped) {
                $this->messageError('vserver_dont_allow_HTML');
            }
            $this->prx->setState($user);
        }
    }
}

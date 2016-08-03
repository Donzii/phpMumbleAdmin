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

class PMA_cmd_murmur_channel extends PMA_cmd
{
    private $prx;
    private $chan_id;

    public function process()
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_SUPERUSER_RU)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }

        $this->getMurmurMeta();
        $this->prx = $this->getServerPrx($_SESSION['page_vserver']['id']);

        $this->chan_id = $_SESSION['page_vserver']['cid'];

        if (isset($this->PARAMS['add_sub_channel'])) {
            $this->addSubChannel($this->PARAMS['add_sub_channel']);
        } elseif (isset($this->PARAMS['send_msg'])) {
            $this->sendMessage($this->PARAMS['send_msg']);
        } elseif (isset($this->PARAMS['delete_channel'])) {
            $this->deleteChannel();
        } elseif (isset($this->PARAMS['channel_property'])) {
            $this->editChannelProperty();
        } elseif (isset($this->PARAMS['move_users_out_the_channel'])) {
            $this->moveUsersOutTheChannel($this->PARAMS['move_users_out_the_channel']);
        } elseif (isset($this->PARAMS['move_users_into_the_channel'])) {
            $this->moveUsersIntoTheChannel();
        } elseif (isset($this->PARAMS['move_channel_to'])) {
            $this->moveChannelTo($this->PARAMS['move_channel_to']);
        } elseif (isset($this->PARAMS['link_channel'])) {
            $this->linkChannel($this->PARAMS['link_channel']);
        } elseif (isset($this->PARAMS['unlink_channel'])) {
            $this->unlinkChannel($this->PARAMS['unlink_channel']);
        } elseif (isset($this->PARAMS['unlink_all_channel'])) {
            $this->unlinkAllChannel();
        }
    }

    private function addSubChannel($name)
    {
        $CHAN = $this->prx->getChannelState($this->chan_id);

        // Don't add a sub channel to a temporary.
        // Mumble doesn't accept that so we don't too ( and it can be problematic anyway).
        if ($CHAN->temporary) {
            $this->messageError('temporary_channel');
            $this->throwException();
        }

        if (! $this->prx->validateChannelChars($name)) {
            $this->messageError('invalid_channel_name');
            $this->throwException();
        }

        $new = $this->prx->addChannel($name, $this->chan_id);

        $_SESSION['page_vserver']['cid'] = $new;
        $this->PMA->router->subtab->setCurrentRoute('properties');

        unset(
            $_SESSION['page_vserver']['aclID'],
            $_SESSION['page_vserver']['groupID']
        );
    }

    private function sendMessage($message)
    {
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
        $sub = isset($this->PARAMS['to_all_sub']);
        $this->prx->sendMessageChannel($this->chan_id, $sub, $message);
    }

    private function deleteChannel()
    {
        if (! isset($this->PARAMS['confirmed'])) {
            $this->throwException();
        }

        $CHAN = $this->prx->getChannelState($this->chan_id);

        $this->prx->removeChannel($this->chan_id);

        $_SESSION['page_vserver']['cid'] = $CHAN->parent;

        // Remove defaultChannel if we have deleted the default channel.
        if ($this->prx->getParameter('defaultchannel') === (string) $this->chan_id) {
            $this->prx->setConf('defaultchannel' , '');
        }

        unset(
            $_SESSION['page_vserver']['aclID'],
            $_SESSION['page_vserver']['groupID']
        );
    }

    private function editChannelProperty()
    {
        $state = $this->prx->getChannelState($this->chan_id);

        // Workaround for murmur 1.2.0 bug ( removed with murmur 1.2.1, 2009-12-31 )
        if ($this->PMA->meta->getVersion('int') === 120 && $this->chan_id === 0) {
            // Without this, we can't modify root channel state
            $state->parent = 0;
        }

        // Default channel
        if (isset($this->PARAMS['defaultchannel']) && ! $state->temporary) {
            // Memo: setConf() require string for second parameter
            $this->prx->setConf('defaultchannel', (string) $this->chan_id);
        }

        // Channel name
        if (isset($this->PARAMS['name']) && $state->name !== $this->PARAMS['name']) {

            if ($this->prx->validateChannelChars($this->PARAMS['name'])) {
                $state->name = $this->PARAMS['name'];
            } else {
                $this->messageError('invalid_channel_name');
                $this->throwException();
            }
        }

        // Description
        if ($state->description !== $this->PARAMS['description']) {
            // As anybody can modify channel descrition, always remove HTML tags
            $state->description = $this->prx->removeHtmlTags($this->PARAMS['description'], $stripped);
            if ($stripped) {
                $this->messageError('vserver_dont_allow_HTML');
            }
        }

        // Position
        if (is_numeric($this->PARAMS['position']) OR $this->PARAMS['position'] === '') {
            $state->position = (int)$this->PARAMS['position'];
        } else {
            $this->messageError(array('invalid_numerical', 'channel position'));
            $this->throwException();
        }

        $this->prx->setChannelState($state);

        // Channel password
        $this->prx->getACL($this->chan_id, $aclList, $groupList, $inherit);

        PMA_MurmurAclHelper::removeInheritedACL($aclList);
        PMA_MurmurAclHelper::removeInheritedGroups($groupList);

        // Check if a password is set.
        $password_is_set = false;
        $password_acl_id = '';

        foreach ($aclList as $key => $obj) {
            if (PMA_MurmurAclHelper::isToken($obj)) {
                $password_is_set = true;
                $password_acl_id = $key;
                break;
            }
        }

        if ($this->PARAMS['pw'] !== '') {
            // Add a new password
            if (! $password_is_set) {
                // Deny all ACL
                $deny_all = new Murmur_ACL();
                $deny_all->group = 'all';
                $deny_all->userid = -1;
                $deny_all->inherited = false;
                $deny_all->applyHere = true;
                $deny_all->applySubs = true;
                $deny_all->allow = 0;
                $deny_all->deny = 908;

                // Password ACL
                $password = new Murmur_ACL();
                $password->group = '#'.$this->PARAMS['pw'];
                $password->userid = -1;
                $password->inherited = false;
                $password->applyHere = true;
                $password->applySubs = true;
                $password->allow = 908;
                $password->deny = 0;

                $aclList[] = $deny_all;
                $aclList[] = $password;

            // edit password
            } else {
                $aclList[$password_acl_id]->group = '#'.$this->PARAMS['pw'];
            }

        // Delete the password if the field is empty and a password was set.
        } elseif ($password_is_set) {

            unset($aclList[$password_acl_id]);

            // Search for the "deny all" ACL included with the password creation.
            foreach ($aclList as $key => $obj) {

                if (
                    $obj->group === 'all'
                    && ! $obj->inherited
                    && $obj->applyHere
                    && $obj->applySubs
                    && $obj->allow === 0
                    && $obj->deny === 908
                ) {
                    $deny_all_acl_id = $key;
                    unset($aclList[$key]);
                    break;
                }
            }

            if (isset($_SESSION['page_vserver']['aclID'])) {
                // Unset selected acl if it's the password or "deny all".
                if ($_SESSION['page_vserver']['aclID'] === $password_acl_id
                OR $_SESSION['page_vserver']['aclID'] === $deny_all_acl_id
                ) {
                    unset($_SESSION['page_vserver']['aclID']);
                }
            }
        }
        $this->prx->setACL($this->chan_id, $aclList, $groupList, $inherit);
    }

    private function moveUsersOutTheChannel($move_to_chan_id)
    {
        if (! ctype_digit($move_to_chan_id)) {
            $this->messageError('invalid_numerical');
            $this->throwException();
        }

        $move_to_chan_id = (int)$move_to_chan_id;

        $users = $this->prx->getUsers();

        foreach ($users as $user) {
            // move only users which are in the selected channel
            if ($user->channel === $this->chan_id) {
                // move only user selected by admin
                if (isset($this->PARAMS[$user->session])) {
                    $user->channel = $move_to_chan_id;
                    $this->prx->setState($user);
                }
            }
        }
    }

    private function moveUsersIntoTheChannel()
    {
        $users = $this->prx->getUsers();

        foreach ($users as $user) {
            // move only user out of the selected channel
            if ($user->channel !== $this->chan_id) {
                // move only user selected by admin
                if (isset($this->PARAMS[$user->session])) {
                    $user->channel = $this->chan_id;
                    $this->prx->setState($user);
                }
            }
        }
    }

    private function moveChannelTo($id)
    {
        if (! ctype_digit($id)) {
            $this->messageError('invalid_numerical');
            $this->throwException();
        }

        $id = (int)$id;

        $CHAN = $this->prx->getChannelState($this->chan_id);

        if ($CHAN->parent === $id) {
            $this->messageError('parent_channel');
            $this->throwException();
        }

        $CHAN->parent = $id;

        try {
            $this->prx->setChannelState($CHAN);
        } catch (Murmur_InvalidChannelException $Ex) {
            // Most probably move to a children channel.
            $this->messageError('children_channel');
            $this->throwException();
        }
    }

    private function linkChannel($id)
    {
        $this->setRedirection('referer');

        if (! ctype_digit($id)) {
            $this->messageError('invalid_numerical');
            $this->throwException();
        }

        $id = (int)$id;

        $channelsList = $this->prx->getChannels();
        $chanObj = $this->prx->getChannelState($this->chan_id);

        if ($chanObj->id !== $id) {
            $chanObj->links[] = $id;
            $this->prx->setChannelState($chanObj);
        }

        $a = count($channelsList);
        $b = (count($chanObj->links) +1);

        if ($a === $b) {
            $this->setRedirection(null);
        }
    }

    private function unlinkChannel($id)
    {
        $this->setRedirection('referer');

        if (! ctype_digit($id)) {
            $this->messageError('invalid_numerical');
            $this->throwException();
        }

        $id = (int)$id;

        $chanObj = $this->prx->getChannelState($this->chan_id);

        foreach ($chanObj->links as $key => $chanID) {
            if ($chanID === $id) {
                unset($chanObj->links[$key]);
                $this->prx->setChannelState($chanObj);
                break;
            }
        }

        if (empty($chanObj->links)) {
            $this->setRedirection(null);
        }
    }

    private function unlinkAllChannel()
    {
        $CHAN = $this->prx->getChannelState($this->chan_id);
        $CHAN->links = array();
        $this->prx->setChannelState($CHAN);
    }
}

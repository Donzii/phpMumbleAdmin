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

class PMA_cmd_murmur_groups extends PMA_cmd
{
    private $prx;
    private $channelID;

    private $aclList;
    private $groupList;
    private $inheritParent;

    // Selected group id
    private $gid;

    public function process()
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_SUPERUSER_RU)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }

        $this->getMurmurMeta();
        $this->prx = $this->getServerPrx($_SESSION['page_vserver']['id']);

        $this->channelID = $_SESSION['page_vserver']['cid'];

        $this->prx->getACL($this->channelID, $this->aclList, $this->groupList, $this->inheritParent);

        PMA_MurmurAclHelper::removeInheritedACL($this->aclList);

        if (isset($this->PARAMS['add_group'])) {
            $this->addGroup($this->PARAMS['add_group']);
        } elseif (isset($this->PARAMS['deleteGroup'])) {
            $this->deleteGroup();
        } elseif (isset($this->PARAMS['toggle_group_inherit'])) {
            $this->toggleGroupInheritFlag();
        } elseif (isset($this->PARAMS['toggle_group_inheritable'])) {
            $this->toggleGroupInheritableFlag();
        } elseif (isset($this->PARAMS['add_user'])) {
            $this->addUser($this->PARAMS['add_user']);
        } elseif (isset($this->PARAMS['removeMember'])) {
            $this->removeMember($this->PARAMS['removeMember']);
        } elseif (isset($this->PARAMS['excludeMember'])) {
            $this->excludeMember($this->PARAMS['excludeMember']);
        } elseif (isset($this->PARAMS['removeExcluded'])) {
            $this->removeExcludedMember($this->PARAMS['removeExcluded']);
        }
    }

    /**
    * SetACL helper
    */
    private function setACL()
    {
        $this->prx->setACL($this->channelID, $this->aclList, $this->groupList, $this->inheritParent);
    }

    /**
    * Common sanity for a valid group id
    * Memo: addGroup do not require a valid group id.
    */
    private function sanity()
    {
        if (! isset($_SESSION['page_vserver']['groupID'])) {
            $this->messageError('invalid_group_id');
            $this->throwException();
        }
        $this->gid = $_SESSION['page_vserver']['groupID'];
        if (! isset($this->groupList[$this->gid])) {
            $this->messageError('invalid_group_id');
            $this->throwException();
        }
        PMA_MurmurAclHelper::removeInheritedGroups($this->groupList, $this->gid);
    }

    private function addGroup($name)
    {
        if ($name === '') {
            $this->messageError('empty_name');
            $this->throwException();
        }

        // Memo: replace this function after added a group to avoid a bug?
        PMA_MurmurAclHelper::removeInheritedGroups($this->groupList);
        /**
        * Mumble add group name in lower case, so do it.
        */
        $name = strToLower($name);

        $add = new Murmur_Group();
        $add->name = $name;
        $add->inherited = false;
        $add->inherit = true;
        $add->inheritable = true;
        $add->add = array();
        $add->members = array();
        $add->remove = array();

        $this->groupList[] = $add;

        $this->setACL();
        /**
        * Murmur will reindex keys of groups after setACL()
        * So get the group list a second time to find the new group and select it.
        */
        $this->prx->getACL($this->channelID, $aclList, $groupList, $inheritParent);

        foreach ($groupList as $key => $group) {
            if ($group->name === $name) {
                $_SESSION['page_vserver']['groupID'] = $key;
                break;
            }
        }
    }

    private function deleteGroup()
    {
        $this->sanity();

        $keepname = $this->groupList[$this->gid]->name;

        unset($this->groupList[$this->gid], $_SESSION['page_vserver']['groupID']);
        $this->setACL();
        /**
        * If we reset an inherited group, re-select it.
        */
        $this->prx->getACL($this->channelID, $aclList, $groupList, $inheritParent);

        foreach ($groupList as $key => $group) {
            if ($group->name === $keepname) {
                $_SESSION['page_vserver']['groupID'] = $key;
                break;
            }
        }
    }

    private function toggleGroupInheritFlag()
    {
        $this->sanity();
        $this->groupList[$this->gid]->inherit = ! $this->groupList[$this->gid]->inherit;
        $this->setACL();
    }

    private function toggleGroupInheritableFlag()
    {
        $this->sanity();
        $this->groupList[$this->gid]->inheritable = ! $this->groupList[$this->gid]->inheritable;
        $this->setACL();
    }

    private function addUser($id)
    {
        if (! ctype_digit($id)) {
            $this->messageError('invalid_numerical');
            $this->throwException();
        }

        $this->sanity();

        $this->groupList[$this->gid]->add[] = (int)$id;
        $this->setACL();
    }

    private function removeMember($id)
    {
        if (! ctype_digit($id)) {
            $this->messageError('invalid_numerical');
            $this->throwException();
        }

        $id = (int)$id;

        $this->sanity();

        foreach ($this->groupList[$this->gid]->add as $key => $uid) {
            if ($uid === $id) {
                unset($this->groupList[$this->gid]->add[$key]);
                // Memo: continue loop to end
            }
        }
        $this->setACL();
    }

    private function excludeMember($id)
    {
        if (! ctype_digit($id)) {
            $this->messageError('invalid_numerical');
            $this->throwException();
        }

        $id = (int)$id;

        $this->sanity();

        // Dont exclude "non-inherited" members.
        if (in_array($id, $this->groupList[$this->gid]->add, true)) {
            $this->messageError('non_inherited_member');
            $this->throwException();
        }

        // Check for a valid inherited uid
        foreach ($this->groupList[$this->gid]->members as $key => $uid) {
            if ($uid === $id) {
                $this->groupList[$this->gid]->remove[] = $id;
                $this->setACL();
                break;
            }
        }
    }

    private function removeExcludedMember($id)
    {
        if (! ctype_digit($id)) {
            $this->messageError('invalid_numerical');
            $this->throwException();
        }

        $id = (int)$id;

        $this->sanity();

        foreach ($this->groupList[$this->gid]->remove as $key => $uid) {
            if ($uid === $id) {
                unset($this->groupList[$this->gid]->remove[$key]);
                // Memo: continue loop to end
            }
        }
        $this->setACL();
    }
}

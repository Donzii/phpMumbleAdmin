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

class PMA_cmd_murmur_acl extends PMA_cmd
{
    private $prx;
    private $channelID;

    private $aclList;
    private $groupList;
    private $inheritFromParentACL;

    private $ACLID;

    private $total;
    private $lastACLkey;

    public function process()
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_SUPERUSER_RU)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }

        $this->getMurmurMeta();
        $this->prx = $this->getServerPrx($_SESSION['page_vserver']['id']);

        $this->channelID = $_SESSION['page_vserver']['cid'];
        $this->prx->getACL($this->channelID, $this->aclList, $this->groupList, $this->inheritFromParentACL);
        $this->total = count($this->aclList);
        $this->lastACLkey = $this->total -1;
        PMA_MurmurAclHelper::removeInheritedACL($this->aclList);
        PMA_MurmurAclHelper::removeInheritedGroups($this->groupList);
        // Fix a rare bug...
        reset($this->aclList);

        if (isset($this->PARAMS['toggle_inherit_acl'])) {
            $this->toggleInheritFromParent();
        } elseif (isset($this->PARAMS['add_acl'])) {
            $this->addACL();
        } elseif (isset($this->PARAMS['edit_acl'])) {
            $this->editACL();
        } elseif (isset($this->PARAMS['up_acl'])) {
            $this->pushUpACL();
        } elseif (isset($this->PARAMS['down_acl'])) {
            $this->pushDownACL();
        } elseif (isset($this->PARAMS['delete_acl'])) {
            $this->deleteACL();
        }
    }

    /**
    * updateACLs helper
    */
    private function updateACLs()
    {
        $this->prx->setACL($this->channelID, $this->aclList, $this->groupList, $this->inheritFromParentACL);
    }

    /**
    * selectACLID helper
    */
    private function selectACLID($id)
    {
        $this->ACLID = (int)$id;
        $_SESSION['page_vserver']['aclID'] = (int)$id;
    }

    /**
    * bitMasksCount helper
    */
    private function bitMasksCount($array)
    {
        $addition = 0;
        foreach ($array as $bit) {
            $addition += (int)$bit;
        }
        return $addition;
    }

    /**
    * Common sanity for actions which require a valid selected ACL.
    * @return object - Murmur_ACL class of the selected ACLID
    */
    private function getAclIDandAclObject()
    {
        if (! isset($_SESSION['page_vserver']['aclID']) OR ! is_int($_SESSION['page_vserver']['aclID'])) {
            $this->messageError('invalid_acl_id');
            $this->throwException();
        }
        $this->ACLID = $_SESSION['page_vserver']['aclID'];
        if (! isset($this->aclList[$this->ACLID])) {
            $this->messageError('invalid_acl_id');
            $this->throwException();
        }
        $acl = $this->aclList[$this->ACLID];
        // Deny SuperUserRu to edit SuperUserRu ACLs.
        if (
            $this->channelID === 0
            && PMA_MurmurAclHelper::isSuperUserRuRule($acl)
            && $this->PMA->user->is(PMA_USER_SUPERUSER_RU)
        ) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }
        return $acl;
    }

    private function toggleInheritFromParent()
    {
        $this->inheritFromParentACL = ! $this->inheritFromParentACL;
        if (! $this->inheritFromParentACL) {
            // Check if we have selected an inherited ACL, and unselect if true.
            if (
                isset($_SESSION['page_vserver']['aclID'])
                && $_SESSION['page_vserver']['aclID'] !== 'default'
                && ! isset($this->aclList[$_SESSION['page_vserver']['aclID']])
            ) {
                unset($_SESSION['page_vserver']['aclID']);
            }
        }
        $this->updateACLs();
    }

    private function addACL()
    {
        $new = new Murmur_ACL();
        $new->group = 'all';
        $new->userid = -1;
        $new->applyHere = true;
        $new->applySubs = true;
        $new->inherited = false;
        $new->allow = 0;
        $new->deny = 0;

        $this->aclList[] = $new;
        $this->updateACLs();
        // Select the new acl
        $this->selectACLID($this->total);
    }

    private function editACL()
    {
        $ACL = $this->getAclIDandAclObject();
        // Change group
        if ($this->PARAMS['group'] !== '' && $this->PARAMS['user'] === '') {
            $ACL->group = $this->PARAMS['group'];
            $ACL->userid = -1;
        }
        // Change user
        if (ctype_digit($this->PARAMS['user'])) {
            $ACL->userid =  (int)$this->PARAMS['user'];
            $ACL->group = null;
        }
        $ACL->applyHere = isset($this->PARAMS['applyHere']);
        $ACL->applySubs = isset($this->PARAMS['applySubs']);
        // Remove ACLs with both allow & deny key.
        if (isset($this->PARAMS['ALLOW'], $this->PARAMS['DENY'])) {
            foreach ($this->PARAMS['ALLOW'] as $key => $value) {
                if (isset($this->PARAMS['DENY'][$key])) {
                    unset($this->PARAMS['ALLOW'][$key], $this->PARAMS['DENY'][$key]);
                }
            }
        }
        if (isset($this->PARAMS['ALLOW'])) {
            $ACL->allow = $this->bitMasksCount($this->PARAMS['ALLOW']);
        } else {
            $ACL->allow = 0;
        }
        if (isset($this->PARAMS['DENY'])) {
            $ACL->deny = $this->bitMasksCount($this->PARAMS['DENY']);
        } else {
            $ACL->deny = 0;
        }
        $this->aclList[$this->ACLID] = $ACL;
        $this->updateACLs();
    }

    private function pushUpACL()
    {
        $acl = $this->getAclIDandAclObject();
        // Push up only if it's not the first ACL
        if ($this->ACLID !== key($this->aclList)) {
            $up = $this->ACLID -1;
            $down = $this->ACLID;

            $tmp[$up] = $this->aclList[$up];
            $tmp[$down] = $this->aclList[$down];

            $this->aclList[$up] = $tmp[$down];
            $this->aclList[$down] = $tmp[$up];

            $this->updateACLs();
            $this->selectACLID($up);
        }
    }

    private function pushDownACL()
    {
        $acl = $this->getAclIDandAclObject();
        // Push down only if it's not the last ACL
        if ($this->ACLID !== $this->lastACLkey) {
            $up = $this->ACLID;
            $down = $this->ACLID +1;

            $tmp[$up] = $this->aclList[$up];
            $tmp[$down] = $this->aclList[$down];

            $this->aclList[$up] = $tmp[$down];
            $this->aclList[$down] = $tmp[$up];

            $this->updateACLs();
            $this->selectACLID($down);
        }
    }

    private function deleteACL()
    {
        $acl = $this->getAclIDandAclObject();
        unset($this->aclList[$this->ACLID]);
        $this->updateACLs();
        // Stay on the last ACL if we deleted the last one.
        if ($this->ACLID === $this->lastACLkey) {
            $this->selectACLID($this->lastACLkey -1);
        }
    }
}

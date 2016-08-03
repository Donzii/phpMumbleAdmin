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

class mumbleAclEditor extends PMA_output
{
    /**
    * Lists
    */
    public $aclList = array();
    public $groupList = array();
    public $registeredUsers = array();
    public $permissions = array();
    /**
    * Attributs
    */
    public $inheritImg;
    public $inheritText;
    public $tableCss;
    /**
    * Selected ACL ID
    */
    public $id;
    /**
    * Selected ACL object
    */
    public $Acl;

    /**
    * Check if an ACL is selected by PMA user.
    */
    public function getNavigation()
    {
        // Get ACLID stored in session
        if (isset($_SESSION['page_vserver']['aclID'])) {
            $this->setid($_SESSION['page_vserver']['aclID']);
        }
        // Change selected ACLID
        if (isset($_GET['acl'])) {
            if ($_GET['acl'] === 'deselect') {
                $this->unsetid();
            } else {
                $this->setid($_GET['acl']);
            }
        }
    }

    private function setid($id)
    {
        if ($id === 'default') {
            $this->id = $id;
            $_SESSION['page_vserver']['aclID'] = $id;
        } elseif (ctype_digit($id) OR is_int($id)) {
            $this->id = (int)$id;
            $_SESSION['page_vserver']['aclID'] = (int)$id;
        }
    }

    private function unsetid()
    {
        $this->id = null;
        unset($_SESSION['page_vserver']['aclID']);
    }

    public function setupSelectedAclObject()
    {
        if (isset($this->aclList[$this->id])) {
            $this->Acl = $this->aclList[$this->id];
        }
    }

    public function getPermissionCheckBox($acl, $bit, $desc)
    {
        $stdClass = new stdClass();
        $stdClass->bit = $bit;
        $stdClass->desc = $desc;
        $stdClass->allow = (bool)($acl->allow & $bit);
        $stdClass->deny = (bool)($acl->deny & $bit);
        return $stdClass;
    }
}

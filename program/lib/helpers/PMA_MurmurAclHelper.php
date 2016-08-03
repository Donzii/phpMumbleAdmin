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

class PMA_MurmurAclHelper
{
    /**
    * Remove inherited ACLs
    * This function permit to not add inherited ACLs as new ACL with Murmur_server::setACL() method.
    */
    public static function removeInheritedACL(&$aclList)
    {
        foreach ($aclList as $key => $acl) {
            if ($acl->inherited) {
                unset($aclList[$key]);
            }
        }
    }

    /**
    * Remove inherited groups
    * This permit to avoid to remove the inherited flag with setACL().
    *
    * @param $keepKey - do not  remove this group.
    */
    public static function removeInheritedGroups(&$groupList, $keepKey = null)
    {
        foreach ($groupList as $key => $group) {
            if ($keepKey !== $key && $group->inherited) {
                unset($groupList[$key]);
            }
        }
    }

    /**
    *
    * Check if a registered user has SuperUserRu rights
    *
    * @param $uid - Mumble user ID.
    * @return boolean
    */
    public static function isSuperUserRu($uid, $aclList)
    {
        $isSuperUserRu = false;

        if ($uid > 0) {
            foreach ($aclList as $acl) {
                if ($acl->userid === $uid) {
                    // Memo: continue on false, maybe user has more than one ACL.
                    if (self::isSuperUserRuRule($acl)) {
                        $isSuperUserRu = true;
                        break;
                    }
                }
            }
        }
        return $isSuperUserRu;
    }

    /**
    * Check if a Murmur ACL match for SuperUserRu rights
    *
    * @return boolean
    */
    public static function isSuperUserRuRule(Murmur_ACL $acl)
    {
        return (
            $acl->allow & Murmur_PermissionWrite
            && $acl->userid > 0
            && $acl->applyHere
            && $acl->applySubs
        );
    }

    /**
    * Check if a Murmur ACL rule is a token
    *
    * @return Bool
    */
    public static function isToken(Murmur_ACL $acl)
    {
        return ($acl->userid === -1 && substr($acl->group, 0, 1) === '#');
    }

    /**
    * Check if a Murmur ACL rule is a "deny all" added with a token
    *
    * @return Bool
    */
    public static function isDenyAllToken(Murmur_ACL $acl)
    {
        return (
            $acl->group === 'all'
            && $acl->applyHere
            && $acl->applySubs
            && $acl->deny === 908
        );
    }
}

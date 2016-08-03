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
* For documentation on mumble groups, see:
* http://mumble.sourceforge.net/slice/Murmur/Group.html
*/

pmaLoadLanguage('vserver_group');

$PMA->widgets->newHiddenPopup('channelGroupAdd');

// Change selected groupID
if (isset($_GET['id'])) {
    if ($_GET['id'] === 'unset') {
        $_SESSION['page_vserver']['groupID'] = null;
    } else {
        $_SESSION['page_vserver']['groupID'] = (int)$_GET['id'];
    }
}
if (! isset($_SESSION['page_vserver']['groupID'])) {
    $_SESSION['page_vserver']['groupID'] = null;
}

$widget->id = $_SESSION['page_vserver']['groupID'];
$widget->group = null;
$widget->groups = array();
$widget->parentGroups = array();
$widget->usersAvailable = array();
$widget->members = array();
$widget->inheritedMembers = array();
$widget->excludedMembers = array();
/**
* Get datas from $prx
*/
$prx->getACL($module->channelObj->id, $aclList, $groupList, $inherit);
/**
* Get the parent channel groups.
*/
if ($module->channelObj->id > 0) {
    $id = $module->channelsList[$module->channelObj->id]->parent;
    $prx->getACL($id, $foo, $parentGroupList, $foo);
}
/**
* Add only inheritable parent group.
*/
if (isset($parentGroupList)) {
    foreach ($parentGroupList as $key => $group) {
        if ($group->inheritable) {
            $widget->parentGroups[] = $group->name;
        }
    }
}
sortObjBy($groupList, 'name');
/**
* Contruct the groups menu.
*/
foreach ($groupList as $key => $obj) {

    $group = clone $obj;

    $group->css = '';
    $group->imgCss = '';
    $group->href = $key;
    $group->modified = false;

    // inherited.
    if ($group->inherited) {
        $group->imgCss = 'groupInherited';
    /**
    * When an inherited group is modified, murmur remove the $inherited flag,
    * when this group is deleted, the flag come back.
    * It's like the group become a fully autonomous layer when modified.
    */
    } elseif (in_array($group->name, $widget->parentGroups, true)) {
        $group->inherited = true; // Remark as $inherited
        $group->modified = true; // Mark as $modified
        $group->imgCss = 'groupModified';
    }
    // Selected group
    if ($key === $widget->id) {
        $group->css = 'selected';
        $group->href = 'unset';
        $widget->group = $group;
    }
    $widget->groups[$key] = $group;
}

if (is_object($widget->group)) {

    $registeredUsers = $prx->getRegisteredUsers('');

    // Inherit
    if ($widget->group->inherit) {
        $widget->group->inheritImg = 'images/xchat/blue_16.png';
    } else {
        $widget->group->inheritImg = IMG_SPACE_16;
    }
    // Inheritable
    if ($widget->group->inheritable) {
        $widget->group->inheritableImg = 'images/xchat/purple_16.png';
    } else {
        $widget->group->inheritableImg = IMG_SPACE_16;
    }

    $widget->usersAvailable = array();
    foreach ($registeredUsers as $uid => $name) {
        // Show only users which are not already members.
        if (! in_array($uid, $widget->group->members, true) && ! in_array($uid, $widget->group->remove, true)) {
            $widget->usersAvailable[$uid] = htEncSpace(cutLongString($name, 40));
        }
    }

    /**
    * List of users to add to the group.
    */
    foreach ($widget->group->add as $uid) {
        $member = new stdClass();
        $member->id = $uid;
        $member->href='?cmd=murmur_groups&amp;removeMember='.$uid;
        $member->login = $registeredUsers[$uid];
        $widget->members[] = $member;
    }

    /**
    * $widget->group->members:
    * Current members of the group, including inherited members.
    *
    * Show inherited members only if the group accept inherited members
    */
    if ($widget->group->inherit) {
        foreach ($widget->group->members as $uid) {
            // Dont show members from $widget->group->add
            if (! in_array($uid, $widget->group->add, true)) {
                $member = new stdClass();
                $member->id = $uid;
                $member->href='?cmd=murmur_groups&amp;excludeMember='.$uid;
                $member->login = $registeredUsers[$uid];
                $widget->inheritedMembers[] = $member;
            }
        }
    }

    /**
    * $widget->group->remove:
    * List of inherited users to remove from the group.
    *
    * Show inherited excluded members only if the group accept inherited members
    */
    if ($widget->group->inherit) {
        foreach ($widget->group->remove as $uid) {
            $member = new stdClass();
            $member->id = $uid;
            $member->href='?cmd=murmur_groups&amp;removeExcluded='.$uid;
            $member->login = $registeredUsers[$uid];
            $widget->excludedMembers[] = $member;
        }
    }
}

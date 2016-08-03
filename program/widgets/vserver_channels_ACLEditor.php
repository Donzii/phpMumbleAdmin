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
* Setup
*/
$widget = new mumbleAclEditor();

pmaLoadLanguage('vserver_acl');
/**
* Get datas from $prx
*/
$prx->getACL($module->channelObj->id, $aclList, $groupList, $inheritFromParent);
$registeredUsers = $prx->getRegisteredUsers('');
/**
* Create the default ACL.
*/
$default = new Murmur_acl();
$default->isDefault = true;
$default->allow = 782;
$default->deny = 984305;
$default->inherited = true;
$default->applyHere = true;
$default->applySubs = true;
$default->userid = -1;
$default->group = 'all';
$merge['default'] = $default;
$aclList = array_merge($merge, $aclList);
/**
* Get route
*/
$widget->getNavigation();
/**
* Contruct the ACL menu.
*/
foreach ($aclList as $key => $acl) {

    $acl = clone $acl;
    /**
    * Add some customs variables into the ACL object.
    */
    $acl->isDefault = (isset($acl->isDefault) && $acl->isDefault);
    /**
    * Do not add inherited ACL if the channel do not $inheritFromParent,
    * but keep the default ACL.
    */
    if (! $inheritFromParent && $acl->inherited && ! $acl->isDefault) {
        continue;
    }
    // Mark ACL as SuperUserRu rule
    $acl->isSuperUserRu = ($module->channelObj->id === 0 && PMA_MurmurAclHelper::isSuperUserRuRule($acl));
    // Highlight to SuperUsers and superior SuperUserRu ACLs.
    $acl->showAsSuperUserRu = (
        $acl->isSuperUserRu
        && $PMA->config->get('SU_auth')
        && $PMA->config->get('SU_ru_active')
        && $PMA->user->isMinimum(PMA_USER_SUPERUSER)
    );
    // ACL menu css
    $acl->css = '';
    // ACL ID
    $acl->href = $key;
    // ACL name (group or user)
    $acl->name = $acl->group;
    // ACL disabled status
    $acl->isDisabled = (
        $acl->inherited
        // SuperUserRu can't change SuperUserRu ACLs
        OR ($acl->isSuperUserRu && $PMA->user->is(PMA_USER_SUPERUSER_RU))
        OR $acl->isDefault
    );

    if ($widget->id === $key) {
        $acl->href = 'deselect';
        $acl->css = 'selected';
        if ($acl->isDisabled) {
            $acl->css .= ' disabled';
        }
    } elseif ($acl->isDisabled) {
        $acl->css = 'disabled';
    }
    // Get img path.
    if (PMA_MurmurAclHelper::isToken($acl) OR PMA_MurmurAclHelper::isDenyAllToken($acl)) {
        $acl->img = 'images/gei/padlock_16.png';
    } elseif ($acl->userid === -1) {
        $acl->img = 'images/tango/group_16.png';
    } else {
        $acl->img = 'images/mumble/user_auth.png';
        $acl->name = $registeredUsers[$acl->userid];
    }
    $widget->aclList[$key] = $acl;
}
/**
* Setup the ACL menu.
*/
if ($inheritFromParent) {
    $widget->inheritImg = 'images/xchat/blue_16.png';
    $widget->inheritText = $TEXT['inherit_parent_channel'];
} else {
    $widget->inheritImg = 'images/pma/space.png';
    $widget->inheritText = $TEXT['do_not_inherit'];
}
/**
* Setup selected ACL permission.
*/
$widget->setupSelectedAclObject();

if (is_object($widget->Acl)) {
    $permissions[Murmur_PermissionWrite] = $TEXT['acl_write'];
    $permissions[Murmur_PermissionTraverse] = $TEXT['acl_traverse'];
    $permissions[Murmur_PermissionEnter] = $TEXT['acl_enter'];
    $permissions[Murmur_PermissionSpeak] = $TEXT['acl_speak'];
    $permissions[Murmur_PermissionMuteDeafen] = $TEXT['acl_muteDeaf'];
    $permissions[Murmur_PermissionMove] = $TEXT['acl_move'];
    $permissions[Murmur_PermissionMakeChannel] = $TEXT['acl_make'];
    $permissions[Murmur_PermissionLinkChannel] = $TEXT['acl_link'];
    $permissions[Murmur_PermissionWhisper] = $TEXT['acl_wisp'];
    $permissions[Murmur_PermissionTextMessage] = $TEXT['acl_txt'];
    $permissions[Murmur_PermissionMakeTempChannel] = $TEXT['acl_temporary'];
    $rootPermissions[Murmur_PermissionKick] = $TEXT['acl_kick'];
    $rootPermissions[Murmur_PermissionBan] = $TEXT['acl_ban'];
    $rootPermissions[Murmur_PermissionRegister] = $TEXT['acl_register'];
    $rootPermissions[Murmur_PermissionRegisterSelf] = $TEXT['acl_register_self'];

    if ($widget->Acl->isDisabled) {
        $widget->tableCss = 'disabled';
    } else {
        // Add custom groups.
        foreach ($groupList as $group) {
            $widget->groupList[] = $group->name;
        }
        // Add registered users.
        foreach ($registeredUsers as $uid => $login) {
            $widget->registeredUsers[$uid] = $login;
        }
    }

    foreach ($permissions as $bit => $desc) {
        $perm = $widget->getPermissionCheckBox($widget->Acl, $bit, $desc);
        $widget->permissions[] = $perm;
    }

    if ($module->channelObj->id === 0) {
        // Add the root flag
        $widget->permissions[] = 'rootFlag';
        foreach ($rootPermissions as $bit => $desc) {
            $perm = $widget->getPermissionCheckBox($widget->Acl, $bit, $desc);
            $widget->permissions[] = $perm;
        }
    }
}

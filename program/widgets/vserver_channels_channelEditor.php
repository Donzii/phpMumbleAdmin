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
* Get datas from $prx
*/
$prx->getACL($module->channelObj->id, $aclList, $groupList, $inherit);
/**
* Default channel checkbox variables.
*/
$isDefault = ($module->defaultChannelID === $module->channelObj->id);
/**
* Get the token password.
*/
$password = '';
foreach ($aclList as $acl) {
    if (! $acl->inherited && PMA_MurmurAclHelper::isToken($acl)) {
        $password = substr($acl->group, 1);
        break;
    }
}
/**
* Setup variables.
*/
$widget->set('isDefault', $isDefault);
$widget->set('isDisabled', ($isDefault OR $module->channelObj->temporary));
$widget->id = $module->channelObj->id;
$widget->set('name', $module->channelObj->name);
$widget->set('password', $password);
$widget->set('position', $module->channelObj->position);
$widget->set('desc', $module->channelObj->description);

PMA_sandBoxHelper::create($module->channelObj->description);

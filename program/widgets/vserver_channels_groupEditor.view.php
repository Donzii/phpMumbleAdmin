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

$widget = $PMA->widgets->getDatas('vserver_channels_groupEditor'); ?>

<div class="toolbar">
    <a href="?action=add_group" onClick="return popup('channelGroupAdd');" class="button">
        <img src="<?php echo IMG_ADD_16; ?>" title="<?php echo $TEXT['add_group']; ?>" alt="" />
    </a>
</div>

<ul id="menuList">
<?php if (empty($widget->groups)): ?>
   <li class="empty">
        <span><?php echo $TEXT['no_group']; ?></span>
    </li>
<?php endif;
foreach ($widget->groups as $g): ?>
    <li>
        <a href="?id=<?php echo $g->href; ?>" class="<?php echo $g->css; ?>">
            <img src="<?php echo 'images/tango/group_16.png' ?>" class="<?php echo $g->imgCss; ?>" alt="" />
            <span class="text"><?php echo htEnc($g->name); ?></span>
        </a>
    </li>
<?php endforeach; ?>
</ul>

<?php
/**
* No group selected. No need to continue.
*/
if (! is_object($widget->group)) {
    return;
}
?>

<div class="toolbar">
<?php if (! $widget->group->inherited): ?>
    <div class="left">
        <a href="?cmd=murmur_groups&amp;deleteGroup" title="<?php echo $TEXT['del_group']; ?>" class="button">
            <img src="<?php echo IMG_TRASH_16; ?>" alt="" />
        </a>
    </div>
<?php else: ?>
    <div class="left"><?php echo $TEXT['inherited_group']; ?></div>
<?php endif;
if ($widget->group->modified): ?>
    <div class="left">
        <a href="?cmd=murmur_groups&amp;deleteGroup" title="<?php echo $TEXT['reset_inherited_group']; ?>" class="button">
            <img src="<?php echo IMG_CLEAN_16; ?>" alt="" />
        </a>
    </div>
<?php endif; ?>
    <a href="?cmd=murmur_groups&amp;toggle_group_inherit" title="<?php echo $TEXT['inherit_parent_group']; ?>" class="button">
        <img src="<?php echo $widget->group->inheritImg; ?>" alt="" />
    </a>
    <a href="?cmd=murmur_groups&amp;toggle_group_inheritable" title="<?php echo $TEXT['inheritable_sub']; ?>" class="button">
        <img src="<?php echo $widget->group->inheritableImg; ?>" alt="" />
    </a>
</div>

<form method="post" class="addGroup">
    <input type="hidden" name="cmd" value="murmur_groups" />
    <fieldset>
        <legend><?php echo $TEXT['add_user_to_group']; ?></legend>
        <select id="add_user" name="add_user" required="required">
            <option value="">-</option>
<?php foreach ($widget->usersAvailable as $uid => $name): ?>
            <option value="<?php echo $uid; ?>"><?php echo $uid.'# '.$name; ?></option>
<?php endforeach; ?>
        </select>
        <input type="submit" class="submit" value="<?php echo $TEXT['add']; ?>" />
    </fieldset>
</form>

<h4><?php echo $TEXT['members']; ?></h4>
<ul class="groupMembers">
<?php if (empty($widget->members)): ?>
    <li class="empty">
        <span class="login"><?php echo $TEXT['empty']; ?></span>
    </li>
<?php endif;
foreach ($widget->members as $m): ?>
    <li>
        <a href="<?php echo $m->href; ?>" class="button" title="<?php echo $TEXT['remove_member']; ?>">
            <img src="<?php echo IMG_DELETE_16; ?>" alt="" />
        </a>
        <span class="login"><?php echo htEnc($m->login); ?></span>
    </li>
<?php endforeach; ?>
</ul>

<?php
/**
* No need to show inherited members and excluded members if the group is not inherited.
*/
if (! $widget->group->inherited) {
    return;
}
?>

<h4><?php echo $TEXT['inherited_members']; ?></h4>
<ul class="groupMembers">
<?php if (empty($widget->inheritedMembers)): ?>
    <li class="empty">
        <span class="login"><?php echo $TEXT['empty']; ?></span>
    </li>
<?php endif;
foreach ($widget->inheritedMembers as $m): ?>
    <li>
        <a href="<?php echo $m->href; ?>" class="button" title="<?php echo $TEXT['exclude_inherited']; ?>">
            <img src="<?php echo IMG_DOWN_16; ?>" alt="" />
        </a>
        <span class="login"><?php echo htEnc($m->login); ?></span>
    </li>
<?php endforeach; ?>
</ul>

<h4><?php echo $TEXT['excluded_members']; ?></h4>
<ul class="groupMembers">
<?php if (empty($widget->excludedMembers)): ?>
    <li class="empty">
        <span class="login"><?php echo $TEXT['empty']; ?></span>
    </li>
<?php endif;
foreach ($widget->excludedMembers as $m): ?>
    <li>
        <a href="<?php echo $m->href; ?>" class="button" title="<?php echo $TEXT['remove_excluded']; ?>">
            <img src="<?php echo IMG_UP_16; ?>" alt="" />
        </a>
        <span class="login"><?php echo htEnc($m->login); ?></span>
    </li>
<?php endforeach; ?>
</ul>

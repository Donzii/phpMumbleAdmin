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

$widget = $PMA->widgets->getDatas('vserver_channels_ACLEditor'); ?>

<div class="toolbar">
<?php if ($module->channelObj->id > 0): ?>
    <div class="left">
        <a href="?cmd=murmur_acl&amp;toggle_inherit_acl" class="button" title="<?php echo $widget->inheritText; ?>">
            <img src="<?php echo $widget->inheritImg; ?>" alt="" />
        </a>
    </div>
<?php endif; ?>
    <a href="?cmd=murmur_acl&amp;add_acl" class="button" title="<?php echo $TEXT['add_acl']; ?>">
        <img src="<?php echo IMG_ADD_16; ?>" alt="" />
    </a>
</div>

<ul id="menuList">
<?php foreach ($widget->aclList as $acl): ?>
    <li>
        <a href="?acl=<?php echo $acl->href; ?>" class="<?php echo $acl->css; ?>">
            <img src="<?php echo $acl->img; ?>" alt="" />
            <span class="text"><?php echo htEnc($acl->name); ?></span>
<?php if ($acl->isDefault): ?>
            <span>(<?php echo $TEXT['default_acl']; ?>)</span>
<?php elseif ($acl->showAsSuperUserRu): ?>
            <span>(<?php echo pmaGetClassName(PMA_USER_SUPERUSER_RU); ?>)</span>
<?php endif; ?>
        </a>
    </li>
<?php endforeach; ?>
</ul>

<?php
/**
* No ACL selected. No need to continue.
*/
if (! is_object($widget->Acl)) {
    return;
}
?>

<div class="toolbar">
<?php if (! $widget->Acl->isDisabled): ?>
    <div class="left">
        <a href="?cmd=murmur_acl&amp;delete_acl" class="button" title="<?php echo $TEXT['del_rule']; ?>">
            <img src="<?php echo IMG_TRASH_16; ?>" alt="" />
        </a>
    </div>
    <a href="?cmd=murmur_acl&amp;down_acl" class="button" title="<?php echo $TEXT['down_rule']; ?>">
        <img src="<?php echo IMG_DOWN_16; ?>" alt="" />
    </a>
    <a href="?cmd=murmur_acl&amp;up_acl" class="button" title="<?php echo $TEXT['up_rule']; ?>">
        <img src="<?php echo IMG_UP_16; ?>" alt="" />
    </a>
<?php endif; ?>
</div>

<form class="<?php echo $widget->tableCss; ?>" method="post" onSubmit="return isFormModified(this);">

    <input type="hidden" name="cmd" value="murmur_acl" />
    <input type="hidden" name="edit_acl" />

    <fieldset <?php HTML::disabled($widget->Acl->isDisabled); ?> id="ACL">

        <select id="groups" name="group" onChange="unselect('users')">
            <option value=""><?php echo $TEXT['select_group']; ?></option>
            <optgroup label="System">
                <option value="all">all</option>
                <option value="auth">auth</option>
                <option value="in">in</option>
                <option value="sub">sub</option>
                <option value="out">out</option>
                <option value="~in">~in</option>
                <option value="~sub">~sub</option>
                <option value="~out">~out</option>
            </optgroup>
            <optgroup label="Custom">
<?php foreach ($widget->groupList as $group): ?>
                <option value="<?php echo $group; ?>"><?php echo htEncSpace(cutLongString($group, 40)); ?></option>
<?php endforeach; ?>
            </optgroup>
        </select>

        <select id="users" name="user" onChange="unselect('groups')">
            <option value=""><?php echo $TEXT['select_user']; ?></option>
<?php foreach ($widget->registeredUsers as $uid => $login): ?>
            <option value="<?php echo $uid; ?>"><?php echo $uid.' # '.htEncSpace(cutLongString($login, 40)); ?></option>
<?php endforeach; ?>
        </select>

        <table>

            <tr>
                <th colspan="2">
                    <label for="applyHere"><?php echo $TEXT['apply_this_channel']; ?></label>
                </th>
                <td>
                    <input type="checkbox" id="applyHere" name="applyHere" <?php HTML::chked($widget->Acl->applyHere); ?> />
                </td>
            </tr>

            <tr>
                <th colspan="2">
                    <label for="applySubs"><?php echo $TEXT['apply_sub_channel']; ?></label>
                </th>
                <td>
                    <input type="checkbox" id="applySubs" name="applySubs" <?php HTML::chked($widget->Acl->applySubs); ?> />
                </td>
            </tr>

            <tr>
                <th><?php echo $TEXT['permissions']; ?></th>
                <th><?php echo $TEXT['deny']; ?></th>
                <th><?php echo $TEXT['allow']; ?></th>
            </tr>

<?php foreach ($widget->permissions as $p):
if (is_string($p)): ?>
            <tr>
                <th colspan="3"><?php echo $TEXT['specific_root']; ?></th>
            </tr>
<?php else: ?>

            <tr>
                <th><?php echo $p->desc; ?></th>
                <td>
                    <input type="checkbox" name="DENY[<?php echo $p->bit; ?>]" value="<?php echo $p->bit; ?>"
                    <?php HTML::chked($p->deny); ?> onClick="uncheck('ALLOW[<?php echo $p->bit; ?>]')" />
                </td>
                <td>
                    <input type="checkbox" name="ALLOW[<?php echo $p->bit; ?>]" value="<?php echo $p->bit; ?>"
                    <?php HTML::chked($p->allow); ?> onClick="uncheck('DENY[<?php echo $p->bit; ?>]')" />
                </td>
            </tr>
<?php endif;
endforeach; ?>

            <tr>
                <th colspan="3">
                    <input type="submit" value="<?php echo $TEXT['apply']; ?>" />
                </th>
            </tr>

        </table>

    </fieldset>

</form>

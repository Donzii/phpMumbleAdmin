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

if (! defined('PMA_STARTED')) { die('ILLEGAL: You cannot call this script directly !'); } ?>

<div class="toolbar">
    <a href="?addMumbleAccount" class="button" title="<?php echo $TEXT['add_acc']; ?>" onClick="return popup('mumbleRegistrationAdd');">
        <img src="<?php echo IMG_ADD_22; ?>" alt="" />
    </a>
<?php require $PMA->widgets->getView('widget_search'); ?>
</div>

<?php require $PMA->widgets->getView('widget_tablePagingMenu'); ?>

<table>

    <tr class="pad">
        <th class="icon">
            <a href="<?php echo $module->table->getColHref('status'); ?>"
                title="<?php echo $TEXT['sort_by']; ?>"><?php echo $module->table->getColText('status'); ?></a>
        </th>
        <th class="id">
            <a href="<?php echo $module->table->getColHref('uid'); ?>"
                title="<?php echo $TEXT['sort_by']; ?>"><?php echo $module->table->getColText('uid'); ?></a>
        </th>
        <th>
            <a href="<?php echo $module->table->getColHref('login'); ?>"
                title="<?php echo $TEXT['sort_by']; ?>"><?php echo $module->table->getColText('login'); ?></a>
        </th>
        <th class="vlarge">
            <a href="<?php echo $module->table->getColHref('email'); ?>"
                title="<?php echo $TEXT['sort_by']; ?>"><?php echo $module->table->getColText('email'); ?></a>
        </th>
<?php if ($module->displayLastActivity): ?>
        <th class="large">
            <a href="<?php echo $module->table->getColHref('lastActivity'); ?>"
                title="<?php echo $TEXT['sort_by']; ?>"><?php echo $module->table->getColText('lastActivity'); ?></a>
        </th>
<?php endif; ?>
        <th class="icon">
            <a href="<?php echo $module->table->getColHref('comment'); ?>"
                title="<?php echo $TEXT['sort_by']; ?>"><?php echo $module->table->getColText('comment'); ?></a>
        </th>
        <th class="icon">
            <a href="<?php echo $module->table->getColHref('hash'); ?>"
                title="<?php echo $TEXT['sort_by']; ?>"><?php echo $module->table->getColText('hash'); ?></a>
        </th>
        <th class="icon"></th>
    </tr>

<?php foreach ($module->table->datas as $d): ?>
    <tr>
        <td class="icon">
<?php if ($d->status === 1): ?>
            <a href="?tab=channels&amp;userSession=<?php echo $d->statusURL; ?>" class="button on">
                <img src="<?php echo IMG_SPACE_16; ?>" alt="" />
            </a>
<?php elseif ($d->status === 2): ?>
            <img src="<?php echo IMG_SPACE_16; ?>" class="button off" alt="" />
<?php endif; ?>
        </td>
<?php if (is_int($d->uid)): ?>
        <td class="id">
            <span><?php echo $d->uid; ?></span>
        </td>
<?php else: ?>
        <td>
        </td>
<?php endif; ?>
        <td class="selection">
<?php if (is_int($d->uid)): ?>
            <a href="?mumbleRegistration=<?php echo $d->uid; ?>">
                <span class="text"><?php echo $d->loginEnc; ?></span>
            </a>
<?php endif; ?>
        </td>
        <td>
<?php if ($d->email !== ''): ?>
            <a href="mailto:<?php echo $d->emailEnc; ?>" class="mailto" title="mailto:<?php echo $d->emailEnc; ?>">
                <span><?php echo $d->emailEnc; ?></span>
            </a>
<?php endif; ?>
        </td>
<?php if ($module->displayLastActivity): ?>
        <td>
<?php if ($d->lastActivityUptime !== ''): ?>
            <span class="help" title="<?php echo $d->lastActivityDate; ?>"><?php echo $d->lastActivityUptime; ?></span>
<?php endif; ?>
        </td>
<?php endif; ?>
        <td class="icon">
<?php if ($d->comment === 1): ?>
            <img src="images/mumble/comment.png" alt="" />
<?php endif; ?>
        </td>
        <td class="icon">
<?php if ($d->hash === 1): ?>
            <img src="<?php echo IMG_OK_16; ?>" alt="" />
<?php endif; ?>
        </td>
        <td class="icon">
<?php if ($d->delete): ?>
            <a href="?deleteMumbleAccountID=<?php echo $d->uid; ?>" class="button"
                onClick="return popupDeleteMumbleID(this, '<?php echo $d->uid; ?>', '<?php echo $d->loginEnc; ?>');">
                <img src="<?php echo IMG_TRASH_16; ?>" alt="" />
            </a>
<?php endif; ?>
        </td>
    </tr>
<?php endforeach; ?>

</table>

<?php require $PMA->widgets->getView('widget_tablePagingMenu');


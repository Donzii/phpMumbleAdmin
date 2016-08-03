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
    <a href="?add_admin" class="button" title="<?php echo $TEXT['add_admin']; ?>" onClick="return popup('adminAdd')">
        <img src="<?php echo IMG_ADD_22; ?>" alt="" />
    </a>
</div>

<table>

    <tr class="pad">
        <th class="small">
            <a href="<?php echo $module->table->getColHref('class'); ?>"
                title="<?php echo $TEXT['sort_by']; ?>"><?php echo $module->table->getColText('class'); ?></a>
        </th>
        <th class="id">
            <a href="<?php echo $module->table->getColHref('id'); ?>"
                title="<?php echo $TEXT['sort_by']; ?>"><?php echo $module->table->getColText('id'); ?></a>
        </th>
        <th>
            <a href="<?php echo $module->table->getColHref('login'); ?>"
                title="<?php echo $TEXT['sort_by']; ?>"><?php echo $module->table->getColText('login'); ?></a>
        </th>
        <th class="icon">A</th>
        <th class="large">
            <a href="<?php echo $module->table->getColHref('last_conn'); ?>"
                title="<?php echo $TEXT['sort_by']; ?>"><?php echo $module->table->getColText('last_conn'); ?></a>
        </th>
        <th class="icon"></th>
    </tr>

<?php foreach ($module->table->datas as $d): ?>
    <tr>
        <td class="<?php echo $d->className; ?>">
<?php if (is_int($d->id)): ?>
            <span><?php echo $d->className; ?></span>
<?php endif; ?>
        </td>
<?php if (is_int($d->id)): ?>
        <td class="id">
            <span><?php echo $d->id; ?></span>
        </td>
<?php else: ?>
        <td>
        </td>
<?php endif; ?>
        <td  class="selection">
<?php if (is_int($d->id)): ?>
            <a href="?adminRegistration=<?php echo $d->id; ?>"><?php echo $d->loginEnc; ?></a>
<?php endif; ?>
        </td>
        <td class="icon tooltip">
<?php if (! empty($d->access)): ?>
            <span class="tooltip right">
                <img src="<?php echo IMG_INFO_16; ?>" alt="" />
                <span class="desc">
<?php foreach ($d->access as $profile): ?>
                    <span><?php echo htEnc($profile); ?></span><br />
<?php endforeach; ?>
                </span>
            </span>
<?php endif; ?>
        </td>
        <td>
<?php if ($d->lastConn !== ''): ?>
            <span class="help" title="<?php echo $d->lastConnDate; ?>"><?php echo $d->lastConn; ?></span>
<?php endif; ?>
        </td>
        <td class="icon">
<?php if (is_int($d->id)): ?>
            <a href="?remove_admin=<?php echo $d->id; ?>" class="button" title="<?php echo $TEXT['del_admin']; ?>"
                onClick="return popupDeleteAdmin(this, '<?php echo $d->id; ?>', '<?php echo $d->loginEnc; ?>')">
                <img src="<?php echo IMG_TRASH_16; ?>" alt="" />
            </a>
<?php endif; ?>
        </td>
    </tr>
<?php endforeach; ?>

</table>


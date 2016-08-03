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
    <a href="?addBan" class="button" title="<?php echo $TEXT['add_ban']; ?>">
        <img src="<?php echo IMG_ADD_22; ?>" alt="" />
    </a>
</div>

<?php require $PMA->widgets->getView('widget_tablePagingMenu'); ?>

<table id="murmurBans">

    <tr class="pad">
        <th></th>
        <th class="icon"></th>
        <th class="large"><?php echo $TEXT['started']; ?></th>
        <th class="large"><?php echo $TEXT['end']; ?></th>
        <th class="icon"></th>
    </tr>

<?php foreach ($module->table->datas as $d): ?>
    <tr>
        <td class="selection large">
<?php if ($d->selection): ?>
            <a href="?edit_ban_id=<?php echo $d->key; ?>">
<?php if ($d->userName !== ''): ?>
                <mark class="text"><?php echo htEnc($d->userName); ?></mark>
<?php endif; ?>
                <br />
                <span><?php echo $d->ip; ?></span>
<?php if ($d->mask !== ''): ?>
                / <mark class="mask"><?php echo $d->mask; ?></mark>
<?php endif; ?>
                <br />
<?php if ($d->reason !== ''): ?>
                <span class="text info"><?php echo htEnc(replaceEOL($d->reason)); ?></span>
<?php endif; ?>
            </a>
<?php endif; ?>
        </td>
        <td class="icon">
<?php if ($d->hash): ?>
            <img src="<?php echo IMG_OK_16; ?>" title="<?php echo $TEXT['cert_included']; ?>" alt="" />
<?php endif; ?>
        </td>
        <td>
            <span><?php echo $d->startedDate; ?></span><br />
            <span><?php echo $d->startedTime; ?></span>
        </td>
        <td>
            <span><?php echo $d->durationDate; ?></span><br />
            <span><?php echo $d->durationTime; ?></span>
        </td>
        <td class="icon">
<?php if ($d->delete): ?>
            <a href="?delete_ban_id=<?php echo $d->key; ?>" class="button" title="<?php echo $TEXT['del_ban']; ?>"
                onClick="return popupDeleteBan(this, '<?php echo $d->key; ?>');">
                <img src="<?php echo IMG_TRASH_16; ?>" alt="" />
            </a>
<?php endif; ?>
        </td>
    </tr>
<?php endforeach; ?>

</table>

<?php require $PMA->widgets->getView('widget_tablePagingMenu');

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
</div>

<table>

    <tr class="pad">
        <th class="large">
            <a href="<?php echo $module->table->getColHref('ip'); ?>"
                title="<?php echo $TEXT['sort_by']; ?>"><?php echo $module->table->getColText('ip'); ?></a>
        </th>
        <th class="small">
            <a href="<?php echo $module->table->getColHref('start'); ?>"
                title="<?php echo $TEXT['sort_by']; ?>"><?php echo $module->table->getColText('start'); ?></a>
        </th>
        <th class="small">
            <a href="<?php echo $module->table->getColHref('duration'); ?>"
                title="<?php echo $TEXT['sort_by']; ?>"><?php echo $module->table->getColText('duration'); ?></a>
        </th>
        <th class="small">
            <a href="<?php echo $module->table->getColHref('comment'); ?>"
                title="<?php echo $TEXT['sort_by']; ?>"><?php echo $module->table->getColText('comment'); ?></a>
        </th>
        <th class="icon">Delete</th>
    </tr>

<?php foreach ($module->table->datas as $data): ?>
    <tr>
        <td><?php echo $data->ip; ?></td>
        <td><?php echo $data->start; ?></td>
        <td><?php echo $data->duration; ?></td>
        <td><?php echo $data->comment; ?></td>
        <td class="icon">
<?php if ($data->delete): ?>
            <img src="<?php echo IMG_TRASH_16; ?>" class="button" alt="" />
<?php endif; ?>
        </td>
    </tr>
<?php endforeach; ?>

</table>

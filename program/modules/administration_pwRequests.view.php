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
    <img src="images/tango/clock_22.png" alt="" />
    <span><?php echo $TEXT['pw_request_pending']; ?></span>
</div>

<table>

    <tr class="pad">
        <th class="large">
            <a href="<?php echo $module->table->getColHref('end'); ?>"
                title="<?php echo $TEXT['sort_by']; ?>"><?php echo $module->table->getColText('end'); ?></a>
        </th>
        <th>
            <a href="<?php echo $module->table->getColHref('login'); ?>"
                title="<?php echo $TEXT['sort_by']; ?>"><?php echo $module->table->getColText('login'); ?></a>
        </th>
        <th class="large">
            <a href="<?php echo $module->table->getColHref('ip'); ?>"
                title="<?php echo $TEXT['sort_by']; ?>"><?php echo $module->table->getColText('ip'); ?></a>
        </th>
        <th class="id">
            <a href="<?php echo $module->table->getColHref('profile_id'); ?>"
                title="<?php echo $TEXT['sort_by']; ?>"><?php echo $module->table->getColText('profile_id'); ?></a>
        </th>
        <th class="id">sid</th>
        <th class="id">uid</th>
        <th><?php echo $TEXT['request_id']; ?></th>
    </tr>

<?php foreach ($module->table->datas as $d): ?>
    <tr>
        <td>
<?php if (isset($d->uptime)): ?>
            <span class="help" title="<?php printf($TEXT['started_at'], $d->date, $d->time); ?>"><?php echo $d->uptime; ?></span>
<?php endif; ?>
        </td>
        <td><?php echo htEnc($d->login); ?></td>
        <td><?php echo $d->ip; ?></td>
        <td class="icon"><?php echo $d->pid; ?></td>
        <td class="icon"><?php echo $d->sid; ?></td>
        <td class="icon"><?php echo $d->uid; ?></td>
        <td><?php echo $d->id; ?></td>
    </tr>
<?php endforeach; ?>

    <tr class="pad">
        <th colspan="7">
            <span class="help" title="<?php echo $TEXT['ice_profile'];?>">(iid)</span>
            <span class="help" title="<?php echo $TEXT['sid'];?>">(sid)</span>
            <span class="help" title="<?php echo $TEXT['uid'];?>">(uid)</span>
        </th>
    </tr>

</table>

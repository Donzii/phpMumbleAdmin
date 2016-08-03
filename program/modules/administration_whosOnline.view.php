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
    <img src="images/tango/whois_22.png" alt="" />
    <span><?php echo $TEXT['whos_online'];?></span>
</div>

<table>

    <thead>
        <tr class="pad">
            <th class="small">
                <a href="<?php echo $module->table->getColHref('class'); ?>"
                    title="<?php echo $TEXT['sort_by']; ?>"><?php echo $module->table->getColText('class'); ?></a>
            </th>
            <th>
                <a href="<?php echo $module->table->getColHref('login'); ?>"
                    title="<?php echo $TEXT['sort_by']; ?>"><?php echo $module->table->getColText('login'); ?></a>
            </th>
            <th class="vlarge">
                <a href="<?php echo $module->table->getColHref('current_ip'); ?>"
                    title="<?php echo $TEXT['sort_by']; ?>"><?php echo $module->table->getColText('current_ip'); ?></a>
            </th>
            <th class="id">
                <a href="<?php echo $module->table->getColHref('profile_id'); ?>"
                    title="<?php echo $TEXT['sort_by']; ?>"><?php echo $module->table->getColText('profile_id'); ?></a>
            </th>
            <th class="id">sid</th>
            <th class="id">uid</th>
            <th class="large">
                <a href="<?php echo $module->table->getColHref('last_activity'); ?>"
                    title="<?php echo $TEXT['sort_by']; ?>"><?php echo $module->table->getColText('last_activity'); ?></a>
            </th>
        </tr>
    </thead>

    <tbody>
<?php foreach ($module->table->datas as $data): ?>

        <tr>
            <td class="<?php echo $data->className; ?>">
                <span><?php echo $data->className; ?></span>
            </td>
            <td>
                <span><?php echo htEnc($data->login); ?></span>
            </td>
            <td>
<?php if ($data->proxyed): ?>
                <img src="images/xchat/red_16.png" class="help"
                    title="<?php echo $TEXT['proxyed'].' (first IP: '.$data->proxy.')'; ?>" alt="" />
<?php endif; ?>
                <span><?php echo $data->ip; ?></span>
            </td>
            <td class="icon">
                <span><?php echo $data->pid; ?></span>
            </td>
            <td class="icon">
                <span><?php echo $data->sid; ?></span>
            </td>
            <td class="icon">
                <span><?php echo $data->uid; ?></span>
            </td>
            <td>
                <span><?php echo $data->lastActivity; ?></span>
            </td>
        </tr>
<?php endforeach; ?>
    </tbody>

    <tfoot>
        <tr class="pad">
            <th colspan="7">
                <span class="help" title="<?php echo $TEXT['ice_profile'];?>">(iid)</span>
                <span class="help" title="<?php echo $TEXT['sid'];?>">(sid)</span>
                <span class="help" title="<?php echo $TEXT['uid'];?>">(uid)</span>
                <span> #<mark>STATS</mark> : <?php printf(
                    $TEXT['sessions_infos'],
                    '<mark>'.$module->table->total.'</mark>',
                    '<mark>'.$module->table->auth.'</mark>',
                    '<mark>'.$module->table->unauth.'</mark>'
                ); ?></span>
            </th>
        </tr>
    </tfoot>

</table>

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
<?php if ($module->defaultSettingsButton): ?>
    <a href="?murmurInformations" class="button" title="<?php echo $TEXT['default_settings']; ?>">
        <img src="<?php echo IMG_INFO_22; ?>" alt="" />
    </a>
    <a href="?murmurMassSettings" class="button" title="<?php echo $TEXT['mass_settings']; ?>">
        <img src="images/tango/settings_22.png" alt="" />
    </a>
    <img src="<?php echo IMG_SPACE_16; ?>" alt="" />
<?php endif;
if ($module->addServerButton): ?>
    <a href="?addServer" class="button" title="<?php echo $TEXT['add_srv']; ?>" onClick="return popup('serverAdd');">
        <img src="<?php echo IMG_ADD_22; ?>" alt="" />
    </a>
<?php endif;
if ($module->sendMessageButton): ?>
    <a href="?messageToServers" class="button" title="<?php echo $TEXT['msg_all_srv']; ?>" onClick="return popup('serversMessage');">
        <img src="<?php echo IMG_MSG_22; ?>" alt="" />
    </a>
<?php endif; ?>
</div>

<?php require $PMA->widgets->getView('widget_tablePagingMenu'); ?>

<table id="overview">

    <tr class="pad">
        <th class="icon">
            <a href="<?php echo $module->table->getColHref('status'); ?>"
                title="<?php echo $TEXT['sort_by']; ?>"><?php echo $module->table->getColText('status'); ?></a>
        </th>
        <th class="id">
            <a href="<?php echo $module->table->getColHref('key'); ?>"
                title="<?php echo $TEXT['sort_by']; ?>"><?php echo $module->table->getColText('key'); ?></a>
        </th>
        <th><?php echo $TEXT['srv_name']; ?></th>
        <th class="icon"></th>
        <th class="icon"></th>
        <th class="small"></th>
        <th class="icon"></th>
        <th class="icon"></th>
    </tr>

<?php foreach ($module->table->datas as $d): ?>
    <tr>
        <td class="icon">
<?php if ($d->id !== ''): ?>
            <a href="?cmd=overview&amp;toggle_server_status=<?php echo $d->id; ?>" class="button <?php echo $d->status; ?>">
                <img src="<?php echo IMG_SPACE_16; ?>" class="" alt="" />
            </a>
<?php endif; ?>
        </td>
        <td class="id<?php HTML::selectedCss($d->selected); ?>">
            <span><?php echo $d->id; ?></span>
        </td>
        <td class="selection large">
<?php if ($d->id !== ''): ?>
            <a href="?page=vserver&amp;sid=<?php echo $d->id; ?>">
                <p class="text serverName"><?php echo $d->serverNameEnc; ?></p>
                <span class="info"><?php echo $d->host.':'.$d->port; ?></span>
<?php if ($d->uptime !== ''): ?>
                <time datetime="<?php echo $d->dt; ?>"
                    title="<?php printf($TEXT['started_at'], $d->date, $d->time); ?>">(<?php echo $d->uptime; ?>)*</time>
<?php endif; ?>
            </a>
<?php endif; ?>
        </td>
        <td class="icon">
<?php if ($d->id !== ''): ?>
            <a href="?resetServer=<?php echo $d->id; ?>" class="button"
                onClick="return popupResetSrv(this, '<?php echo $d->id; ?>', '<?php echo $d->serverNameEnc; ?>');">
                <img src="images/gei/hot_16.png" alt="" />
            </a>
<?php endif; ?>
        </td>
        <td class="icon">
<?php if ($d->connURL !== ''): ?>
            <a href="<?php echo $d->connURL; ?>" class="button">
                <img src="<?php echo IMG_CONN_16; ?>" alt="" />
            </a>
<?php endif; ?>
        </td>
        <td>
<?php if ($d->onlineUsers !== ''): ?>
            <meter min="0" low="<?php echo $d->low; ?>" optimum="1" high="<?php echo $d->high; ?>"
                max="<?php echo $d->max; ?>" value="<?php echo $d->users; ?>"></meter>
            <span><?php echo $d->onlineUsers; ?></span>
<?php endif; ?>
        </td>
        <td class="icon">
<?php if ($d->id !== ''): ?>
            <a href="?cmd=overview&amp;toggle_web_access=<?php echo $d->id; ?>" class="button">
                <img src="<?php echo $d->webAccessIMG; ?>" alt="" />
            </a>
<?php endif; ?>
        </td>
        <td class="icon">
<?php if ($d->delete): ?>
            <a href="?deleteServer=<?php echo $d->id; ?>" class="button"
                onClick="return popupDeleteSrv(this, '<?php echo $d->id; ?>', '<?php echo $d->serverNameEnc; ?>');">
                <img src="<?php echo IMG_TRASH_16; ?>" alt="" />
            </a>
<?php endif; ?>
        </td>
    </tr>
<?php endforeach; ?>

</table>

<?php
require $PMA->widgets->getView('widget_tablePagingMenu');

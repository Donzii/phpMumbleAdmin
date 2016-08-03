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
<?php require $PMA->widgets->getView('route_subTabs'); ?>
</div>

<form method="post" onSubmit="return isFormModified(this);">

    <input type="hidden" name="cmd" value="config" />
    <input type="hidden" name="set_settings_smtp" />

    <table class="config">

        <tr class="pad">
            <th class="title"></th>
            <td class="hide"></td>
        </tr>

        <tr>
            <th>
                <label for="host"><?php echo $TEXT['host']; ?></label>
            </th>
            <td>
                <input type="text" id="host" name="host" value="<?php $module->prt('host'); ?>" />
            </td>
        </tr>

        <tr>
            <th>
                <label for="port"><?php echo $TEXT['port']; ?></label>
            </th>
            <td>
                <input type="text" class="small" id="port" name="port" maxlength="5" value="<?php $module->prt('port'); ?>" />
            </td>
        </tr>

        <tr>
            <th>
                <label for="sender"><?php echo $TEXT['default_sender_email']; ?></label>
            </th>
            <td>
                <input type="email" id="sender" name="default_sender" value="<?php $module->prt('defaultSender'); ?>" />
            </td>
        </tr>

<?php if ($PMA->config->get('debug') > 0): ?>

        <tr class="pad">
            <td class="hide" colspan="2"></td>
        </tr>

        <tr>
            <th>
                <label for="debug">Debug email destination</label>
            </th>
            <td>
                <input type="text" id="debug" name="email" value="<?php $module->prt('debugEmailTo'); ?>" />
                <a href="?cmd=config&amp;send_debug_email">Send a debug email</a>
            </td>
        </tr>

<?php endif; ?>

        <tr>
            <th colspan="2">
                <input type="submit" value="<?php echo $TEXT['apply']; ?>" />
            </th>
        </tr>

    </table>

</form>

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
    <input type="hidden" name="set_settings_tables" />

    <table class="config">

        <tr class="pad">
            <th class="title"><?php echo $TEXT['tables']; ?></th>
            <td class="hide"></td>
        </tr>

        <tr>
            <th>
                <label for="overview"><?php echo $TEXT['overview_table_lines']; ?></label>
            </th>
            <td>
                <input type="text" class="small" maxlength="4" id="overview" name="overview" value="<?php $module->prt('overview'); ?>" />
                <span><?php echo $TEXT['tables_infos']; ?></span>
            </td>
        </tr>

        <tr>
            <th>
                <label for="users"><?php echo $TEXT['users_table_lines']; ?></label>
            </th>
            <td>
                <input type="text" class="small" maxlength="4" id="users" name="users" value="<?php $module->prt('users'); ?>" />
                <span><?php echo $TEXT['tables_infos']; ?></span>
            </td>
        </tr>

        <tr>
            <th>
                <label for="bans"><?php echo $TEXT['ban_table_lines']; ?></label>
            </th>
            <td>
                <input type="text" class="small" maxlength="4" id="bans" name="bans" value="<?php $module->prt('bans'); ?>" />
                <span><?php echo $TEXT['tables_infos']; ?></span>
            </td>
        </tr>

        <tr class="pad">
            <td class="hide" colspan="2"></td>
        </tr>

        <tr class="pad">
            <th class="title"><?php echo $TEXT['overview_table']; ?></th>
            <td class="hide"></td>
        </tr>

        <tr>
            <th>
                <label for="totalUsers"><?php echo $TEXT['enable_users_total']; ?></label>
            </th>
            <td>
                <input type="checkbox" <?php $module->chked('totalUsers'); ?> id="totalUsers" name="totalUsers" />
                <label for="totalUsersSa"><?php echo $TEXT['sa_only']; ?></label>
                <input type="checkbox" <?php $module->chked('totalUsersSa'); ?> id="totalUsersSa" name="totalUsersSa" />
            </td>
        </tr>

        <tr>
            <th>
                <label for="totalOnline"><?php echo $TEXT['enable_connected_users']; ?></label>
            </th>
            <td>
                <input type="checkbox" <?php $module->chked('totalOnline'); ?> id="totalOnline" name="totalOnline" />
                <label for="totalOnlineSa"><?php echo $TEXT['sa_only']; ?></label>
                <input type="checkbox" <?php $module->chked('totalOnlineSa'); ?> id="totalOnlineSa" name="totalOnlineSa" />
            </td>
        </tr>

        <tr>
            <th>
                <label for="uptime"><?php echo $TEXT['enable_vserver_uptime']; ?></label>
            </th>
            <td>
                <input type="checkbox" <?php $module->chked('uptime'); ?> id="uptime" name="uptime" />
                <label for="uptimeSa"><?php echo $TEXT['sa_only']; ?></label>
                <input type="checkbox" <?php $module->chked('uptimeSa'); ?> id="uptimeSa" name="uptimeSa" />
            </td>
        </tr>

        <tr>
            <th colspan="2">
                <input type="submit" value="<?php echo $TEXT['apply']; ?>" />
            </th>
        </tr>

    </table>

</form>

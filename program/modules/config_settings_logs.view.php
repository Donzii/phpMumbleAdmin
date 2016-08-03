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
    <input type="hidden" name="set_settings_logs" />

    <table class="config">

        <tr class="pad">
            <th class="title"><?php echo $TEXT['vservers_logs']; ?></th>
            <td class="hide"></td>
        </tr>

        <tr>
            <th><?php echo $TEXT['srv_logs_amount']; ?></th>
            <td>
                <select name="murmur_logs_size">
                    <option value="-1"><?php echo $TEXT['all']; ?></option>
<?php foreach ($module->logOptions as $o): ?>
                    <option <?php HTML::selected($o->select); ?> value="<?php echo $o->val; ?>"><?php echo $o->format; ?></option>
<?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
            <th>
                <label for="activate_admins"><?php echo $TEXT['activate_vservers_logs_for_adm']; ?></label>
            </th>
            <td>
                <input type="checkbox" <?php $module->chked('vlogsAdmins'); ?> id="activate_admins" name="activate_admins" />
            </td>
        </tr>

        <tr>
            <th>
                <label for="hightlights"><?php echo $TEXT['activate_adm_highlight_logs']; ?></label>
            </th>
            <td>
                <input type="checkbox" <?php $module->chked('vlogsAdminsHighlights'); ?> id="hightlights" name="adm_hightlights_logs" />
            </td>
        </tr>

<?php if ($PMA->user->is(PMA_USER_SUPERADMIN)): ?>

        <tr class="pad">
            <td class="hide" colspan="2"></td>
        </tr>

        <tr class="pad">
            <th class="title"><?php echo $TEXT['pma_logs']; ?></th>
            <td>
                <cite><?php echo $TEXT['pma_logs_infos']; ?></cite>
            </td>
        </tr>

        <tr>
            <th>
                <label for="keep"><?php echo $TEXT['pma_logs_clean']; ?></label>
            </th>
            <td>
                <input type="text" class="small" maxlength="5" id="keep" name="log_keep" value="<?php $module->prt('pmaLogsKeep'); ?>" />
                <span><?php echo $TEXT['disable_function']; ?></span>
            </td>
        </tr>

        <tr>
            <th>
                <label for="log_SA"><?php echo $TEXT['logs_sa_actions']; ?></label>
            </th>
            <td>
                <input type="checkbox" <?php $module->chked('pmaLogsSaActions'); ?> id="log_SA" name="log_SA" />
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

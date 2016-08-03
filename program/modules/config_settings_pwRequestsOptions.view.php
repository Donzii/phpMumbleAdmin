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
    <div class="right">
        <a href="./" class="button" title="<?php echo $TEXT['cancel']; ?>">
            <img src="<?php echo IMG_CANCEL_22; ?>" alt="" />
        </a>
    </div>
<?php require $PMA->widgets->getView('route_subTabs'); ?>
</div>

<form method="post" onSubmit="return isFormModified(this);">

    <input type="hidden" name="cmd" value="config" />
    <input type="hidden" name="set_pw_requests_options" />

    <table class="config">

        <tr>
            <th>
                <label for="explicit"><?php echo $TEXT['activate_explicite_msg']; ?></label>
            </th>
            <td>
                <input type="checkbox" <?php $module->chked('explicitMsg'); ?> id="explicit" name="explicit_msg" />
            </td>
        </tr>

        <tr>
            <th>
                <label for="senderEmail"><?php echo $TEXT['sender_email']; ?></label>
            </th>
            <td>
                <input type="email" id="senderEmail" name="sender_email" value="<?php $module->prt('senderEmail'); ?>" />
            </td>
        </tr>

        <tr>
            <th>
                <label for="pending"><?php echo $TEXT['pwgen_max_pending']; ?></label>
            </th>
            <td>
                <input type="number" maxlength="3" min="1" max="744" id="pending" name="pending_delay"
                    value="<?php $module->prt('pending'); ?>" />
            </td>
        </tr>

        <tr>
            <th colspan="2">
                <input type="submit" value="<?php echo $TEXT['apply']; ?>" />
            </th>
        </tr>

    </table>
</form>

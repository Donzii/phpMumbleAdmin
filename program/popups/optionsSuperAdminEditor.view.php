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

if (! defined('PMA_STARTED')) { die('ILLEGAL: You cannot call this script directly !'); }

$widget = $PMA->widgets->getDatas('optionsSuperAdminEditor'); ?>

<form id="optionsSuperAdminEditor" method="POST" class="actionBox medium" onSubmit="return validateSuperAdmin(this);">

    <input type="hidden" name="cmd" value="config_admins" />
    <input type="hidden" name="edit_SuperAdmin" value="" />

    <h3>
        <span>SuperAdmin editor</span>
    </h3>

<?php require 'buttonCancel.inc'; ?>

    <div class="body">
        <table class="config">

            <tr>
                <th>
                    <label for="current"><?php echo $TEXT['enter_your_pw']; ?></label>
                </th>
                <td>
                    <input type="password" autofocus="autofocus" required="required" id="current" name="current" value="" />
                </td>
            </tr>

            <tr class="pad">
                <td class="hide" colspan="2"></td>
            </tr>

            <tr>
                <th>
                    <label for="login"><?php echo $TEXT['sa_login']; ?></label>
                </th>
                <td>
                    <input type="text" required="required" id="login" name="login" value="<?php $widget->prt('saLogin'); ?>" />
                </td>
            </tr>

            <tr class="pad">
                <td class="hide" colspan="2"></td>
            </tr>

            <tr>
                <th>
                    <label for="new_pw"><?php echo $TEXT['new_pw']; ?></label>
                </th>
                <td>
                    <input type="password" id="new_pw" name="new_pw" value="" />
                </td>
            </tr>
            <tr>
                <th>
                    <label for="confirm_new_pw"><?php echo $TEXT['confirm_pw']; ?></label>
                </th>
                <td>
                    <input type="password" id="confirm_new_pw" name="confirm_new_pw" value="" />
                </td>
            </tr>
            <tr>
                <th class="mid" colspan="2">
                    <input type="submit" value="<?php echo $TEXT['modify']; ?>" />
                </th>
            </tr>

        </table>
    </div>

</form>

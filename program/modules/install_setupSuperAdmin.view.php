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

<form class="actionBox medium" method="POST" onSubmit="return validatePw(this);">

    <input type="hidden" name="cmd" value="install" />
    <input type="hidden" name="setup_SuperAdmin" />

    <table class="config">

        <tr>
            <th class="title"><?php echo $TEXT['setup_sa']; ?></th>
        </tr>

        <tr>
            <th>
                <label for="login"><?php echo $TEXT['sa_login']; ?></label>
            </th>
            <td>
                <input type="text" autofocus="autofocus" required="required" id="login" name="login" value="<?php $module->prt('saLogin'); ?>" />
            </td>
        </tr>

        <tr class="pad">
            <td class="hide" colspan="2"></td>
        </tr>

        <tr>
            <th>
                <label for="pw"><?php echo $TEXT['new_pw']; ?></label>
            </th>
            <td>
                <input type="password" required="required" id="pw" name="new_pw" />
            </td>
        </tr>

        <tr>
            <th>
                <label for="confirm_pw"><?php echo $TEXT['confirm_pw']; ?></label>
            </th>
            <td>
                <input type="password" required="required" id="confirm_pw" name="confirm_new_pw" />
            </td>
        </tr>

        <tr>
            <th colspan="2">
                <input type="submit" />
            </th>
        </tr>

    </table>

</form>

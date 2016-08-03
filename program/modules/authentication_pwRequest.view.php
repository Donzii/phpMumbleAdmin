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

<form method="post" class="actionBox auth">

    <input type="hidden" name="cmd" value="pw_requests" />

    <fieldset>

        <legend><?php echo $TEXT['gen_pw']; ?></legend>

        <table class="config pad">

            <tr>
                <th>
                    <label for="login"><?php echo $TEXT['login']; ?></label>
                </th>
                <td>
                    <input type="text" autofocus="autofocus" required="required" id="login" name="login" />
                </td>
            </tr>

            <tr>
                <th>
                    <label for="server_id"><?php echo $TEXT['server']; ?></label>
                </th>
                <td>
<?php require 'authentication_serverField.view.inc'; ?>
                </td>
            </tr>

            <tr>
                <th colspan="2">
                    <input type="submit" value="<?php echo $TEXT['submit']; ?>" />
                </th>
            </tr>

        </table>

    </fieldset>

    <div class="passwordRequest">
        <a href="./"><?php echo $TEXT['cancel']; ?></a>
    </div>

</form>

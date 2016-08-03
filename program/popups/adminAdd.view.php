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

$widget = $PMA->widgets->getDatas('adminAdd'); ?>

<form id="adminAdd" method="POST" class="actionBox medium" onSubmit="return validatePw(this);">

    <input type="hidden" name="cmd" value="config_admins" />
    <input type="hidden" name="add_new_admin" value="" />

    <h3>
        <span><?php echo $TEXT['add_admin']; ?></span>
    </h3>

<?php require 'buttonCancel.inc'; ?>

    <fieldset>

        <div class="body">

            <table class="config">

                <tr>
                    <th>
                        <label for="login"><?php echo $TEXT['login']; ?>*</label>
                    </th>
                    <td>
                        <input type="text" id="login" name="login" autofocus="autofocus" required="required" value="" />
                    </td>
                </tr>
                <tr class="pad">
                    <td class="hide" colspan="2"></td>
                </tr>
                <tr>
                    <th>
                        <label for="new_pw"><?php echo $TEXT['new_pw']; ?>*</label>
                    </th>
                    <td>
                        <input type="password" id="new_pw" name="new_pw" required="required" value="" />
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="confirm_new_pw"><?php echo $TEXT['confirm_pw']; ?>*</label>
                    </th>
                    <td>
                        <input type="password" id="confirm_new_pw" name="confirm_new_pw" required="required" value="" />
                    </td>
                </tr>
                <tr class="pad">
                    <td class="hide" colspan="2"></td>
                </tr>
                <tr>
                    <th><?php echo $TEXT['class']; ?></th>
                    <td>
                        <select name="class">
<?php foreach ($widget->classes as $o): ?>
                            <option value="<?php echo $o->class; ?>" <?php HTML::selected($o->select); ?>><?php echo $o->className; ?></option>
<?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr class="pad">
                    <td class="hide" colspan="2"></td>
                </tr>
                <tr>
                    <th>
                        <label for="email"><?php echo $TEXT['email_addr']; ?></label>
                    </th>
                    <td>
                        <input type="email" id="email" name="email" value="" />
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="name"><?php echo $TEXT['user_name']; ?></label>
                    </th>
                    <td>
                        <input type="text" id="name" name="name" value="" />
                    </td>
                </tr>
                <tr>
                    <th class="mid" colspan="2">
                        <input type="submit" value="<?php echo $TEXT['add']; ?>" />
                    </th>
                </tr>

            </table>

        </div>

    </fieldset>

</form>

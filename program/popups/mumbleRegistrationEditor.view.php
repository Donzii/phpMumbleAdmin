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

$widget = $PMA->widgets->getDatas('mumbleRegistrationEditor'); ?>

<form id="mumbleRegistrationEditor" method="POST" class="actionBox medium" onSubmit="return validateMumbleRegistrationEditor(this)">

    <input type="hidden" name="cmd" value="murmur_registrations" />
    <input type="hidden" name="editRegistration" />

    <h3>
        <span><?php echo $TEXT['edit_account']; ?></span>
    </h3>

<?php require 'buttonCancel.inc'; ?>

    <div class="body">

        <table class="config">

<?php if ($widget->is_set('login')): ?>
                <tr>
                    <th>
                        <label for="login"><?php echo $TEXT['login']; ?></label>
                    </th>
                    <td>
                        <input type="text" required="required" id="login" name="login" value="<?php $widget->prt('login'); ?>" />
                    </td>
                </tr>
<?php endif; ?>

<?php if ($widget->is_set('pw')): ?>

            <tr class="pad">
                <td colspan="2" class="hide">
                </td>
            </tr>

<?php if ($widget->ownAccount): ?>
            <tr>
                <th>
                    <label for="current"><?php echo $TEXT['enter_your_pw']; ?></label>
                </th>
                <td>
                    <input type="password" id="current" name="current" value="" />
                </td>
            </tr>
<?php endif; ?>

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
<?php endif; ?>

            <tr class="pad">
                <td colspan="2" class="hide">
                </td>
            </tr>

            <tr>
                <th>
                    <label for="email"><?php echo $TEXT['email_addr']; ?></label>
<?php if ($widget->certificat !== ''): ?>
                        <span class="tooltip">
                            <img src="<?php echo IMG_INFO_16; ?>" alt="" />
                            <span class="desc"><?php echo $TEXT['cert_email_info']; ?></span>
                        </span>
<?php endif; ?>
                </th>
                <td>
                    <input type="email" id="email" name="email" value="<?php $widget->prt('email'); ?>" />
                </td>
            </tr>

            <tr>
                <th>
                    <label for="comm"><?php echo $TEXT['comment']; ?></label>
                </th>
                <td>
                    <textarea id="comm" name="comm" rows="10" cols="4"><?php $widget->prt('description'); ?></textarea>
                </td>
            </tr>

        </table>

    </div>

    <div class="submit">
        <input type="submit" value="<?php echo $TEXT['modify']; ?>" />
    </div>

</form>

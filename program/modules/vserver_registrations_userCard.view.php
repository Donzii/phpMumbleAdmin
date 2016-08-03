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
<?php if ($PMA->user->isMinimum(PMA_USER_SUPERUSER_RU)): ?>
    <a href="?mumbleRegistration=unset" class="button right" title="<?php echo $TEXT['cancel']; ?>">
        <img src="<?php echo IMG_CANCEL_22; ?>" alt="" />
    </a>
<?php endif; ?>
</div>

<div id="mumbleRegistration">

    <div class="card">

        <ul class="menu">
            <li>
                <a href="?editMumbleRegistration" onClick="return popup('mumbleRegistrationEditor');"><?php echo $TEXT['edit_account']; ?></a>
            </li>
<?php if ($module->deleteAccount): ?>
            <li>
                <a href="?delete_account" onClick="return popup('mumbleRegistrationDelete');"><?php echo $TEXT['delete_acc']; ?></a>
            </li>
<?php endif;
if ($module->deleteAvatar): ?>
            <li>
                <a href="?remove_avatar" onClick="return popup('mumbleRegistrationDeleteAvatar');"><?php echo $TEXT['delete_avatar']; ?></a>
            </li>
<?php endif; ?>
        </ul>

        <div class="userInfos">
            <table class="config">
                <tr>
                    <th><?php echo $TEXT['login']; ?></th>
                    <td class="hide"></td>
                </tr>
                <tr>
                    <td colspan="2">
<?php if ($module->statusLink): ?>
                        <a href="?tab=channels&amp;userSession=<?php $module->prt('statusUrl'); ?>" class="button on"
                            title="<?php echo $TEXT['user_is_online']; ?>">
                            <img src="<?php echo IMG_SPACE_16; ?>" alt="" />
                        </a>
<?php else: ?>
                        <img src="<?php echo IMG_SPACE_16; ?>" class="button <?php echo $module->statusCss; ?>" title="<?php echo $module->statusText; ?>" alt="" />
<?php endif; ?>
                        <span class="login"><?php $module->prt('login'); ?></span>
                    </td>
                </tr>

                <tr>
                    <th>
                        <span><?php echo $TEXT['email_addr']; ?></span>
<?php if ($module->certificat !== ''): ?>
                        <span class="tooltip">
                            <img src="<?php echo IMG_INFO_16; ?>" alt="" />
                            <span class="desc"><?php echo $TEXT['cert_email_info']; ?></span>
                        </span>
<?php endif; ?>
                    </th>
                    <td class="hide"></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <a href="mailto:<?php $module->prt('email'); ?>" title="mailto:<?php $module->prt('email'); ?>">
                            <span><?php $module->prt('email'); ?></span>
                        </a>
                    </td>
                </tr>

<?php if ($module->is_set('lastActivity')): ?>
                <tr>
                    <th><?php echo $TEXT['last_activity']; ?></th>
                    <td class="hide"></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <span class="help" title="<?php $module->prt('lastActivityTitle'); ?>"><?php $module->prt('lastActivity'); ?></span>
                    </td>
                </tr>
<?php endif; ?>

<?php if ($module->showCertificatHash): ?>
                <tr>
                    <th><?php echo $TEXT['cert_hash']; ?></th>
                    <td class="hide"></td>
                </tr>
                <tr>
                    <td colspan="2"><?php echo $module->certificat; ?></td>
                </tr>
<?php endif; ?>

                <tr>
                    <th><?php echo $TEXT['comment']; ?></th>
                    <td class="hide"></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <iframe src="<?php echo PMA_FILE_SANDBOX_RELATIVE; ?>" sandbox="">
                            <p>Your browser does not support iframes.</p>
                        </iframe>
                    </td>
                </tr>
            </table>
        </div>

<?php if ($module->showAvatar): ?>
            <div class="avatar">
<?php if ($module->avatar->isEmpty()): ?>
                <div class="text"><?php echo $TEXT['no_avatar']; ?></div>
<?php else: ?>
                <img src="<?php echo $module->avatar->getSRC(); ?>" alt="" />
<?php endif; ?>
            </div>
<?php endif; ?>

        <div class="clear"></div>

    </div>
</div>

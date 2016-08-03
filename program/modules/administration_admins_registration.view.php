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
        <a href="?adminRegistration=unset" class="button" title="<?php echo $TEXT['back']; ?>">
            <img src="<?php echo IMG_CANCEL_22; ?>" alt="" />
        </a>
    </div>
    <a href="?adminRegistrationEditor" class="button" title="<?php echo $TEXT['edit_account']; ?>"
        onClick="return popup('adminsRegistrationEditor');">
        <img src="<?php echo IMG_EDIT_22; ?>" alt="" />
    </a>
</div>

<div id="admin">

    <aside class="card">
        <p>
            <span class="key"><?php echo $TEXT['login']; ?>:</span>
            <span class="value"><?php $module->prt('admLogin'); ?></span>
        </p>
        <p>
            <span class="key"><?php echo $TEXT['class']; ?>:</span>
            <mark class="<?php $module->prt('admClassName'); ?>"> <?php $module->prt('admClassName'); ?> </mark>
        </p>
        <p>
            <span class="key">ID:</span>
            <span class="value"><?php $module->prt('admID'); ?></span>
        </p>
        <p>
            <span class="key"><?php echo $TEXT['registered_date']; ?>:</span>
            <span class="value help" title="<?php $module->prt('admCreatedUptime'); ?>"><?php $module->prt('admCreatedDate'); ?></span>
        </p>
        <p>
            <span class="key"><?php echo $TEXT['last_conn']; ?>:</span>
<?php if (isset($module->lastConn)): ?>
            <span class="value help" title="<?php $module->prt('lastConnUptime'); ?>"><?php $module->prt('lastConnDate'); ?></span>
<?php endif; ?>
        </p>
        <p>
            <span class="key"><?php echo $TEXT['email_addr']; ?>:</span>
<?php if (isset($module->email)): ?>
                <a href="mailto:<?php $module->prt('email'); ?>" title="mailto:<?php $module->prt('email'); ?>">
                    <span class="value"><?php $module->prt('email'); ?></span>
                </a>
<?php endif; ?>
        </p>
        <p>
            <span class="key"><?php echo $TEXT['user_name']; ?>:</span>
            <span class="value"><?php $module->prt('admName'); ?></span>
        </p>

        <h4 class="key"><?php echo $TEXT['profile_access']; ?>:</h4>
        <ul>
<?php foreach ($module->profilesAccess as $d): ?>
            <li>
<?php if ($d->selected): ?>
                <mark class="value access"><img src="images/xchat/red_8.png" alt="" /><?php echo $d->textEnc; ?></mark>
<?php else: ?>
                <span class="value access"><img src="images/xchat/blue_8.png" alt="" /><?php echo $d->textEnc; ?></span>
<?php endif; ?>
            </li>
<?php endforeach; ?>
        </ul>

    </aside>

<?php if (isset($module->showServersScroll)): ?>

    <form id="adminsServersAccess" method="post" onSubmit="return isFormModified(this);">

        <input type="hidden" name="cmd" value="config_admins" />
        <input type="hidden" name="editAccess" value="<?php $module->prt('admID'); ?>" />

        <div class="buttons">
            <input type="reset" value="<?php echo $TEXT['reset']; ?>" />
            <script type="text/javascript">
                document.write(
                '<input type="button" onClick="uncheck(\'fullAccess\'); checkAllBox(\'serversScroll\');" value="<?php echo $TEXT['all']; ?>" />'+
                '<input type="button" onClick="uncheck(\'fullAccess\'); uncheckAllBox(\'serversScroll\');" value="<?php echo $TEXT['none']; ?>" />'+
                '<input type="button" onClick="uncheck(\'fullAccess\'); invertAllChkBox(\'serversScroll\');" value="<?php echo $TEXT['invert']; ?>" />'
                );
            </script>
            <input type="submit" class="apply" value="<?php echo $TEXT['apply']; ?>" />
        </div>

        <div class="fullAccess">
            <input type="checkbox" class="chkbox" <?php $module->chked('hasFullAccess'); ?>
                id="fullAccess" name="fullAccess" onClick="AdminFullAccessToggle(this);" />
            <label for="fullAccess"><?php echo $TEXT['enable_full_access']; ?></label>
        </div>

        <ul id="serversScroll" class="scroll">
<?php if (empty($module->serversScroll)): ?>
            <li>Error: Failed to get the servers list.</li>
<?php endif;
foreach ($module->serversScroll as $d): ?>
            <li>
                <input type="checkbox" class="chkbox" id="<?php echo $d->label; ?>" name="<?php echo $d->id; ?>"
                    onClick="uncheck('fullAccess');" <?php HTML::chked($d->chked); ?> />
                <label for="<?php echo $d->label; ?>"><?php echo $d->id; ?># <?php echo htEnc($d->name); ?></label>
            </li>
<?php endforeach; ?>
        </ul>

    </form>
<?php endif; ?>

    <div class="clear"></div>
</div>

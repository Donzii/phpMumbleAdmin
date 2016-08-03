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
<?php if ($module->editDefaultOptions): ?>
    <a href="?set_default_options" class="right"><?php echo $TEXT['default_options']; ?></a>
<?php endif;
if ($module->editSuperAdmin): ?>
    <a href="?edit_SuperAdmin" class="button" title="<?php echo $TEXT['change_your_pw']; ?>" onClick="return popup('optionsSuperAdminEditor');">
        <img src="<?php echo IMG_KEY_22; ?>" alt="" />
    </a>
<?php elseif ($module->editAdminPassword): ?>
    <a href="?change_your_password" class="button" title="<?php echo $TEXT['change_your_pw']; ?>" onClick="return popup('optionsPasswordEditor');">
        <img src="<?php echo IMG_KEY_22; ?>" alt="" />
    </a>
<?php endif; ?>
</div>

<form method="post" onSubmit="return isFormModified(this);">

    <input type="hidden" name="cmd" value="config" />
    <input type="hidden" name="set_options" />

    <table class="config">

        <tr>
            <th><?php echo $TEXT['select_lang']; ?></th>
            <td>
                <select name="lang">
<?php foreach ($module->langs as $a): ?>
                    <option <?php HTML::selected($a['select']); ?> value="<?php echo $a['dir']; ?>"><?php echo $a['name']; ?></option>
<?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
            <th><?php echo $TEXT['select_style']; ?></th>
            <td>
                <select name="skin">
<?php foreach ($module->skins as $a): ?>
                    <option <?php HTML::selected($a['select']); ?> value="<?php echo $a['file']; ?>"><?php echo $a['name']; ?></option>
<?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
            <th><?php echo $TEXT['select_time']; ?></th>
            <td>
                <select name="timezone">
<?php foreach ($module->timezones as $zones): ?>
                    <option disabled="disabled">---</option>
<?php foreach ($zones as $z): ?>
                    <option <?php HTML::selected($z->select); ?> value="<?php echo $z->tz; ?>"><?php echo $z->city; ?></option>
<?php endforeach;
endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
            <th><?php echo $TEXT['time_format']; ?></th>
            <td>
                <select name="time">
<?php foreach ($module->timeFormats as $a): ?>
                <option <?php HTML::selected($a['select']); ?> value="<?php echo $a['option']; ?>"><?php echo $a['desc']; ?></option>
<?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
            <th><?php echo $TEXT['date_format']; ?></th>
            <td>
                <select name="date">
<?php foreach ($module->dateFormats as $a): ?>
                <option <?php HTML::selected($a['select']); ?> value="<?php echo $a['option']; ?>"><?php echo $a['desc']; ?></option>
<?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
            <th><?php echo $TEXT['select_locales_profile']; ?></th>
            <td>
                <select name="locales">
                    <option value=""><?php echo $TEXT['none']; ?></option>
<?php foreach ($module->systemLocalesProfiles as $o): ?>
            <option <?php HTML::selected($o->select); ?> value="<?php echo $o->key; ?>"><?php echo $o->val; ?></option>
<?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
            <th><?php echo $TEXT['uptime_format']; ?></th>
            <td>
                <select name="uptime">
<?php foreach ($module->uptimeOptions as $o): ?>
            <option <?php HTML::selected($o->select); ?> value="<?php echo $o->val; ?>"><?php echo $o->uptime; ?></option>
<?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr class="pad">
            <td class="hide" colspan="2"></td>
        </tr>

        <tr>
            <th>
                <label for="vserver_login">
                    <span><?php echo $TEXT['conn_login']; ?></span>
                    <span class="tooltip">
                        <img src="<?php echo IMG_INFO_16; ?>" alt="" />
                        <span class="desc"><?php echo $TEXT['conn_login_info']; ?></span>
                    </span>
                </label>
            </th>
            <td>
                <input type="text" id="vserver_login" name="vserver_login" value="<?php $module->prt('vserversLogin'); ?>" />
            </td>
        </tr>

        <tr>
            <th colspan="2">
                <input type="submit" value="<?php echo $TEXT['apply']; ?>" />
            </th>
        </tr>

    </table>
</form>

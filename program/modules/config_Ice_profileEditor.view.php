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
    <a href="?add_profile" class="button" title="<?php echo $TEXT['add_ICE_profile']; ?>" onClick="return popup('profileAdd');">
        <img src="<?php echo IMG_ADD_22; ?>" alt="" />
    </a>
<?php if ($module->addDefaultButton): ?>
    <a href="?cmd=config_ICE&amp;set_default_profile" class="button" title="<?php echo $TEXT['default_ICE_profile']; ?>">
        <img src="images/tango/fav_22.png" alt="" />
    </a>
<?php endif; ?>
</div>

<form method="post" onSubmit="return isFormModified(this);">

    <input type="hidden" name="cmd" value="config_ICE" />
    <input type="hidden" name="edit_profile" />

    <table class="config">

        <tr class="pad">
            <th class="title"></th>
            <td class="hide"></td>
        </tr>

        <tr>
            <th>
                <label for="name"><?php echo $TEXT['profile_name']; ?></label>
            </th>
            <td>
<?php if ($module->addDeleteProfileButton): ?>
                <a href="?delete_profile" class="button right" title="<?php echo $TEXT['del_profile']; ?>" onClick="return popup('profileDelete');">
                    <img src="<?php echo IMG_TRASH_16; ?>" alt="" />
                </a>
<?php endif; ?>
            <input type="text" required="required" id="name" name="name" value="<?php $module->prt('profileName'); ?>" />
            </td>
        </tr>

        <tr>
            <th>
                <label for="public"><?php echo $TEXT['public_profile']; ?></label>
            </th>
            <td>
                <input type="checkbox" <?php $module->chked('isPublic'); ?> id="public" name="public" />
            </td>
        </tr>

        <tr>
            <th>
                <label for="host"><?php echo $TEXT['ICE_host']; ?></label>
            </th>
            <td>
                <input type="text" required="required" id="host" name="host" value="<?php $module->prt('host'); ?>" />
            </td>
        </tr>

        <tr>
            <th>
                <label for="port"><?php echo $TEXT['ICE_port']; ?></label>
            </th>
            <td>
                <input type="number" min="0" max="65535" id="port" name="port" value="<?php $module->prt('port'); ?>" />
            </td>
        </tr>

        <tr>
            <th>
                <label for="timeout"><?php echo $TEXT['ICE_timeout']; ?></label>
            </th>
            <td>
                <input type="number" min="1" max="99" id="timeout" name="timeout" value="<?php $module->prt('timeout'); ?>" />
            </td>
        </tr>

        <tr>
            <th>
                <label for="secret"><?php echo $TEXT['ICE_secret']; ?></label>
            </th>
            <td>
                <input type="text" id="secret" name="secret" value="<?php $module->prt('secret'); ?>" />
            </td>
        </tr>

<?php if (isset($module->slicesIceProfiles)): ?>
        <tr>
            <th><?php echo $TEXT['slice_profile']; ?></th>
            <td>
                <select name="slice_profile">
                    <option value=""><?php echo $TEXT['none']; ?></option>
<?php foreach ($module->slicesIceProfiles as $o): ?>
                    <option <?php HTML::selected($o->select); ?> value="<?php echo $o->name; ?>"><?php echo $o->name; ?></option>
<?php endforeach; ?>
                </select>
            </td>
        </tr>
<?php elseif (isset($module->slicesPhpProfiles)): ?>
        <tr>
            <th><?php echo $TEXT['slice_php_file']; ?></th>
            <td>
                <select name="slice_php">
                    <option value=""><?php echo $TEXT['none']; ?></option>
<?php foreach ($module->slicesPhpProfiles as $o): ?>
                    <option <?php HTML::selected($o->select); ?> value="<?php echo $o->filename; ?>"><?php echo $o->name; ?></option>
<?php endforeach; ?>
                </select>
            </td>
        </tr>
<?php endif; ?>

        <tr>
            <th>
                <label for="http_addr">
                    <span><?php echo $TEXT['conn_url']; ?></span>
                    <span class="tooltip">
                        <img src="<?php echo IMG_INFO_16; ?>" alt="" />
                        <span class="desc"><?php echo $TEXT['conn_url_info']; ?></span>
                    </span>
                </label>
            </th>
            <td>
                <input type="text" id="http_addr" name="http_addr" value="<?php $module->prt('httpAddr'); ?>" />
            </td>
        </tr>

        <tr>
            <th colspan="2">
                <input type="submit" value="<?php echo $TEXT['apply']; ?>" />
            </th>
        </tr>

    </table>

</form>

<br />

<div class="information">
<?php foreach ($module->IceInfos as $array): ?>
    <p>
        <strong><?php echo htEnc($array[0]); ?>:</strong>
        <cite><?php echo htEnc($array[1]); ?></cite>
    </p>
<?php endforeach; ?>
</div>

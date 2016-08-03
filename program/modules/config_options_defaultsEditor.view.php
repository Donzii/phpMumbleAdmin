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
</div>

<form method="post" onSubmit="return isFormModified(this);">

    <input type="hidden" name="cmd" value="config" />
    <input type="hidden" name="set_default_options" />

    <table class="config">

        <tr class="pad">
            <th class="title" ><?php echo $TEXT['default_options']; ?></th>
            <td></td>
        </tr>

        <tr>
            <th><?php echo $TEXT['default_lang']; ?></th>
            <td>
                <select name="lang">
<?php foreach ($module->langs as $a): ?>
                    <option <?php HTML::selected($a['select']); ?> value="<?php echo $a['dir']; ?>"><?php echo $a['name']; ?></option>
<?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
            <th><?php echo $TEXT['default_style']; ?></th>
            <td>
                <select name="skin">
<?php foreach ($module->skins as $a): ?>
                    <option <?php HTML::selected($a['select']); ?> value="<?php echo $a['file']; ?>"><?php echo $a['name']; ?></option>
<?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
            <th><?php echo $TEXT['default_time']; ?></th>
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
            <th><?php echo $TEXT['default_time_format']; ?></th>
            <td>
                <select name="time">
<?php foreach ($module->timeFormats as $a): ?>
                <option <?php HTML::selected($a['select']); ?> value="<?php echo $a['option']; ?>"><?php echo $a['desc']; ?></option>
<?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
            <th><?php echo $TEXT['default_date_format']; ?></th>
            <td>
                <select name="date">
<?php foreach ($module->dateFormats as $a): ?>
                <option <?php HTML::selected($a['select']); ?> value="<?php echo $a['option']; ?>"><?php echo $a['desc']; ?></option>
<?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
            <th><?php echo $TEXT['default_locales']; ?></th>
            <td>
                <select name="systemLocales">
                    <option value=""><?php echo $TEXT['default']; ?></option>
<?php foreach ($module->systemLocales as $a): ?>
                    <option <?php HTML::selected($a['select']); ?> value="<?php echo $a['locale']; ?>"><?php echo $a['desc']; ?></option>
<?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
            <th colspan="2">
                <input type="submit" value="<?php echo $TEXT['apply']; ?>" />
            </th>
        </tr>

    </table>
</form>

<form method="post">

    <input type="hidden" name="cmd" value="config" />
    <input type="hidden" name="add_locales_profile" />

    <table class="config">

        <tr class="pad">
            <th class="title"><?php echo $TEXT['add_locales_profile']; ?></th>
            <td></td>
        </tr>

        <tr>
            <th>Select a system locales</th>
            <td>
                <select name="key" required="required">
                    <option value=""><?php echo $TEXT['none']; ?></option>
<?php foreach ($module->availableSystemLocales as $locale): ?>
                    <option value="<?php echo $locale; ?>"><?php echo $locale; ?></option>
<?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
            <th>Name of the profile</th>
            <td>
                <input type="text" required="required" name="val" />
            </td>
        </tr>

        <tr>
            <th colspan="2">
                <input type="submit" value="<?php echo $TEXT['add']; ?>" />
            </th>
        </tr>

    </table>

</form>

<form method="post">

    <input type="hidden" name="cmd" value="config" />

    <table class="config">

        <tr class="pad">
            <th class="title"><?php echo $TEXT['del_locales_profile']; ?></th>
            <td></td>
        </tr>

        <tr>
            <th></th>
            <td>
                <select name="delete_locales_profile" required="required">
                    <option value=""><?php echo $TEXT['none']; ?></option>
<?php foreach ($module->systemLocalesProfiles as $key => $value): ?>
                    <option value="<?php echo $key; ?>"><?php echo $value.' ('.$key.')'; ?></option>
<?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
            <th colspan="2">
                <input type="submit" value="<?php echo $TEXT['delete']; ?>" />
            </th>
        </tr>

    </table>

</form>

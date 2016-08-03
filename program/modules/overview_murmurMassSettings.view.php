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

<form method="POST">

    <input type="hidden" name="cmd" value="overview" />
    <input type="hidden" name="mass_settings" />
    <input type="hidden" name="confirm_word" value="<?php echo $TEXT['confirm_word']; ?>" />

    <table class="config">

        <tr class="pad">
            <th class="title"><?php echo $TEXT['mass_settings']; ?></th>
            <td></td>
        </tr>

        <tr>
            <th>
                <select name="key" required="required">
                    <option value=""><?php echo $TEXT['select_setting']; ?></option>
<?php foreach ($module->settings as $key => $array): ?>
                    <option value="<?php echo $key; ?>"><?php echo $array['name']; ?></option>
<?php endforeach; ?>
                </select>
            </th>
            <td>
                <textarea name="value" rows="6" cols="6"></textarea>
            </td>
        </tr>

        <tr>
            <th>
               <span><?php printf($TEXT['confirm_with_word'], $TEXT['confirm_word']); ?></span>
            </th>
            <td>
                <input type="text" required="required" pattern="<?php echo $TEXT['confirm_word']; ?>" name="confirm" />
            </td>
        </tr>

        <tr>
            <th colspan="2">
                <input type="submit" />
            </th>
        </tr>

    </table>

</form>

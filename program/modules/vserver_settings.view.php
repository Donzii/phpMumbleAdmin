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

<form method="post" onSubmit="return validateSrvSettings(this);">

    <input type="hidden" name="cmd" value="murmur_settings" />
    <input type="hidden" name="setConf" />

    <table id="vserverSettings">

        <tr class="invisible">
            <th class="headKey"></th>
            <th></th>
            <th class="headInput"></th>
            <th class="icon"></th>
        </tr>

<?php foreach ($module->settingsDatas as $d): ?>
        <tr>

            <th class="key">
                <span class="tooltip">
                    <span><?php echo $d->title; ?></span>
                    <span class="desc"><?php echo $TEXT[$d->key.'_info']; ?></span>
                </span>
            </th>

            <td>
                <span class="value <?php echo $d->css; ?>"><?php echo $d->setting; ?></span>
            </td>

            <td>
<?php if ($d->boolean): ?>
                <select name="<?php echo $d->key; ?>" tabindex="<?php echo $d->tidx; ?>">
                    <option value="">-</option>
<?php foreach ($d->boolOptions as $opt): ?>
                    <option value="<?php echo $opt->var; ?>"><?php echo $TEXT[$opt->text]; ?></option>
<?php endforeach; ?>
                </select>
<?php else: ?>
                <input type="text" class="<?php echo $d->cssInput; ?>" name="<?php echo $d->key; ?>"
                maxlength="<?php echo $d->maxlen; ?>" tabindex="<?php echo $d->tidx; ?>" value="<?php echo $d->value; ?>" />
<?php endif; ?>
            </td>

            <td class="icon">
<?php if ($d->reset): ?>
                <a href="?cmd=murmur_settings&amp;reset_setting=<?php echo $d->key; ?>" class="button"
                    title="<?php printf($TEXT['reset_param'], $d->title); ?>">
                    <img src="<?php echo IMG_CLEAN_16; ?>" alt="" />
                </a>
<?php endif; ?>
            </td>

        </tr>
<?php endforeach; ?>

        <tr>
            <th colspan="4">
                <input type="submit" value="<?php echo $TEXT['apply']; ?>" />
            </th>
        </tr>

    </table>

</form>

<?php foreach ($module->settingsDebug as $array): ?>
<p class="debug">
    <strong>Hidden custom settings</strong> : <?php echo $array['key'].' => '.$array['value']; ?>
</p>
<?php endforeach;

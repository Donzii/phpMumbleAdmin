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
<?php require $PMA->widgets->getView('route_subTabs'); ?>
</div>

<form method="post" onSubmit="return isFormModified(this);">

    <input type="hidden" name="cmd" value="config" />
    <input type="hidden" name="set_settings_ext_viewer" />

    <table class="config">

        <tr class="pad">
            <th class="title"></th>
        </tr>

        <tr>
            <th>
                <label for="enable"><?php echo $TEXT['external_viewer_enable']; ?></label>
            </th>
            <td>
<?php if ($module->extViewerEnable): ?>
                <div class="right">
                    <a href="<?php $module->prt('path'); ?>?ext_viewer&amp;profile=<?php $module->prt('id'); ?>&amp;server=*">
                        <span><?php echo $TEXT['see_external_viewer']; ?></span>
                    </a>
                </div>
<?php endif; ?>
                <input type="checkbox" <?php $module->chked('enable'); ?> id="enable" name="enable" />
            </td>
        </tr>

        <tr>
            <th>
                <label for="width"><?php echo $TEXT['external_viewer_width']; ?></label>
            </th>
            <td>
                <input type="text" id="width" name="width" value="<?php $module->prt('width'); ?>" />
            </td>
        </tr>

        <tr>
            <th>
                <label for="height"><?php echo $TEXT['external_viewer_height']; ?></label>
            </th>
            <td>
                <input type="text" id="height" name="height" value="<?php $module->prt('height'); ?>" />
            </td>
        </tr>

        <tr>
            <th>
                <label for="vertical"><?php echo $TEXT['external_viewer_vertical']; ?></label>
            </th>
            <td>
                <input type="checkbox" <?php $module->chked('vertical'); ?> id="vertical" name="vertical" />
            </td>
        </tr>

        <tr>
            <th>
                <label for="scroll"><?php echo $TEXT['external_viewer_scroll']; ?></label>
            </th>
            <td>
                <input type="checkbox" <?php $module->chked('scroll'); ?> id="scroll" name="scroll" />
            </td>
        </tr>

        <tr>
            <th colspan="2">
                <input type="submit" value="<?php echo $TEXT['apply']; ?>" />
            </th>
        </tr>

    </table>

</form>

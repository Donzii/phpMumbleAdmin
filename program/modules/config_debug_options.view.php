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
    <input type="hidden" name="set_settings_debug" />

    <table class="config">

        <tr class="pad">
            <th class="title"></th>
            <td class="hide"></td>
        </tr>

        <tr>
            <th>
                Debug mode
                <span class="tooltip">
                    <img src="<?php echo IMG_INFO_16; ?>" alt="" />
                    <span class="desc">( 0 to disactivate debug mode )</span>
                </span>
            </th>
            <td>
                <input type="radio" name="mode" id="0" value="0" <?php HTML::chked($module->debug === 0); ?> />
                <label for="0">Level 0</label><br />
                <input type="radio" name="mode" id="1" value="1" <?php HTML::chked($module->debug === 1); ?> />
                <label for="1">Level 1</label><br />
                <input type="radio" name="mode" id="2" value="2" <?php HTML::chked($module->debug === 2); ?> />
                <label for="2">Level 2</label><br />
                <input type="radio" name="mode" id="3" value="3" <?php HTML::chked($module->debug === 3); ?> />
                <label for="3">Level 3</label><br />
            </td>
         </tr>

        <tr class="pad">
            <td colspan="2" class="hide"></td>
        </tr>

        <tr>
            <th>
                <label for="flag">Show language flag selection (on top)</label>
            </th>
            <td>
                <input type="checkbox" <?php $module->chked('langFlag'); ?> id="flag" name="flag" />
            </td>
        </tr>

        <tr>
            <th>
                <label for="stats">Show page generation stats</label>
            </th>
            <td>
                <input type="checkbox" <?php $module->chked('debugStats'); ?> id="stats" name="stats" />
            </td>
        </tr>

        <tr>
            <th>
                <label for="messages">Show debug messages</label>
            </th>
            <td>
                <input type="checkbox" <?php $module->chked('debugMessages'); ?> id="messages" name="messages" />
            </td>
        </tr>

        <tr>
            <th>
                <label for="session">Show user session</label>
            </th>
            <td>
                <input type="checkbox" <?php $module->chked('debugSession'); ?> id="session" name="session" />
            </td>
        </tr>

        <tr>
            <th>
                <label for="object">Show PMA object</label>
            </th>
            <td>
                <input type="checkbox" <?php $module->chked('pmaObject'); ?> id="object" name="object" />
            </td>
        </tr>

        <tr>
            <th colspan="2">
                <input type="submit" value="<?php echo $TEXT['apply']; ?>" />
            </th>
        </tr>

    </table>

</form>

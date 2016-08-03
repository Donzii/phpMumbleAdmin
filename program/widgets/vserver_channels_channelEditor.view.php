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

$widget = $PMA->widgets->getDatas('vserver_channels_channelEditor');?>

<form method="post" id="channelProperty" onSubmit="return isFormModified(this);">

    <input type="hidden" name="cmd" value="murmur_channel" />
    <input type="hidden" name="channel_property" />

    <fieldset>

        <div>
            <h4>
                <label for="default"><?php echo $TEXT['set_as_defaultchannel']; ?></label>
                <input type="checkbox" id="default" <?php $widget->chked('isDefault'); ?>
                    name="defaultchannel" <?php $widget->disabled('isDisabled'); ?> />
            </h4>
        </div>

        <div class="channelName">
<?php if ($widget->id > 0): ?>
            <h4><?php echo $TEXT['channel_name']; ?></h4>
            <p>
                <input type="text" name="name" value="<?php $widget->prt('name'); ?>" />
            </p>
<?php endif; ?>
        </div>

        <h4><?php echo $TEXT['channel_desc']; ?></h4>
        <iframe src="<?php echo PMA_FILE_SANDBOX_RELATIVE; ?>" sandbox="">
            <p>Your browser does not support iframes.</p>
        </iframe>

        <br />
        <br />

        <textarea name="description" cols="4" rows="6"><?php $widget->prt('desc'); ?></textarea>

        <br />
        <br />

        <h4><?php echo $TEXT['channel_pw']; ?></h4>
        <p>
            <input type="text" name="pw" value="<?php $widget->prt('password'); ?>" />
        </p>

        <br />
        <br />

        <h4><?php echo $TEXT['channel_pos']; ?></h4>
        <p>
            <input type="number" name="position" value="<?php $widget->prt('position'); ?>" />
        </p>

        <div class="apply">
            <input type="submit" value="<?php echo $TEXT['modify']; ?>" />
        </div>

    </fieldset>

</form>

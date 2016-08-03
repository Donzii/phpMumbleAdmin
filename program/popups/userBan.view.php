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

$widget = $PMA->widgets->getDatas('userBan'); ?>

<form id="userBan" method="POST" class="actionBox">

    <input type="hidden" name="cmd" value="murmur_bans" />
    <input type="hidden" name="addBan" value="" />
    <input type="hidden" name="name" value="<?php $widget->prt('login'); ?>" />
    <input type="hidden" name="ip" value="<?php $widget->prt('ip'); ?>" />
    <input type="hidden" name="mask" value="" />
    <input type="hidden" name="hash" value="<?php $widget->prt('certSha1'); ?>" />
    <input type="hidden" name="kickhim" value="" />

    <h3>
        <span><?php echo $TEXT['ban']; ?></span>
    </h3>

<?php require 'buttonCancel.inc'; ?>

    <fieldset>

        <p><?php $widget->printf($TEXT['ban_user'], 'login'); ?></p><br />

        <div class="body">
            <input type="text" autofocus="autofocus" placeholder="<?php echo $TEXT['reason']; ?>" name="reason" />
<?php require $PMA->widgets->getView('widget_banDurationSelector'); ?>
        </div>

        <div class="submit">
            <input type="submit" value="<?php echo $TEXT['ban']; ?>" />
        </div>

    </fieldset>

</form>


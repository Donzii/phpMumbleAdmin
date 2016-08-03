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

$widget = $PMA->widgets->getDatas('channelMoveOut'); ?>

<div class="toolbar">
    <a href="./" class="button" title="<?php echo $TEXT['cancel']; ?>">
        <img src="<?php echo IMG_CANCEL_16; ?>" alt="" />
    </a>
</div>

<?php if (isset($_GET['to'])): ?>

<form id="channelMoveOut" method="POST" class="actionBox" onSubmit="return isFormModified(this);">

    <input type="hidden" name="cmd" value="murmur_channel" />
    <input type="hidden" name="move_users_out_the_channel" value="<?php echo $_GET['to']; ?>" />

    <h3><?php echo $TEXT['move_user_off_chan']; ?></h3>

    <div class="body">

        <p><?php echo $TEXT['select_user_to_move']; ?></p>

        <ul class="scroll">
<?php foreach ($widget->scroll as $u): ?>
            <li>
                <input type="checkbox" class="chkbox" id="id<?php echo $u->session; ?>" name="<?php echo $u->session; ?>" />
                <label for="id<?php echo $u->session; ?>"><?php echo htEnc($u->name); ?></label>
            </li>
<?php endforeach; ?>
        </ul>

    </div>

    <div class="submit">
        <input type="submit" value="<?php echo $TEXT['move']; ?>" />
    </div>

</form>

<?php else: ?>

<div class="information">

    <h3>
        <span><?php echo $TEXT['move_user_off_chan']; ?></span>
    </h3>

    <p><?php echo $TEXT['select_in_right_tree']; ?></p>

</div>

<?php endif;

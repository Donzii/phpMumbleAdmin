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

$widget = $PMA->widgets->getDatas('vserver_channels_userComment'); ?>

<iframe src="<?php echo PMA_FILE_SANDBOX_RELATIVE; ?>" sandbox="">
    <p>Your browser does not support iframes.</p>
</iframe>

<br />
<br />

<form method="post" onSubmit="return unchanged(this.change_user_comment);">

    <input type="hidden" name="cmd" value="murmur_users_sessions" />

    <textarea name="change_user_comment" cols="4" rows="6"><?php $widget->prt('comment'); ?></textarea>

    <br />
    <br />

    <div class="right">
        <input type="submit" value="<?php echo $TEXT['modify']; ?>" />
    </div>

</form>


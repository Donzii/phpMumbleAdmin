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

<form id="serversMessage" method="POST" class="actionBox small">

    <input type="hidden" name="cmd" value="overview" />

    <h3>
        <label for="msg"><?php echo $TEXT['msg_all_srv']; ?></label>
    </h3>

<?php require 'buttonCancel.inc'; ?>

    <fieldset>

        <div class="body">
            <textarea autofocus="autofocus" required="required" id="msg" name="messageToServers" rows="10" cols="4"></textarea>
        </div>

        <div class="submit">
            <input type="submit" value="<?php echo $TEXT['submit']; ?>" />
        </div>

    </fieldset>

</form>

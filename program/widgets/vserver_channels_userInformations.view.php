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

$widget = $PMA->widgets->getDatas('vserver_channels_userInformations'); ?>

<div id="userInfos">

<?php foreach ($widget->datas as $info): ?>
    <p>
        <span class="text"><?php echo htEnc($info->desc); ?></span>:
<?php if ($info->tooltip !== ''): ?>
        <span class="tooltip">
            <img src="<?php echo IMG_INFO_13; ?>" alt="" />
            <span class="desc"><?php echo $info->tooltip; ?></span>
        </span>
<?php endif; ?>
        <span class="value"><?php echo htEnc($info->value); ?></span>
    </p>
<?php endforeach; ?>

</div>

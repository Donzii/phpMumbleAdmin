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

<table class="config">

    <tr class="pad">
        <th class="title vlarge"><?php echo $TEXT['default_settings']; ?></th>
        <th class="title"><?php printf($TEXT['murmur_vers'], $PMA->meta->getVersion('txt')); ?></th>
    </tr>

<?php foreach ($module->defaultConf as $key => $desc): ?>
        <tr class="small">
            <th><?php echo $key; ?></th>
            <td><?php echo htEnc($desc); ?></td>
        </tr>
<?php endforeach; ?>

</table>

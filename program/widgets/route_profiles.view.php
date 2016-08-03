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

$widget = $PMA->widgets->getDatas('route_profiles');

if (count($widget->profiles) > 1): ?>
        <ul id="PMA_profiles">
<?php foreach ($widget->profiles as $d): ?>
            <li class="<?php echo $d->css; ?>" title="<?php echo $d->title; ?>">
                <a href="?profile=<?php echo $d->id; ?>">
<?php if ($d->isDefault): ?>
                    <img src="images/tango/delete_12.png" alt="" />
<?php endif; ?>
                    <span class="text"><?php echo htEnc($d->name); ?></span>
<?php if ($d->isPublic): ?>
                    <img src="images/xchat/blue_8.png" alt="" />
<?php endif;
if ($d->isDisabled): ?>
                    <img src="images/xchat/red_8.png" alt="" />
<?php endif; ?>
                </a>
            </li>
<?php endforeach; ?>
        </ul>
<?php endif;

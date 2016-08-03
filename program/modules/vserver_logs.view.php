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
    <div class="expand">
        <span><?php echo $TEXT['filters']; ?></span>
        <img src="<?php echo IMG_ARROW_DOWN; ?>" alt="" />
        <ul>
<?php foreach ($filtersMenu->menu as $f): ?>
            <li>
<?php if (isset($f->href)): ?>
                <a href="<?php echo $f->href; ?>">
                    <img src="<?php echo $f->img; ?>" alt="" />
                    <span><?php echo $f->text; ?></span>
                </a>
<?php elseif (isset($f->separation)): ?>
                <hr />
<?php else: ?>
                <span><?php echo $f->text; ?></span>
<?php endif; ?>
            </li>
<?php endforeach; ?>
        </ul>
    </div>
<?php require $PMA->widgets->getView('widget_search'); ?>
</div>

<?php require $PMA->widgets->getView('widget_logs');

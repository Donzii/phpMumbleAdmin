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

<aside id="viewerBox">

    <div class="toolbar <?php echo $module->viewerState; ?>">

<?php if (! empty($module->actionMenu)): ?>

        <div class="expand">
            <span><?php echo $TEXT['action']; ?></span>
            <img src="<?php echo IMG_ARROW_DOWN; ?>" alt="" />
            <ul>
<?php foreach ($module->actionMenu as $a): ?>
                <li>
<?php if (! is_null($a->href)): ?>
                    <a href="<?php echo $a->href; ?>" <?php echo $a->js; ?>>
<?php endif; ?>
                        <img src="<?php echo $a->img; ?>" alt="" />
                        <span><?php echo $a->text; ?></span>
<?php if (! is_null($a->href)): ?>
                    </a>
<?php endif; ?>
                </li>
<?php endforeach; ?>
            </ul>
        </div>

<?php require $PMA->widgets->getView('route_subTabs');
endif; ?>

    </div>

<?php
if ($viewerBoxWidget->type === 'widget') {
    require $PMA->widgets->getView($viewerBoxWidget->id);
} else {
    require $PMA->widgets->getView($viewerBoxWidget->id);
}
?>

</aside>

<?php require $PMA->widgets->getView('widget_viewer'); ?>

<div class="clear"></div>

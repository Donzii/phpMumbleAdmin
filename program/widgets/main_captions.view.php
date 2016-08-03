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

$captions = $PMA->skeleton->getCaptions();
if (empty($captions)) {
    return;
}
?>

                <div class="caption">
                    <span class="inside">
                        <span class="title"><?php echo $TEXT['caption']; ?></span>
<?php foreach ($captions as $c): ?>
                        <span class="tooltip">
                            <img src="<?php echo $c->src; ?>" class="<?php echo $c->css; ?>" alt="" />
                            <span class="desc"><?php echo $c->text; ?></span>
                        </span>
<?php endforeach; ?>
                    </span>
                </div><!-- captions - END -->

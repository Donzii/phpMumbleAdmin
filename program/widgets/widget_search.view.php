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

    <form id="search" method="post" onSubmit="return unchanged(this.<?php echo $searchWidget->CMDname; ?>);">

        <input type="hidden" name="cmd" value="<?php echo $searchWidget->CMDroute; ?>" />

<?php if (! is_null($searchWidget->searchValue)): ?>
        <span><?php echo $TEXT['found']; ?> :</span> <span class="found"><?php echo $searchWidget->totalFound; ?></span>
        <a href="<?php echo $searchWidget->removeSearchHREF; ?>" class="button" title="<?php echo $TEXT['clean_search']; ?>">
            <img src="<?php echo IMG_CANCEL_22; ?>" alt="" />
        </a>
<?php endif; ?>

        <input type="text" name="<?php echo $searchWidget->CMDname; ?>" value="<?php echo $searchWidget->searchValue; ?>" />
        <input type="submit" value="<?php echo $TEXT['search']; ?>" />
    </form>

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

$module->table->contructPagingMenu();
if (count($module->table->pagingMenu) < 2) {
    return;
}
?>

<div class="tablePaging">

<?php if ($module->table->currentPage === 1): ?>
    <span class="first nolink"><img src="<?php echo IMG_SPACE_16; ?>" alt="" /></span>
    <span class="prev nolink"><img src="<?php echo IMG_SPACE_16; ?>" alt="" /></span>
<?php else: ?>
    <a class="first" title="<?php echo $TEXT['go_first']; ?>" href="?tablePage=1">
        <span><img src="images/tango/page_first_16.png" alt="" /></span>
    </a>
    <a class="prev" title="<?php echo $TEXT['go_prev']; ?>" href="?tablePage=<?php echo ($module->table->currentPage -1); ?>">
        <span><img src="images/tango/page_prev_16.png" alt="" /></span>
    </a>
<?php endif; ?>

<?php foreach ($module->table->pagingMenu as $m): ?>
    <a class="<?php echo $m->css; ?>" href="?tablePage=<?php echo $m->page; ?>">
        <span><?php echo $m->page; ?></span>
    </a>
<?php endforeach; ?>

<?php if ($module->table->currentPage === $module->table->totalOfPages): ?>
    <span class="next nolink"><img src="<?php echo IMG_SPACE_16; ?>" alt="" /></span>
    <span class="last nolink"><img src="<?php echo IMG_SPACE_16; ?>" alt="" /></span>
<?php else: ?>
    <a class="next" title="<?php echo $TEXT['go_next']; ?>" href="?tablePage=<?php echo ($module->table->currentPage +1); ?>">
        <span><img src="images/tango/page_next_16.png" alt="" /></span>
    </a>
    <a class="last" title="<?php echo $TEXT['go_last']; ?>" href="?tablePage=<?php echo $module->table->totalOfPages; ?>">
        <span><img src="images/tango/page_last_16.png" alt="" /></span>
    </a>
<?php endif; ?>

    <span class="total nolink"><?php printf($TEXT['total_pages'], $module->table->totalOfPages); ?></span>

<?php if ($module->table->totalOfPages > 9): ?>
    <form method="GET" class="go">
        <input type="text" class="medium" name="tablePage" required="required" />
        <input type="submit" value="GO" />
    </form>
<?php endif; ?>

</div>

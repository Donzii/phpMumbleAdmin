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

$logs = $module->logs->get('logs'); ?>

<div id="PMA_logs">

<?php if ($module->logs->comment !== ''): ?>
    <h5><?php echo $module->logs->comment; ?></h5>
<?php endif;

if (empty($logs)): ?>
    <h4>No log found</h4>
<?php endif;

foreach ($logs as $log):
    if (isset($log->newDay)): ?>
    <h4><?php echo $log->newDay; ?></h4>
<?php endif; ?>
    <p>
        <time><?php echo $log->time; ?></time>
        <span class="log"><?php echo $log->text; ?></span>
    </p>
<?php endforeach; ?>

</div>

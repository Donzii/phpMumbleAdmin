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
<?php require $PMA->widgets->getView('route_subTabs'); ?>
</div>

<h3>Check languages differences</h3>

<?php if (! isset($module->languageDiffs)): ?>
<p>No need to check english language !</p>
<?php else: ?>

<?php if (empty($module->languageDiffs)): ?>

<p>No difference found. The language pack is up-to-date.</p>

<?php else: ?>

<div id="debugLanguages">

<?php foreach ($module->languageDiffs as $data): ?>

<?php if ($data->title) : ?>
    <br />
    <h4><?php echo $data->text; ?></h4>
<?php elseif ($data->new): ?>
    <p>
        <span class="missing">MISSING</span>:
        <span class="var">$TEXT</span>
        [<span class="str">'<?php echo $data->key; ?>'</span>] =
        <span class="str">'<?php echo htEnc($data->text); ?>'</span>
    </p>
<?php elseif ($data->old): ?>
    <p>
        <span class="obsolete">OBSOLETE</span>:
        <span class="var">$TEXT</span>[
        <span class="str">'<?php echo $data->key; ?>'</span>] =
        <span class="str">'<?php echo htEnc($data->text); ?>'</span>
    </p>
<?php endif; ?>

<?php endforeach; ?>

</div>
<?php endif;

endif;

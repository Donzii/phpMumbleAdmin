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

<h3>Compare languages strings</h3>

<?php if (! isset($module->dirScan)): ?>
<p>No need to compare english language !</p>
<?php else: ?>

<div class="expand">
    <span class="fill">Files <img src="<?php echo IMG_ARROW_DOWN;?>" alt="" /></span>
    <ul>
<?php foreach ($module->menu as $data): ?>
        <li>
            <a class="<?php echo $data->css; ?>" href="?fileName=<?php echo $data->name; ?>"><?php echo $data->name; ?></a>
        </li>
<?php endforeach; ?>
    </ul>
</div>

<?php if (isset($module->filePath)): ?>

<div id="debugLanguages">

    <table class="tdwrap">

        <tr class="pad">
            <th class="title"><?php echo htEnc($module->filePath); ?></th>
            <td class="hide"></td>
            <td class="hide"></td>
        </tr>

<?php foreach ($module->datas as $data): ?>

        <tr class="pad">
            <th>
                <span class="var">$TEXT</span>
                [<span class="str">'<?php echo $data->key; ?>'</span>]
            </th>
            <td><?php echo htEnc($data->ref); ?></td>
<?php if (is_null($data->comp)): ?>
            <td class="missing">*** MISSING ***</td>
<?php else: ?>
            <td><?php echo htEnc($data->comp); ?></td>
<?php endif; ?>
        </tr>

<?php endforeach; ?>

    </table>

</div>

<?php endif;
endif;

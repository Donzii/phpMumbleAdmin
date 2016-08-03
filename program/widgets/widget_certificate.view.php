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

$widget = $PMA->widgets->getDatas('widget_certificate'); ?>

            <div id="certificate">
<?php if ($widget->isError()): ?>
                <h4>
                    <span>Error : <?php echo $widget->errorText; ?></span>
                </h4>
<?php endif;
foreach ($widget->datas as $data):
if ($data->title): ?>
                <h4>
                    <span><?php echo htEnc($data->text); ?></span>
                </h4>
<?php else: ?>
                <p>
                    <span class="key"><?php echo htEnc($data->key); ?>:</span>
                    <span class="value"><?php echo htEnc($data->text); ?></span>
                </p>
<?php endif;
endforeach; ?>
            </div>

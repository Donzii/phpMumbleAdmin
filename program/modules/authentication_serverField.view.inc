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

if (! $module->allowServerField): ?>
                    <input type="text" class="small" disabled="disabled" />
<?php elseif (! isset($module->vserversList)): ?>
                    <input type="text" <?php $module->required('isPwRequest'); ?> class="small" id="server_id" name="server_id" maxlength="6" />
<?php else: ?>
                    <select <?php $module->required('isPwRequest'); ?> id="server_id" name="server_id">
                        <option value=""><?php echo $TEXT['select_server']; ?></option>
<?php foreach ($module->vserversList as $a): ?>
                        <option value="<?php echo $a['id']; ?>"><?php echo $a['id']; ?># <?php echo $a['name']; ?></option>
<?php endforeach; ?>
                    </select>
<?php endif;

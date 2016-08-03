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

<div id="vserverSettingsMore">

    <div class="toolbar">
<?php if ($module->resetCert): ?>
            <a href="?resetCertificate" class="button right" title="<?php printf($TEXT['reset_param'], 'certificate'); ?>"
                onClick="return popup('settingsResetCertificate');">
                <img src="<?php echo IMG_CLEAN_16; ?>" alt="" />
            </a>
<?php endif;
require $PMA->widgets->getView('route_subTabs'); ?>
    </div>

<?php require $PMA->widgets->getView('widget_certificate'); ?>

    <form method="post" class="actionBox" enctype="multipart/form-data">

        <input type="hidden" name="cmd" value="murmur_settings" />
        <input type="hidden" name="MAX_FILE_SIZE" value="20480" />

            <h3><?php echo $TEXT['add_certificate']; ?></h3>

        <div class="body">
<?php if ($module->fileUpload): ?>
            <input type="file" required="required" name="add_certificate" />
<?php else: ?>
            <textarea required="required" name="add_certificate" rows="10" cols="4"></textarea>
<?php endif; ?>
        </div>

        <div class="submit">
            <input type="submit" value="<?php echo $TEXT['submit']; ?>" />
        </div>

    </form>

</div>

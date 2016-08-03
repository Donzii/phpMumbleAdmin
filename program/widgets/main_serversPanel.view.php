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

$widget = $PMA->widgets->getDatas('main_serversPanel');

if ($widget->displayServersList): ?>

                <form class="dropdown_list" method="GET">

                    <input type="hidden" name="page" value="vserver" />

<?php if ($widget->displayRefreshButton): ?>
                    <a href="?cmd=overview&amp;refreshServerList" title="<?php echo $TEXT['refresh_srv_cache']; ?>">
                        <img src="images/tango/refresh_16.png" class="button" alt="" />
                    </a>
<?php endif; ?>

                    <select name="sid" required="required" onChange="submit();">
                        <option value=""><?php echo $TEXT['select_server']; ?></option>
<?php foreach ($widget->serversList as $o): // Collapse all options by removing EOL ?>
<option class="<?php echo $o->css; ?>" <?php HTML::disabled($o->disabled); ?> value="<?php echo $o->id; ?>"><?php echo $o->text; ?></option><?php echo ''; ?>
<?php endforeach; ?>

                    </select>
                    <noscript>
                        <input type="submit" value="<?php echo $TEXT['ok']; ?>" />
                    </noscript>
<?php foreach ($widget->serversListButtons as $button):
if (is_int($button->id)): ?>
                    <a href="?page=vserver&amp;sid=<?php echo $button->id; ?>">
                        <img src="<?php echo $button->src; ?>" class="button" alt="" />
                    </a>
<?php else: ?>
                    <img src="<?php echo IMG_SPACE_16; ?>" class="button" alt="" />
<?php endif;
endforeach; ?>

                </form>
<?php endif;

if ($widget->displayServerName): ?>
                <h2>
                    <a href="?cmd=config&amp;toggle_infopanel" <?php echo $widget->infoPanelJs; ?> title="<?php echo $TEXT['toggle_panel']; ?>">
                        <img src="<?php echo $widget->infoPanelSrc; ?>" id="js_infopanel" class="button" alt="" />
                    </a>
                    <span class="id"><?php $widget->prt('serverID'); ?></span>
                    <abbr>#</abbr>
                    <span class="serverName"><?php $widget->prt('serverName'); ?></span>
                </h2>
<?php endif;

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

if (! isset($_GET['profile'], $_GET['server']) OR ! $PMA->config->get('external_viewer_enable')) {
    die();
}

require PMA_DIR_INCLUDES.'def.images.inc';

$PMA->widgets->newWidget('widget_viewer');

/**
* width & height CSS
*/
if ($PMA->config->get('external_viewer_width') > 0) {
    $width = $PMA->config->get('external_viewer_width').'px';
} else {
    $width = 'auto';
}
if ($PMA->config->get('external_viewer_height') > 0) {
    $height = $PMA->config->get('external_viewer_height').'px';
} else {
    $height = 'auto';
}

$extViewerProfile = $PMA->profiles->get((int)$_GET['profile']);
// Profile has to be public
if (! $extViewerProfile['public']) {
    die();
}
/**
* Check for Ice-PHP 3.4 workaround.
*/
require PMA_FILE_ICE34_WORKAROUND;
/**
* Get connection to Ice
*/
PMA_MurmurMetaHelper::connection();
if (! $PMA->meta->isConnected()) {
    die();
}

/**
* Setup the list of servers $prx
*/
if ($_GET['server'] === '*') {
    try {
        $booted = $PMA->meta->getBootedServers();
    } catch (Exception $e) {
        die();
    }
    $prxList = $booted;
} else {
    $servers = explode('-', $_GET['server']);
    foreach ($servers as $id) {
        if (ctype_digit($id)) {
            try {
                $prxList[] = $PMA->meta->getServer((int)$id);
            } catch (Exception $e) {
                continue;
            }
        }
    }
}

?>
<style type="text/css">
<?php echo file_get_contents('css/viewer.css'); ?>

/* EXTERNAL */

.viewer.external {
    width: <?php echo $width; ?>;
    height: <?php echo $height; ?>;
    margin: 0px 2px;
    padding: 5px;
    /* outset black */
    border-top: 1px solid #b2b2b2;
    border-left: 1px solid #b2b2b2;
    border-bottom: 1px solid black;
    border-right: 1px solid black;
}
.viewer.external.vertical {
    float: left;
}
.viewer.external.scroll {
    overflow: auto;
}
.viewer.external.scroll p,
.viewer.external.scroll .text {
    overflow: visible; /* remove from .viewer */
}
</style>
<?php

/**
* Perform multiple viewer.
*/
foreach ($prxList as $prx) {
    if (! is_null($prx)) {
        try {
            $getTree = $prx->getTree();
            $viewer = new PMA_MurmurViewer();
            $viewer->css = 'external';
            if ($PMA->config->get('external_viewer_vertical')) {
                $viewer->css .= ' vertical';
            }
            if ($PMA->config->get('external_viewer_scroll')) {
                $viewer->css .= ' scroll';
            }
            $viewer->setParam('serverName', $prx->getParameter('registername'));
            require $PMA->widgets->getView('widget_viewer');
        } catch (Exception $e) {
            continue;
        }
    }
}

die();


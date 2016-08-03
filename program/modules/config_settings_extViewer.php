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

$module->extViewerEnable = $PMA->config->get('external_viewer_enable');
$module->set('enable', $PMA->config->get('external_viewer_enable'));
$module->set('path', PMA_HTTP_HOST.PMA_HTTP_PATH);
$module->set('id', $PMA->router->getRoute('profile'));
$module->set('width', $PMA->config->get('external_viewer_width'));
$module->set('height', $PMA->config->get('external_viewer_height'));
$module->set('vertical', $PMA->config->get('external_viewer_vertical'));
$module->set('scroll', $PMA->config->get('external_viewer_scroll'));


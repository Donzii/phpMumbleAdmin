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

define('PMA_DIR_CSS', PMA_PATH_ROOT.'css/');
define('PMA_DIR_CONFIG', PMA_PATH_ROOT.'config/');
define('PMA_DIR_HTMLCACHE', PMA_PATH_ROOT.'cache/');
define('PMA_DIR_LANGUAGES', PMA_PATH_ROOT.'languages/');
define('PMA_DIR_LOGS', PMA_PATH_ROOT.'logs/');

define('PMA_DIR_SANDBOXS', PMA_DIR_HTMLCACHE);
define('PMA_DIR_AVATARS', PMA_DIR_HTMLCACHE);

define('PMA_DIR_CACHE', PMA_DIR_PROG.'cache/');
define('PMA_DIR_FUNCTIONS', PMA_DIR_PROG.'functions/');
define('PMA_DIR_LIB', PMA_DIR_PROG.'lib/');
define('PMA_DIR_INCLUDES', PMA_DIR_PROG.'includes/');
define('PMA_DIR_MODULES', PMA_DIR_PROG.'modules/');
define('PMA_DIR_POPUPS', PMA_DIR_PROG.'popups/');
define('PMA_DIR_ROUTES', PMA_DIR_PROG.'routes/');
define('PMA_DIR_WIDGETS', PMA_DIR_PROG.'widgets/');

define('PMA_DIR_SESSIONS', PMA_DIR_CACHE.'sessions/');

define('PMA_DIR_SLICE_PHP', PMA_DIR_LIB.'MurmurSlicesPhp/');
define('PMA_DIR_SLICE_PHP_34', PMA_DIR_SLICE_PHP.'ice34/');
define('PMA_DIR_SLICE_PHP_35', PMA_DIR_SLICE_PHP.'ice35/');
define('PMA_DIR_SLICE_PHP_CUSTOM', PMA_PATH_ROOT.'slicesPhp/');
define('PMA_DIR_SLICE_PHP_CUSTOM_34', PMA_DIR_SLICE_PHP_CUSTOM.'ice34/');
define('PMA_DIR_SLICE_PHP_CUSTOM_35', PMA_DIR_SLICE_PHP_CUSTOM.'ice35/');

define('PMA_FILE_LOGS', PMA_DIR_LOGS.'log.txt');
define('PMA_FILE_ICE34_WORKAROUND', PMA_DIR_INCLUDES.'ice34Workaround.inc');

// Relative paths
define('PMA_DIR_AVATARS_RELATIVE', str_replace(PMA_PATH_ROOT, '', PMA_DIR_AVATARS));
/**
* Define file iframe in def.images.inc as we need a valid session id.
*/

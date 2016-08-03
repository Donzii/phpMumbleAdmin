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

define('PMA_FILE_SANDBOX', PMA_DIR_SANDBOXS.'sandbox_'.session_id().'.html');
define('PMA_FILE_SANDBOX_RELATIVE', str_replace(PMA_PATH_ROOT, '', PMA_FILE_SANDBOX));

define('PMA_DIR_IMAGES', 'images/');
define('PMA_DIR_FLAGS', PMA_DIR_IMAGES.'flags/');

define('IMG_SPACE_16', 'images/pma/space.png');

define('IMG_ADD_16', 'images/tango/add_16.png');
define('IMG_ADD_22', 'images/tango/add_22.png');

define('IMG_2_ADD_16', 'images/tango/add2_16.png');

define('IMG_DELETE_16', 'images/tango/delete_16.png');
define('IMG_DELETE_22', 'images/tango/delete_22.png');
define('IMG_2_DELETE_16', 'images/tango/delete2_16.png');

define('IMG_CLEAN_16', 'images/tango/clear_16.png');

define('IMG_TRASH_16', 'images/tango/trash_16.png');

define('IMG_CANCEL_12', 'images/gei/cancel_12.png');
define('IMG_CANCEL_16', 'images/gei/cancel_16.png');
define('IMG_CANCEL_22', 'images/gei/cancel_22.png');

define('IMG_MSG_16', 'images/tango/msg_16.png');
define('IMG_MSG_22', 'images/tango/msg_22.png');

define('IMG_KEY_22', 'images/gei/key_22.png');

define('IMG_CONN_16', 'images/tango/connection_16.png');
define('IMG_CONN_22', 'images/tango/connection_22.png');

define('IMG_EDIT_16', 'images/tango/edit_16.png');
define('IMG_EDIT_22', 'images/tango/edit_22.png');

define('IMG_UP_16', 'images/tango/step_16.png');
define('IMG_DOWN_16', 'images/tango/step_inv_16.png');

define('IMG_INFO_13', 'images/gei/info_13.png');
define('IMG_INFO_16', 'images/gei/info_16.png');
define('IMG_INFO_22', 'images/gei/info_22.png');

define('IMG_OK_16', 'images/gei/ok_16.png');

define('IMG_ARROW_DOWN', 'images/pma/arrowSmall_down.png');
define('IMG_ARROW_UP', 'images/pma/arrowSmall_up.png');

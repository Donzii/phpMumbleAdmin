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

/**
 * Workaround for php-Ice 3.4
 *
 * Ice.php and slices php definitions files require to be loaded in the global scope.
 * See: http://www.zeroc.com/forums/help-center/5200-no-object-found-icephp_definestruct.html
*/

if (PMA_ICE_INT >= 30400 && PMA_ICE_INT < 30500) {

    $PMA->debug('Ice 3.4 workaround required', 2);

    if (1 !== @include('Ice.php')) {
        $PMA->debugError('Ice 3.4 workaround could not load Ice.php file');
    } else {
        if (isset($extViewerProfile)) {
            $profile = $extViewerProfile;
        } else {
            $profile = $PMA->userProfile;
        }
        if (is_file($file = PMA_DIR_SLICE_PHP_CUSTOM_34.$profile['slice_php'])) {
            include $file;
        } elseif (is_file($file = PMA_DIR_SLICE_PHP_34.$profile['slice_php'])) {
            include $file;
        }
    }
}

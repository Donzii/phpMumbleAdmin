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
* Get Ice connection helper.
*/

class PMA_MurmurMetaHelper
{
    public static function connection()
    {
        $PMA = PMA_core::getInstance();
        $profile = $PMA->userProfile;

        if (! is_array($profile)) {
            if ($PMA->user->is(PMA_USER_ADMIN)) {
                $PMA->messageError('iceprofiles_admin_none');
            }
            $PMA->debugError(__method__ .' : No profile found');
        } else {

            if (PMA_ICE_INT < 30400) {
               $connection = new PMA_MurmurConnectionIce32();
            } elseif (PMA_ICE_INT >= 30400 && PMA_ICE_INT < 30500) {
               $connection = new PMA_MurmurConnectionIce34();
            } else {
                $connection = new PMA_MurmurConnectionIce35();
            }

            // Enable debug.
            $connection->addDebugObs($PMA);

            $connection->host = $profile['host'];
            $connection->port = $profile['port'];
            $connection->timeout = $profile['timeout'];
            $connection->secret = $profile['secret'];
            $connection->slice_profile = $profile['slice_profile'];
            $connection->slice_php = $profile['slice_php'];

            try {
                $meta = $connection->getMeta();
                $PMA->meta->setMeta($meta);
            } catch (PMA_IceConnectionException $e) {
                $PMA->messageIceError($e->getMessage());
            }

            if ($PMA->meta->isConnected()) {
                $PMA->meta->setDefaultConf($connection->defaultConf);
                $PMA->meta->setSecret($connection->secret);
                $PMA->meta->setVersion($connection->version);
            }
        }
    }
}
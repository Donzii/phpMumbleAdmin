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

class PMA_passwordHelper
{
    /**
    * Crypt a password
    *
    * default algorithm: BLOWFISH
    * If blowfish is not configured for the system: MD5
    * else: sytem default crypt algorithm
    *
    * @return string
    */
    public static function crypt($pw)
    {
        if (defined('CRYPT_BLOWFISH') && CRYPT_BLOWFISH === 1) {
            return crypt($pw, '$2a$08$'.genRandomChars(22).'$');
        }
        // MD5
        if (defined('CRYPT_MD5') && CRYPT_MD5 === 1) {
            return crypt($pw, '$1$'.genRandomChars(22).'$');
        }
        // Use the default system hash.
        return crypt($pw);
    }

    /**
    * Check for crypted password.
    *
    * @return Boolean
    */
    public static function check($pw, $crypted)
    {
        return (crypt($pw, $crypted) === $crypted);
    }

    /**
    * Check if a password is not empty and match with the confirm field.
    *
    * @return Bool
    */
    public static function confirm($pw, $confirm_pw)
    {
        return ($pw !== '' && $pw === $confirm_pw);
    }
}

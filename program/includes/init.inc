<?php

 /**
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
* Memo:
* get_magic_quotes_gpc() : PHP 4, PHP 5
* PHP 5.4 : Always returns false because the magic quotes feature is removed.
*/
if (get_magic_quotes_gpc() === 1) {
    function stripslashesArray($var)
    {
        return is_array($var) ? array_map('stripslashesArray', $var) : stripslashes($var);
    }
    // Strip slashes GPC (GET / POST / COOKIE).
    $_GET = stripslashesArray($_GET);
    $_POST = stripslashesArray($_POST);
    $_COOKIE = stripslashesArray($_COOKIE);
}

/**
* Default is 60 secondes, it's really too long
*/
if (ini_get('default_socket_timeout') > 10) {
    @ini_set('default_socket_timeout', 10);
}

// MEMO: check for
// session.auto_start
// file_uploads

/**
* Remove magic quotes
*
* Memo:
* magic_quotes_runtime, magic_quotes_sybase : PHP 4, PHP 5
* PHP 5.4 : Theses PHP options do not exists anymore because the magic quotes feature was removed from PHP
*/
@ini_set('magic_quotes_runtime', 0);
@ini_set('magic_quotes_sybase', 0);

define('PMA_NAME', 'phpMumbleAdmin');

define('PMA_VERS_STR', '0.4.4');
define('PMA_VERS_INT', 44);
define('PMA_VERS_DESC', '');

/**
* Setup PMA URLs
*/
$http = getenv('HTTPS') === 'on' ? 'https' : 'http';
$port = $_SERVER['SERVER_PORT'];
$port = ($port !== '80' && $port !== '443') ? ':'.$port : '';
define('PMA_HTTP_HOST', $http.'://'.$_SERVER['HTTP_HOST'].$port);
define('PMA_HTTP_PATH', str_replace('index.php', '', $_SERVER['SCRIPT_NAME']));

define('PMA_OS', strToLower(php_uname('s')));

define('SUHOSIN_COOKIE_ENCRYPT', (ini_get('suhosin.cookie.encrypt') === '1'));

/**
* Get Ice versions ( string & integer).
*/
if (defined('ICE_STRING_VERSION') && defined('ICE_INT_VERSION')) {
    // phpIce 3.2
    define('PMA_ICE_STR', ICE_STRING_VERSION);
    define('PMA_ICE_INT', ICE_INT_VERSION);
} elseif (function_exists('Ice_stringversion') && function_exists('Ice_intversion')) {
    // phpIce 3.3 / 3.4 / 3.5
    define('PMA_ICE_STR', Ice_stringversion());
    define('PMA_ICE_INT', Ice_intversion());
} else {
    define('PMA_ICE_STR', 'Not found');
    define('PMA_ICE_INT', 0);
}

<?php

 /*
 *    phpMumbleAdmin (PMA), web php administration tool for murmur ( mumble server daemon ).
 *    Copyright (C) 2010 - 2013  Dadon David. PMA@ipnoz.net
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if ( ! defined( 'PMA_STARTED' ) ) { die( 'ILLEGAL: You cannot call this script directly !' ); }

/**
* Default is 60 secondes, it's really too long
*/
if ( ini_get( 'default_socket_timeout' ) > 10 ) {
	@ini_set( 'default_socket_timeout', 10 );
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
@ini_set( 'magic_quotes_runtime', 0 );
@ini_set( 'magic_quotes_sybase', 0 );

/**
* Memo:
* get_magic_quotes_gpc() : PHP 4, PHP 5
* PHP 5.4 : Always returns FALSE because the magic quotes feature was removed from PHP
*/
if ( get_magic_quotes_gpc() === 1 ) {

	function stripslashes_array( $var ) {
		return is_array( $var ) ? array_map( 'stripslashes_array', $var ) : stripslashes( $var );
	}

	// Strip slashes GPC ( GET / POST / COOKIE ).
	$_GET = stripslashes_array( $_GET );
	$_POST = stripslashes_array( $_POST );
	$_COOKIE = stripslashes_array( $_COOKIE );
}


require 'def.files.php';
require 'def.user_classes.php';

define( 'PMA_NAME', 'phpMumbleAdmin' );

define( 'PMA_VERS_STR', '0.4.3' );
define( 'PMA_VERS_INT', 43 );
define( 'PMA_VERS_DESC', '' );

define( 'PMA_TIME', time() );

define( 'PMA_USER_IP', $_SERVER['REMOTE_ADDR'] );

define( 'EOL', PHP_EOL );

// Http protocole
$http = getenv( 'HTTPS' ) === 'on' ? 'https' : 'http';

// Http port
$port = $_SERVER['SERVER_PORT'];
$port = ( $port !== '80' && $port !== '443' ) ? ':'.$port : '';

define( 'PMA_HTTP_HOST', $http.'://'.$_SERVER['HTTP_HOST'].$port );
define( 'PMA_HTTP_PATH', str_replace( 'index.php', '', $_SERVER['SCRIPT_NAME'] ) );

define( 'PMA_OS', strToLower( php_uname( 's' ) ) );

define( 'SUHOSIN_COOKIE_ENCRYPT', ( ini_get( 'suhosin.cookie.encrypt' ) === '1' ) );

// Define PMA installation mode
define( 'PMA_INSTALL', is_file( 'install/install.php' ) );

// Setup PMA mode ( output / command / external viewer )
if ( isset( $_GET['cmd'] ) ) {

	define( 'PMA_MODE', 'cmd' );
	define( 'PMA_CMD_MODE', $_GET['cmd'] );

} elseif ( isset( $_POST['cmd'] ) ) {

	define( 'PMA_MODE', 'cmd' );
	define( 'PMA_CMD_MODE', $_POST['cmd'] );

} elseif ( isset( $_GET['ext_viewer'] ) ) {

	define( 'PMA_MODE', 'ext_viewer' );

	if ( ! isset( $_GET['profile'] ) ) {
		die;
	}

	$pid = (int) $_GET['profile'];

} else {
	define( 'PMA_MODE', 'output' );
}

/**
* Get Ice versions ( str & int ).
*/
if ( function_exists( 'Ice_stringversion' ) ) {

	// icePHP 3.3 / 3.4
	define( 'PMA_ICE_STR', Ice_stringversion() );

} elseif ( defined( 'ICE_STRING_VERSION' ) ) {

	// icePHP 3.2
	define( 'PMA_ICE_STR', ICE_STRING_VERSION );

} else {
	define( 'PMA_ICE_STR', 'Not found' );
}

if ( function_exists( 'Ice_intversion' ) ) {

	// icePHP 3.3 / 3.4
	define( 'PMA_ICE_INT', Ice_intversion() );

} elseif ( defined( 'ICE_INT_VERSION' ) ) {

	// icePHP 3.2
	define( 'PMA_ICE_INT', ICE_INT_VERSION );

} else {
	define( 'PMA_ICE_INT', 0 );
}

// Includes functions
require 'main/functions/misc.php';
require 'main/functions/files.php';
require 'main/functions/messages.php';
require 'main/functions/PMA.php';

// Init PMA object.
$PMA = PMA::instance();

$PMA->messages = array();

$PMA->db = PMA_db::instance();
$PMA->config = PMA_config::instance();

if ( PMA_MODE !== 'ext_viewer' ) {

	$PMA->bans = PMA_bans::instance();
	$PMA->bans->check_current_user();

	$PMA->cookie = PMA_cookie::instance();
	$PMA->session = PMA_session::instance();
	$PMA->profiles = PMA_profiles::instance();
	$PMA->user = PMA_user::instance();
}

if ( PMA_DEBUG > 0 ) {
	require 'main/functions/debug.php';
}

// Ice 3.4 workaround
if ( PMA_ICE_INT > 30400 ) {
	require 'main/include/ice34.php';
}

?>
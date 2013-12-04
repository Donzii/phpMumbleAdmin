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
* Miscellaneous functions.
*/

function check_ip( $str ) {

	if ( check_ipv4( $str ) ) {
		return TRUE;
	}

	if ( check_ipv6( $str ) ) {
		return TRUE;
	}

	return FALSE;
}

function check_ipv4( $str ) {

	$regex_ipv4 = '/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/';

	if ( preg_match( $regex_ipv4, $str ) === 1 ) {
		return TRUE;
	}

	return FALSE;
}

function check_ipv6( $str ) {

	if ( $str === '::' ) {
		return TRUE;
	}

	$regex_ipv6 = '/^((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))$/';

	if ( preg_match( $regex_ipv6, $str ) === 1 ) {
		return TRUE;
	}

	return FALSE;
}

/**
* @function check_port()
*
* @return bool
*/
function check_port( $port ) {

	if ( ! is_int( $port ) ) {

		if ( is_string( $port ) && ctype_digit( $port ) ) {

			(int) $port;

		} else {
			return FALSE;
		}
	}

	if ( $port >= 0 && $port <= 65535 ) {
		return TRUE;
	}

	return FALSE;
}

// function check_email( $str ) {
//
// 	$regex_email = '/^[a-z0-9A-Z._-]+@[a-z0-9A-Z.-]{2,}[.][a-zA-Z]{2,4}$/';
//
// 	if ( preg_match( $regex_email, $str ) === 1 ) {
// 		return TRUE;
// 	}
//
// 	return FALSE;
// }


/**
 * @function crypt_pw
 *
 * Crypt a password
 *
 * default algorithm: BLOWFISH
 * If blowfish is not configured for the system: MD5
 * else: sytem default crypt algorithm
 *
 * @return string
 */
function crypt_pw( $pw ) {

	if ( defined( 'CRYPT_BLOWFISH' ) && CRYPT_BLOWFISH === 1 ) {
		return crypt( $pw, '$2a$08$'.gen_random_chars( 22 ).'$' );
	}

	// MD5
	if ( defined( 'CRYPT_MD5' ) && CRYPT_MD5 === 1 ) {
		return crypt( $pw, '$1$'.gen_random_chars( 22 ).'$' );
	}

	// Use the default system hash.
	return crypt( $pw );
}

/**
 * @function check_crypted_pw
 *
 * Check a password with crypt
 *
 * @return Bool
 */
function check_crypted_pw( $pw, $crypted ) {

	if ( crypt( $pw, $crypted ) === $crypted ) {
		return TRUE;
	}

	return FALSE;
}

/**
 * @function confirm_new_pw()
 *
 * Check if a new password is not empty and match with the confirm field.
 *
 * @return Bool
 */
function confirm_new_pw( $pw, $confirm_pw ) {

	if ( $pw !== '' && $pw === $confirm_pw ) {
		return TRUE;
	}

	return FALSE;
}

/**
* @function sort_array_by
*
* Sort a multi-dimensionnal array with one of it's sub key
*
* example:
* $array[1] = array( 'name' => 'foo' , 'profile' = 'foo' );
* $array[2] = array( 'name' => 'bar' , 'profile' = 'bar' );
* $array[3] = array( 'name' => 'foobar' , 'profile' = 'foobar' );
*/
function sort_array_by( &$array, $key, $key2 = '' ) {

	if ( $key2 === '' ) {
		$code = "return strNatCaseCmp( \$a['$key'], \$b['$key'] );";
	} else {
		$code = "if ( \$a['$key'] === \$b['$key'] ) return strNatCaseCmp( \$a['$key2'], \$b['$key2'] ); else return strNatCaseCmp( \$a['$key'], \$b['$key'] );";
	}

 	uasort( $array, create_function( '$a,$b', $code ) );
}

/**
* @function in_istring - in string insensitive
*
* @return bool
*/
function in_istring( $haystack, $needle ) {
	return ( FALSE !== stripos( $haystack, $needle ) );
}

/**
* @function in_string - in string sensitive
*
* @return bool
*/
function in_string( $haystack, $needle ) {
	return ( FALSE !== strpos( $haystack, $needle ) );
}


/**
* @function reindex_array_keys
*
* Reindex all keys of an array
*/
function reindex_array_keys( &$array ) {

	$reindex = $array;

	$array = array();

	foreach ( $reindex as $value ) {
		$array[] = $value;
	}
}

function sort_obj_by_names( $a, $b ) {
	return strCaseCmp( $a->name, $b->name );
}

/**
* @function url_to_HTML
*
* Return a HTTP URL to HTML format
* example : http://www.example.com
* return : <a href="http://www.example.com">http://www.example.com</a>
*/
function url_to_HTML( $str ) {
	return preg_replace( '/https?:\/\/[\pL\pN\-\.!~?&=+\*\'"(),\/]+/', '<a href="$0">$0</a>', $str );
}

/**
* @function replace_eol
*
* Replace end of line by a custom string.
* By default, a space
*
* @return string
*/
function replace_eol( $str, $replace = ' ' ) {
	return str_replace( array( "\n\r", "\r\n", "\n", "\r", "\0" ), $replace, $str );
}

// Transform ipv4 & ipv6 decimal array to string
function ip_dec_to_str( $array ) {

	if ( ! is_array( $array ) OR count( $array ) !== 16 ) {
		return 'Invalid IP';
	}

	// ipv4
	if (
		$array[0] === 0 && $array[1] === 0 && $array[2] === 0
		&& $array[3] === 0 && $array[4] === 0 && $array[5] === 0
		&& $array[6] === 0 && $array[7] === 0 && $array[8] === 0
		&& $array[9] === 0 && $array[10] == 255 && $array[11] === 255
	) {

		$retval['type'] = 'ipv4';
		$retval['ip'] = $array[12].'.'.$array[13].'.'.$array[14].'.'.$array[15];

	// ipv6
	} else {

		$retval['type'] = 'ipv6';

		$i = 0;
		$hex = '';
		$ipv6 = array();

		foreach ( $array as $dec ) {

			// Add missing zeros.
			$hex .= zero_pad( dechex( $dec ), 2 );

			++$i;

			if ( $i === 2 ) {

				$ipv6[] = $hex;
				// Reset
				$i = 0;
				$hex = '';
			}
		}

		$str = join( ':', $ipv6 );
		$retval['ip'] = ipv6_str_compress( $str );
	}

	return $retval;
}

function ipv4_str_to_dec( $str ) {

	$e = explode( '.', $str );

	return array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 255, 255, (int)$e[0], (int)$e[1], (int)$e[2], (int)$e[3] );
}

// transform ipv4 mask to ipv6.
// ipv4 range is 1-32, so in a ipv6 format, the mask can be only 96-128.
function ip_mask_4to6( $bits ) {
	return 128 - ( 32 - $bits );
}

function ip_mask_6to4( $bits ) {
	return 32 - ( 128 - $bits );
}

// Transform an ipv6 string addr into a decimal array for ICE
function ipv6_str_to_dec( $str ) {

	if ( $str === '::' ) {
		$str = '0:0:0:0:0:0:0:0';
	}

	// uncompress ipv6
	if ( FALSE !== strpos( $str, '::' ) ) {
		list( $start, $end ) = explode( '::', $str );
		$c = 8 - count( explode( ':', $start ) ) - count( explode( ':', $end ) );
		$str = $start.':'.str_repeat( '0000:', $c ).$end;
	}

	// add missing zeros: 1 => 0001,  20 => 0020, 300 => 0300.
	$hex = '';
	$exp = explode( ':', $str );

	foreach( $exp as $key => &$val ) {
		$hex .= zero_pad( $val, 4 );
	}

	// hexa to decimal
	$array = str_split( $hex, 2 );

	foreach ( $array as $key => $hex ) {
		$array[ $key ] = hexdec( $hex );
	}

	return $array;
}

// ipv6 string addr compression
function ipv6_str_compress( $str ) {

	// Already compressed
	if ( FALSE !== strpos( $str, '::' ) ) {
		return $str;
	}

	$str = ':'.$str.':';

	// remove zeros: 0001 => 1,  0020 => 20, 0300 => 300.
	$str = preg_replace( '/:00/', ':', $str );
	$str = preg_replace( '/:0/', ':', $str );

	preg_match_all( '/(:0)+/', $str, $matchs );

	if ( count( $matchs[0] ) > 0 ) {

		foreach( $matchs[0] as $match ) {

			// ":0:0:0:0" to "::"
			if ( strlen( $match ) >= 4 ) {

				$str = str_replace( $match, ':', $str );
				// One compression authorized ( RFC ).
				break;
			}
		}
	}

	// Remove first ":" if it's not a compression ( example "::1" )
	if ( substr( $str, 0, 2 ) !== '::' ) {
		$str = substr( $str, 1 );
	}

	// Remove last ":" if it's not a compression ( example "Fe80::" )
	if ( substr( $str, -2 ) !== '::' ) {
		$str = substr( $str, 0, -1 );
	}

	return $str;
}

// Add multiple 0 at the begining of the string.
// @limit max lenght of the string.
function zero_pad( $str, $limit ) {

	$len = strlen( $str );

	if ( $len < $limit ) {
		$str = str_repeat( 0, $limit - $len ).$str;
	}

	return $str;
}

/**
 * @function convert_size
 *
 * Transforme to human readable a size
 *
 * @return string
 */
function convert_size( $size, $unit = 'bit' ) {

	$base = 1024;

	switch( $unit ) {

		case 'bit':
			$symbole = 'b';
			break;

		case 'byte':
			$symbole = 'B';
			break;

		case 'octet':
			$symbole = 'o';
			break;
	}

	$coef = array( '', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y' );

	if ( $size > 0 ) {
		return @round( $size / pow( $base, ( $i = floor( log( $size, $base ) ) ) ), 2 ).' '.$coef[ $i ].$symbole;
	} else {
		return '0';
	}
}

// Decompose a bitmask count and return it in an array.
function bitmask_decompose( $mask ) {

	$a = array();

	while ( $mask > 0 ) {

		for( $i = 0, $n = 0; $i <= $mask; $i = 1 * pow( 2, $n ), $n++ ) {
			$end = $i;
		}

		$a[] = $end;
		$mask = $mask - $end;
	}

	sort( $a );

	return $a;
}

function bitmask_count( $array ) {

	$addition = 0;

	if ( ! empty( $array ) ) {

		foreach( $array as $key => $nb ) {
			$addition += $nb;
		}
	}

	return (int)$addition;
}

// Convert an array of decimal to it's character value and return it in a single string.
function array_dec_to_chars( $array ) {

	$str = '';

	foreach ( $array as $dec ) {
		$str .= chr( $dec );
	}

	return $str;
}

/**
 * @function gen_random_chars
 *
 * Generate random characters
 *
 * @param int $len
 * @param bool $alpha_num_only
 *
 * @return string
 */
function gen_random_chars( $len, $alpha_num_only = TRUE ) {

	// Initialize the random generator with a seed
	srand( ( double ) microtime() * 1000000 );

	$len = (int) $len;

	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

	if ( $alpha_num_only === FALSE ) {
		$chars .= '!&@][=}{+$*%?';
	}

	$str = '';

	for( $i = 0; $i < $len; ++$i ) {
		$str .= $chars[ rand() % strlen( $chars ) ];
	}

	return $str;
}

/**
 * @function scan_dir
 *
 * Return an array of all file and dir.
 *
 * @param string $dir
 *
 * @return array
 */
function scan_dir( $dir ) {

	$a = array();

	if ( ! is_dir( $dir ) OR ! is_readable( $dir ) ) {
		return $a;
	}

	$opendir = opendir( $dir );

	if ( ! is_resource( $opendir ) ) {
		return $a;
	}

	while ( FALSE !== ( $entry = readdir( $opendir ) ) ) {

		if ( $entry !== '.' && $entry !== '..' ) {
			$a[] = $entry;
		}
	}

	closedir( $opendir );

	natcasesort( $a );

	return $a;
}

/**
* Setup timezone
*
* date_default_timezone_set() comes with PHP 5.1 ( like date_default_timezone_get() )
*
* @param string $tz - valid php timezone
*/
function set_timezone( $tz ) {

	if ( function_exists( 'date_default_timezone_set' ) ) {
		@date_default_timezone_set( $tz );
	}
}

/**
* htmlentities helper
*
* Also htmlentities ( or htmlspecialchars ) do not return space code, so include an option to do it.
*
* In a textarea, space code produce a bug so it's possible to disable it.
*
* @param $str - string to convert
* @param $encode_space - bool
*
* @return string
*/
function html_encode( $str, $encode_space = TRUE ) {

 	$str = htmlentities( $str, ENT_QUOTES, 'UTF-8' );

	if ( $encode_space === TRUE ) {
		$str = str_replace( ' ', '&nbsp;', $str );
	}

	return $str;
}

/**
* Cut too long string.
*
* @return string
*/
function cut_long_str( $str, $maxlen, $cut_str = '...' ) {

	$strlen = strlen( $str );

	if ( $strlen > $maxlen ) {

		$sub = $maxlen - $strlen;

		$str = substr( $str, 0, $sub ).$cut_str;
	}

	return $str;
}

/**
* array_diff() transform all value to string.
* We require our -strict- function.
*
* @return string
*/
function array_diff_strict( $original, $compare ) {

	$diff = array();

	foreach( $original as $key => $value ) {

		if ( ! isset( $compare[ $key ] ) ) {
			$diff[ $key ] = $value;
			continue;
		}

		if ( $compare[ $key ] !== $value ) {
			$diff[ $key ] = $value;
		}
	}

	return $diff;
}

?>

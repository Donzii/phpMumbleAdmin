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
* Ice queries stats ( current and total of duration and queries ).
*/
class PMA_ice_queries_stats {

	// Current ice methode name
	private static $function = '';

	// Current ice methode timestamp start
	private static $start = 0;

	// Total count of queries
	private static $count = 0;

	// Total duration of queries
	private static $duration = 0;

	// Return time difference.
	private static function diff( $start ) {
		return microtime( TRUE ) - $start;
	}

	static function start( $function ) {

		self::$function = $function;
		self::$start = microtime( TRUE );
		++self::$count;
	}

	static function stop() {

		$diff = self::diff( self::$start );
		self::$duration += $diff;

		msg_debug( 'ice query #'.self::$count.' : <b>'.self::$function.'</b> ( '.round( $diff, 5 ).' s )', 3 );
	}

	static function get_global_stats() {
		return array( self::$count, round( self::$duration, 3 ) );
	}
}

?>
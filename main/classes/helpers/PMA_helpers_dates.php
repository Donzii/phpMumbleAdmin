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
* Dates and times functions.
*/

class PMA_helpers_dates {

	/**
	* Date with time.
	*
	* @param $ts - unix timestamp.
	* @param $options - PMA format options.
	*
	* @return string
	*/
	static function complet( $ts, $options = NULL ) {

		global $PMA;

		// Separation string betwin date and time.
		$separator = ' - ';

		$options = pma_parse_options( $options );

		if ( isset( $options['conf'] ) && $options['conf'] === 'default' ) {

			$date = $PMA->config->get( 'default_date' );
			$time = $PMA->config->get( 'default_time' );

		} else {
			$date = $PMA->cookie->get( 'date' );
			$time = $PMA->cookie->get( 'time' );
		}

		if ( isset( $options['separator'] ) ) {
			$separator = $options['separator'];
		}

		return strftime( $date, $ts ).$separator.date( $time, $ts );
	}

	/**
	* Contruct uptime from an unix timestamp
	*
	* @param $ts - unix timestamp.
	* @param $format - use a custom uptime format instead of the cookie.
	*
	* @return string
	*/
	static function uptime( $ts, $format = NULL ) {

		global $TEXT;

		// $format = 1 : 250 days 23:59:59
		// $format = 2 : 250 days 23:59
		// $format = 3 : 250 days

		if ( $format === NULL ) {
			$format = PMA_cookie::instance()->get( 'uptime' );
		}

		$str = '';

		$days = (int) floor( $ts / 86400 );
		$ts %= 86400;

		$hours = sprintf( '%02d', floor( $ts / 3600 ) );
		$ts %= 3600;

		$mins = sprintf( '%02d', floor( $ts / 60 ) );
		$secs = $ts % 60;

		$secs = sprintf( '%02d', $secs );

		if ( $days > 0 ) {

			if ( $days === 1 ) {

				$str = $days.' '.$TEXT['day'].' ';

			} else {
				$str = $days.' '.$TEXT['days'].' ';
			}
		}

		if ( $format === 1 ) {

			$str .= $hours.'h'.$mins.'m'.$secs.'s';

		} elseif ( $format === 2 OR ( $format === 3 && $days === 0 ) ) {

			$str .= $hours.'h'.$mins.'m';
		}

		return strToLower( $str );
	}

	/**
	* Uptime with a nice "started at" on mouse over.
	*
	* @param $ts - unix timestamp.
	*
	* @return string
	*/
	static function started_at( $ts ) {

		global $TEXT;

		$uptime = self::uptime( $ts );

		$start_ts = PMA_TIME - $ts;

		$date = strftime( PMA_cookie::instance()->get( 'date' ), $start_ts );
		$time = date( PMA_cookie::instance()->get( 'time' ), $start_ts );

		$txt = sprintf( $TEXT['started_at'], $date, $time );

		return '<span class="help" title="'.$txt.'">'.$uptime.'</span>';
	}

	/**
	* Modify a datetime ( time format for database like mysql, sqlite )
	* to unix timestamp.
	*
	* @param $str - datetime string
	*
	* @return int - unix timestamp
	*/
	static function datetime_to_timestamp( $str ) {

		// ie: 2012-04-07 17:25:03
		$regex_datetime =  '/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/';

		if ( preg_match( $regex_datetime, $str ) !== 1 ) {
			return '';
		}

		list( $date, $time ) = explode( ' ', $str );
		list( $year, $month, $day ) = explode( '-', $date );
		list( $hour, $minute, $second ) = explode( ':', $time );

		return gmmktime( (int)$hour, (int)$minute, (int)$second, (int)$month, (int)$day, (int)$year );
	}
}

?>
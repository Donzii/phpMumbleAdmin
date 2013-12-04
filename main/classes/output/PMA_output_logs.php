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

class PMA_output_logs extends PMA_output {

	private $date_format;

	private $DAY;
	private $MONTH;
	private $YEAR;

	function __construct( $date_format ) {
		$this->date_format = $date_format;
	}

	/**
	* Return a HTML div if the current log day is different with the last checked log.
	*
	* @param $timestamp - timestamp of the current log.
	*
	* @return string
	*/
	function day( $timestamp ) {

		list( $day, $month, $year ) = explode( '/', date( 'd/m/Y', $timestamp ) );

		// Check if current day has changed with the last log entry.
		if (
			is_null( $this->DAY )
			OR $day !== $this->DAY
			OR $month !== $this->MONTH
			OR $year !== $this->YEAR
		) {
			// The day is different.
			$output = '<div class="Ldate">'.strftime( $this->date_format, $timestamp ).'</div>'.EOL;
		} else {
			$output = '';
		}

		// Remember the log date for next log.
		$this->DAY = $day;
		$this->MONTH = $month;
		$this->YEAR = $year;

		return $output;
	}
}

?>
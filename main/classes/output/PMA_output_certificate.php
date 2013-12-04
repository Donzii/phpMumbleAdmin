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

class PMA_output_certificate {

	static private function error( $message ) {
		return '<div class="empty">Error : '.$message.'</div>'.EOL;
	}

	/**
	* Certificate HTML box
	*
	* @return string
	*/
	static function get( $certificate ) {

		if ( ! function_exists( 'openssl_x509_parse' ) ) {
			return self::error( 'php-openssl module seems not installed' );
		}

		$parse = openssl_x509_parse( $certificate, FALSE );

		if ( ! is_array( $parse ) ) {
			return self::error( 'invalid certificate' );
		}

		// remove duplicate / useless entries.
		unset( $parse['name'], $parse['purposes'], $parse['validFrom'], $parse['validTo'] );

		$output = '';

		foreach ( $parse as $key => $value ) {

			if ( is_array( $value ) ) {

				if ( empty( $value ) ) {
					continue;
				}

				ksort( $value );

				$output .= '<div style="margin: 10px 0px;">'.EOL;
				$output .= '<b>'.strToUpper( $key ).':</b><br>'.EOL;

				foreach ( $value as $k => $v ) {

					$output .= $k.': <b>'.$v.'</b><br>'.EOL;
				}

				$output .= '</div>'.EOL;

			} else {

				$output .= '<div style="margin: 10px 0px;">';

				if ( $key === 'validFrom_time_t' OR $key === 'validTo_time_t' ) {

					if ( $key === 'validFrom_time_t' ) {
						$output .= 'Valid from: ';
					} else {
						$output .= 'Valid to: ';
					}

					$output .= '<b>'.PMA_helpers_dates::complet( $value ).'</b>';

				} else {
					$output .= $key.': <b>'.$value.'</b>';
				}

				$output .= '</div>'.EOL;
			}
		}

		return $output;
	}
}

?>
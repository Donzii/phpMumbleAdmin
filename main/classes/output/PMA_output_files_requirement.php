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
* Check all files and dirs which require write acces and print result.
*/
class PMA_output_files_requirement {

	static function get( $files ) {

		$dirs[] = PMA_DIR_AVATARS;
		$dirs[] = PMA_DIR_CACHE;
		$dirs[] = PMA_DIR_CONFIG;
		$dirs[] = PMA_DIR_LOGS;
		$dirs[] = PMA_DIR_SESSIONS;
		$dirs[] = NULL;

		$files[] = NULL;
		$files[] = PMA_FILE_LOGS;

		// Title
		$output = '<div style="margin: 10px;"><span class="fill occ">Php write access required</span></div>'.EOL;

		// Directories
		foreach( $dirs as $dir ) {

			if ( $dir === NULL ) {
				$output .= self::blank();
				continue;
			}

			$output .=  self::path( $dir );

			if ( is_dir( $dir ) && is_writeable( $dir ) ) {

				$output .= self::success();

			} else {
				$output .= self::invalid();
			}

			$output .= '</div>'.EOL;
		}

		// Files
		foreach( $files as $file ) {

			if ( $file === NULL ) {
				$output .= self::blank();
				continue;
			}

			$output .=  self::path( $file );

			if ( check_file( $file ) ) {

				$output .= self::success();

			} else {
				$output .= self::invalid();
			}

			$output .= '</div>'.EOL;
		}

		return $output;
	}

	static private function blank() {
		return '<br>'.EOL;
	}

	static private function path( $text ) {
		return '<div><b>'.$text.'</b> : ';
	}

	static private function success() {
		return '<span class="safe">Good</span>';
	}

	static private function invalid() {
		return '<span class="unsafe">not writeable</span>';
	}
}

?>
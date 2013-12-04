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
* Write a ( multi-dimensional ) array into a file
*
* @return Bool
*/
class PMA_array_to_file {

	private $fp;

	private $file;

	private $compact;

	function __construct( $file, $array, $compact = FALSE ) {

		$this->file = $file;

		$this->compact = $compact;

		// w = Open for writing only; place the file pointer at the beginning of the file and truncate the file to zero length.
		// If the file does not exist, attempt to create it.
		$this->fp = @fopen( $this->file, 'wb' );

		return $this->write( $array );
	}

	private function debug( $message, $level = 1, $error = FALSE ) {
		msg_debug( '<span class="unsafe b">'.__class__ .'</span>-> '.$message, $level, $error );
	}

	/**
	* Core of the class
	*/
	private function write( $array ) {

		if ( $this->fp === FALSE ) {
			return FALSE;
		}

		fwrite( $this->fp, '<?php'.EOL.EOL );
		fwrite( $this->fp, 'if ( ! defined( \'PMA_STARTED\' ) ) { die( \'ILLEGAL: You cannot call this script directly !\' ); }'.EOL.EOL );

		foreach ( $array as $key => $value ) {

			fwrite( $this->fp, '$array' );

			$this->key_value( $key, $value );

			fwrite( $this->fp, EOL );
		}

		// End - check if no error occured during last fwrite
		// Memo: fwrite returns the number of bytes written, or FALSE on error
		if ( FALSE !== fwrite( $this->fp, EOL.EOL.'?>' ) ) {

			fclose( $this->fp );
			$this->debug( '( '.$this->file.' )' );
			return TRUE;

		} else {

			fclose( $this->fp );
			$this->debug( '( '.$this->file.' )', 1, TRUE );
			return FALSE;
		}
	}


	private function key_value( $key, $value, $tabs = NULL ) {

		if ( $tabs === NULL ) {
			$tabs = 0;
			$symb = '=';
			$end = ';';
		} else {
			$symb = '=>';
			$end = ',';
		}

		if ( ! is_int( $key ) ) {
			// String key
			$key = '\''.$key.'\'';
		}

		if ( $tabs === 0 ) {
			$key = '['.$key.']';
		}

		// Compact
		if ( $this->compact === TRUE ) {
			$tabulation = '';
			$EOL = '';
		} else {
			$tabulation = str_repeat( "\t", $tabs );
			$EOL = EOL;
			$symb = ' '.$symb.' ';
		}

		if ( is_array( $value ) ) {

			if ( empty( $value ) ) {

				fwrite( $this->fp, $tabulation.$key.$symb.'array()'.$end );

			} else {

				fwrite( $this->fp, $tabulation.$key.$symb.'array('.$EOL );

				foreach( $value as $key2 => $value2 ) {

					$this->key_value( $key2, $value2, $tabs+1 );

					fwrite( $this->fp, $EOL );
				}

				fwrite( $this->fp, $tabulation.')'.$end );
			}

		} else {

			if ( is_bool( $value ) ) {

				if ( $value ) {
					$value = 'TRUE';
				} else {
					$value = 'FALSE';
				}

			// Memo: transform a null value to an empty string.
			} elseif ( is_string( $value ) OR is_null( $value ) ) {

				$this->remove_invalid_str_chars( $value );

				$value = '\''.$value.'\'';
			}

			fwrite( $this->fp, $tabulation.$key.$symb.$value.$end );
		}
	}

	private function remove_invalid_str_chars( &$str ) {

		// Add an anti-slash for single quote ( ' )
		$str = str_replace( '\'', '\\\'', $str );

		// replace EOL by a space
		$str = replace_eol( $str );

		return $str;
	}
}

?>
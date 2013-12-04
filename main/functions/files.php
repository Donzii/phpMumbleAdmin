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
* @function check_file
*
* Check if a file have write access.
* If the file doesn't exists, try to create it.
*
* @return bool
*/
function check_file( $file ) {

	if ( is_file( $file ) && is_writeable( $file ) ) {
		return TRUE;
	}

	if ( ! file_exists( $file ) ) {

		$fopen = @fopen( $file, 'wb' );

		if ( $fopen === FALSE ) {
			return FALSE;
		}

		fclose( $fopen );
		return TRUE;
	}

	return FALSE;
}

/**
* Get an array in file
*
* @return array
*/
function get_array_file( $file ) {

	$array = array();
	include $file;
	return $array;
}

/**
* Write in log file.
*/
function write_log( $level, $message, $file = PMA_FILE_LOGS ) {

	if ( ! check_file( $file ) ) {
		return;
	}

	// PMA print a human readable time inside the log file
	// Use default timezone for it.
	set_timezone( PMA_config::instance()->get( 'default_timezone' ) );

	// 'a' = Open for writing only; place the file pointer at the end of the file.
	// If the file does not exist, attempt to create it.
	$fopen = fopen( $file, 'ab' );

	// [0]timestamp ::: [1]localtime ::: [2]logLvl ::: [3]ip ::: [4]txt ::: [5]EOL
	fwrite( $fopen, PMA_TIME.':::'.date( 'H:i:s - Y-m-d' ) . ':::['.$level.']:::'.PMA_USER_IP.':::'.$message.':::'.EOL );
	fclose( $fopen );

	// Back to PMA user timezone
	set_timezone( PMA_cookie::instance()->get( 'timezone' ) );
}

?>
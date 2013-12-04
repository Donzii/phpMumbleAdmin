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
* Debug functions.
*/
function pprint( $datas ) {

	echo '<pre>'.EOL;
	print_r( $datas ).EOL;
	echo '</pre>'.EOL;
}

function pdump( $datas ) {

	echo '<pre>'.EOL;
	var_dump( $datas ).EOL;
	echo '</pre>'.EOL;
}

function ptrace() {

	echo '<pre>'.EOL;
	print_r( debug_backtrace() ).EOL;
	echo '</pre>'.EOL;
}

function mark() {
	echo '<div style="background: red; padding: 0px 20px;">Mark</div>'.EOL.EOL;
}

function get_parent_function() {

	$backtrace = debug_backtrace();

	// Memo:
	// key "0" is this function
	// key "1" is the function calling this.

	if ( isset( $backtrace[2]['class'] ) ) {
		$class = $backtrace[2]['class'].$backtrace[2]['type'];
	} else {
		$class = '';
	}

	return $class.$backtrace[2]['function'];
}

?>
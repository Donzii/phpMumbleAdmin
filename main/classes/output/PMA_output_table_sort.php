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

class PMA_output_table_sort {

	// Table id ( for session caching ).
	private $id;

	// Default array key to sort if none set
	private $default_key;

	// Array key to sort
	private $sort_key;

	// Boolean: reverse or not the array.
	private $reverse;

	function __construct( $id, $default_key ) {

		$this->id = $id;

		$this->default_key = $default_key;

		$this->setup_params();
		$this->update_session();
	}

	/**
	* Get current table sort key and reverse.
	*/
	private function setup_params() {

		if ( isset( $_GET['sort'] ) ) {

			$this->sort_key = $_GET['sort'];
			$this->reverse = isset( $_GET['rev'] );

			return;
		}

		// Else get session settings:
		if ( isset( $_SESSION['sort'][ $this->id ] ) ) {

			$session = $_SESSION['sort'][ $this->id ];

		// Default
		} else {

			$session = $this->default_key;
		}

		if ( $session[0] === '*' ) {
			$this->reverse = TRUE;
			$this->sort_key = substr( $session, 1 );
		} else {
			$this->reverse = FALSE;
			$this->sort_key = $session;
		}
	}

	/**
	* Keep in session current table settings.
	*/
	private function update_session() {

		if ( $this->reverse ) {
			$session = '*'.$this->sort_key;
		} else {
			$session = $this->sort_key;
		}

		$_SESSION['sort'][ $this->id ] = $session;
	}

	/**
	* Sort the table
	*
	* @param $force - Force to order the default key
	* Example:
	* The table "password request" sort by default the "id" key.
	* But PMA override the default key for "end".
	* Without $force = TRUE, the key "id" is still the default key
	*/
	function order( &$table, $force = FALSE ) {

		if ( $this->sort_key !== $this->default_key OR $force === TRUE ) {
			sort_array_by( $table, $this->sort_key, $this->default_key );
		}

		if ( $this->reverse ) {
			$table = array_reverse( $table, TRUE );
		}
	}

	/**
	* Construct sort link url
	*
	* @Return string
	*/
	function url( $key, $txt, $option = NULL ) {

		global $TEXT;

		if ( $this->sort_key !== $key ) {
			return '<a href="?sort='.$key.'" title="'.$TEXT['sort_by'].'">'.$txt.'</a>';
		}

		if ( $this->reverse ) {
			$img = HTML::img( IMG_ARROW_UP );
		} else {
			$img = HTML::img( IMG_ARROW_DOWN );
		}

		if ( $option === 'short' ) {
			$txtimg = $img.'</a>';
		} else {
			$txtimg = $txt.'</a>'.$img;
		}

		if ( ! $this->reverse ) {
			return '<a href="?sort='.$key.'&amp;rev">'.$txtimg;
		} else {
			return '<a href="?sort='.$key.'">'.$txtimg;
		}
	}
}

?>
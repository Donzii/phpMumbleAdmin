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
* Abstract class for stored datas manipulation
*/
abstract class PMA_abs_datas {

	/**
	* Datas key for get_datas() save_datas() methods
	*/
	protected $datas_key;

	/**
	* All datas are stored in this var.
	*/
	protected $datas;

	protected function get_datas() {
		$this->datas = PMA_db::instance()->get( $this->datas_key );
	}

	protected function save_datas() {
		PMA_db::instance()->queue( $this->datas_key, $this->datas );
	}

	/**
	* Rename all keys with id
	*/
	protected function id_to_key() {

		$id_to_key = array();

		foreach( $this->datas as $data ) {
			$id_to_key[ $data['id'] ] = $data;
		}

		$this->datas = $id_to_key;

		// Sort array by id.
		ksort( $this->datas );
	}

	/**
	* Check for a unique id
	*
	* @return bool
	*/
	protected function is_unique_id( $id ) {

		foreach( $this->datas as $array ) {

			if ( $array['id'] === $id ) {
				return FALSE;
			}
		}

		return TRUE;
	}

	function delete( $key ) {

		if ( ! isset( $this->datas[ $key ] ) ) {
			return;
		}

		unset( $this->datas[ $key ] );
		$this->save_datas();
	}

// For the futur
// 	function delete_id( $id ) {

// 		foreach( $this->datas as $key => $array ) {

// 			if ( $array['id'] === $id ) {
// 				$this->delete( $key );
// 			}
// 		}
// 	}

	function get_all() {
		return $this->datas;
	}
}

?>
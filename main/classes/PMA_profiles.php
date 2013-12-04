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

class PMA_profiles extends PMA_abs_datas {

	protected $datas_key = 'profiles';

	private $publics;

	/**
	* Singleton
	*/
	static function instance() {

		static $_instance;

		if ( is_null( $_instance ) ) {
			return $_instance = new self();
		}

		return $_instance;
	}

	private function __construct() {

		$this->get_datas();

		// empty profile list is not allowed, add default.
		if ( empty( $this->datas ) ) {

			$this->add( 'Default' );
			$this->save_datas();
		}

		$this->init_public();
	}

	private function init_public() {

		$this->publics = array();

		foreach( $this->datas as $profile ) {

			if ( $profile['public'] === TRUE ) {
				$this->publics[ $profile['id'] ] = $profile;
			}
		}
	}

	function save_datas() {

		$this->id_to_key();
		parent::save_datas();
	}

	function add( $name ) {

		if ( empty( $this->datas ) ) {

			$id = 1;
			$public = TRUE;

		} else {

			$end = end( $this->datas );

			$id = $end['id'] + 1;
			$public = FALSE;
		}

		$this->datas[ $id ] = array(
			'id' => $id,
			'name' => $name,
			'public' => $public,
			'host' => '127.0.0.1',
			'port' => '6502',
			'timeout' => 10,
			'secret' => '',
			'slice_profile' => '',
			'slice_php' => '',
			'http-addr' => ''
		);

		$this->save_datas();

		return $id;
	}

	function modify( $array ) {

		$id = $array['id'];

		if ( isset( $this->datas[ $id ] ) ) {

			$this->datas[ $id ] = $array;
			$this->save_datas();
		}
	}

	function delete( $id ) {

		// Zero profile is not allowed
		if ( isset( $this->datas[ $id ] ) && count( $this->datas ) > 1 ) {

			unset( $this->datas[ $id ] );
			$this->save_datas();
		}
	}

	function get( $id ) {

		if ( isset( $this->datas[ $id ] ) ) {
			return $this->datas[ $id ];
		}
	}

	function get_name( $id ) {

		if ( isset( $this->datas[ $id ] ) ) {
			return $this->datas[ $id ]['name'];
		}
	}

	function get_all_ids( $list = '' ) {

		if ( $list !== 'publics' ) {
			$list = 'datas';
		}

		$arr = array();

		foreach( $this->$list as $array ) {
			$arr[ $array['id'] ] = $array['id'];
		}

		return $arr;
	}

	function get_first( $list = '' ) {

		if ( $list !== 'publics' ) {
			$list = 'datas';
		}

		foreach( $this->$list as $array ) {
			return $array;
		}
	}

	function get_all( $list = '' ) {

		if ( $list !== 'publics' ) {
			$list = 'datas';
		}

		return $this->$list;
	}

	function total( $list = '' ) {

		if ( $list !== 'publics' ) {
			$list = 'datas';
		}

		return count( $this->$list );
	}

	/**
	* Set current profile as invalid slice file.
	*/
	function set_as_invalid_slice_file( $id, $message ) {

		$name = $this->get_name( $id );

		$this->datas[ $id ]['invalid_slice_file'] = TRUE;
		$this->save_datas();

		write_log( 'PMA.error', 'Invalid slice definitions file. The Ice profile has been disabled. ( '.$id.'# '.$name.' ) => '.$message );
	}

	function remove_invalid_slice_file( $id ) {

		unset( $this->datas[ $id ]['invalid_slice_file'] );
		$this->save_datas();
	}
}

?>
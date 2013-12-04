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

class PMA_pw_requests extends PMA_abs_datas {

	/**
	* Nomber of characters a new id must have.
	*/
	const ID_CHARS = 50;

	protected $datas_key = 'pw_requests';

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
		$this->sanity();
	}

	private function sanity() {

		foreach ( $this->datas as $key => $array ) {

			// Remove invalid request
			if ( count( $array ) !== 10 ) {

				$this->delete( $key );
				continue;
			}

			// Remove too old request
			if ( PMA_TIME > $array['end'] ) {

				$this->delete( $key );
				continue;
			}

			// Remove request with invalid ICE profile
			if ( NULL === PMA_profiles::instance()->get( $array['profile_id'] ) ) {

				$this->delete( $key );
				continue;
			}
		}
	}

	/**
	* Add a request to the list
	*/
	function add( $array ) {

		$this->datas[] = $array;
		$this->save_datas();
	}

	/**
	* Get a request with it's id
	*/
	function get( $id ) {

		foreach( $this->datas as $array ) {

			if ( $array['id'] === $id ) {
				return $array;
			}
		}
	}

	/**
	* Delete identical requests found in the list.
	*/
	function delete_identical( $profile_id, $profile_host, $profile_port, $sid, $uid ) {

		foreach( $this->datas as $key => $array ) {

			if (
				$array['profile_id'] === $profile_id
				&& $array['profile_host'] === $profile_host
				&& $array['profile_port'] === $profile_port
				&& $array['sid'] === $sid
				&& $array['uid'] === $uid
			) {
				$this->delete( $key );
			}
		}
	}

	/**
	* Return an unique ID.
	*/
	function get_unique_id() {

		$id = gen_random_chars( self::ID_CHARS );

		if ( $this->is_unique_id( $id ) ) {
			return $id;
		}

		return $this->get_unique_id();
	}
}

?>
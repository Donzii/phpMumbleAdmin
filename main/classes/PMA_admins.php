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

class PMA_admins extends PMA_abs_datas {

	protected $datas_key = 'admins';

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
	}

	function save_datas() {
		$this->id_to_key();
		parent::save_datas();
	}

	protected function id_to_key() {

		// Rename keys with id
		$id_to_key = array();

		foreach( $this->datas as $admin ) {

			// Sort access by profile.
			ksort( $admin['access'] );

			$id_to_key[ $admin['id'] ] = $admin;
		}

		$this->datas = $id_to_key;

		// Sort admins by id.
		ksort( $this->datas );
	}

	/**
	* Validate admin login characters
	*
	* @return bool
	*/
	function validate_login( $str ) {
		return ( is_string( $str ) && ctype_alnum( $str ) );
	}

	/**
	* Check if an admin login exists already
	*
	* @return bool
	*/
	function login_exists( $login ) {

		$SA_login = PMA_config::instance()->get( 'SA_login' );

		// Check SA
		if ( strToLower( $login ) === strToLower( $SA_login ) ) {
			return TRUE;
		}

		// Check all admins
		foreach( $this->datas as $array ) {

			if ( strToLower( $login ) === strToLower( $array['login'] ) ) {
				return TRUE;
			}
		}

		return FALSE;
	}

	function add( $login, $pw, $email, $name, $class ) {

		if ( ! empty( $this->datas ) ) {

			$end = end( $this->datas );
			$id = $end['id'] + 1;

		} else {
			$id = 1;
		}

		$this->datas[ $id ] = array(

			'id' => $id,
			'login' => $login,
			'pw' => crypt_pw( $pw ),
			'created' => PMA_TIME,
			'email' => $email,
			'name' => $name,
			'class' => (int) $class,
			'last_conn' => 0,
			'access' => array(),
		);

		$this->save_datas();

		return $id;
	}

	/**
	* Get admin registration
	*
	* @param $id integer - admin id
	*/
	function get( $id ) {

		if ( isset( $this->datas[ $id ] ) ) {
			return $this->datas[ $id ];
		}
	}

	/**
	* Modify admin registration
	*
	* @param $array array - admin registration
	*/
	function modify( $array ) {

		$id = $array['id'];

		if ( isset( $this->datas[ $id ] ) ) {

			$this->datas[ $id ] = $array;
			$this->save_datas();
		}
	}

	/**
	* Authenticate an admin
	*
	* @return array - admin registration on succes
	* @return interger - 1 invalid pw, 2 admin not found
	*/
	function auth( $login, $pw ) {

		foreach( $this->datas as $array ) {

			if ( strToLower( $login ) === strToLower( $array['login'] ) ) {

				if ( check_crypted_pw( $pw, $array['pw'] ) ) {
					return $array;
				} else {
					return 1;
				}
			}
		}

		return 2;
	}

	/**
	* Remove all admins access for a profile
	*
	* @param $profile integer - profile id
	*/
	function del_profile_access( $profile ) {

		$modified = FALSE;

		foreach( $this->datas as $key => $array ) {

			if ( isset( $array['access'][ $profile ] ) ) {

				unset( $this->datas[ $key ]['access'][ $profile ] );
				$modified = TRUE;
			}
		}

		if ( $modified ) {
			$this->save_datas();
		}
	}

	/**
	* Remove all admins access to a server id for a profile
	*
	* @param $profile integer - profile id
	* @param $sid integer - server id
	*/
	function del_sid_access( $profile, $sid ) {

		$sid = strval( $sid );

		$modified = FALSE;

		foreach( $this->datas as $key => $array ) {

			if ( ! isset( $array['access'][ $profile ] ) ) {
				continue;
			}

			$servers = explode( ';', $array['access'][ $profile ] );

			foreach( $servers as $k => $value ) {

				if ( $value === $sid ) {

					unset( $servers[ $k ] );
					$modified = TRUE;
				}
			}

			if ( empty( $servers ) ) {
				unset( $this->datas[ $key ]['access'][ $profile ] );
			} else {
				$this->datas[ $key ]['access'][ $profile ] = join( ';', $servers );
			}
		}

		if ( $modified ) {
			$this->save_datas();
		}
	}
}

?>
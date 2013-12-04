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

class PMA_whos_online extends PMA_abs_datas {

	protected $datas_key = 'whos_online';

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

		if ( PMA_INSTALL ) {
			return;
		}

		$this->get_datas();

		$this->sanity();
	}

	private function sanity() {

		$autologout = PMA_config::instance()->get( 'auto_logout' ) * 60;

		foreach( $this->datas as $key => $array ) {

			// Remove old entries
			if ( PMA_TIME > ( $array['last_activity'] + $autologout ) ) {

				$this->delete( $key );
			}
		}
	}

	function update_current_user() {

		$user = PMA_user::instance();

		$id = session_id();

		$update['sessid'] = $id;

		$update['class'] = $user->class;

		$update['classname'] = pma_class_name( $user->class );

		$update['login'] = $user->login;

		$update['current_ip'] = $_SESSION['current_ip'];

		$update['last_activity'] = $_SESSION['last_activity'];

		if ( $user->mumble_id !== NULL ) {

			$update['profile_id'] = $user->profile_id;
			$update['sid'] = $user->sid;
			$update['uid'] = $user->mumble_id;

		} else {

			$update['profile_id'] = '';
			$update['sid'] = '';
			$update['uid'] = '';
		}

		if ( isset( $_SESSION['proxy'] ) ) {
			$update['proxy'] = $_SESSION['proxy'];
		}

		$this->datas[ $id ] = $update;

		$this->save_datas();
	}
}

?>
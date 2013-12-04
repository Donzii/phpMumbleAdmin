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
* PMA bans
*/

class PMA_bans extends PMA_abs_datas {

	protected $datas_key = 'bans';

	private $_ip;

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

		$this->_ip = PMA_USER_IP;

		$this->get_datas();

		$this->sanity();
	}

	private function sanity() {

		foreach( $this->datas as $key => $array ) {

			// Remove invalid bans
			if ( count( $array ) !== 4 ) {

				$this->delete( $key );
				continue;
			}

			// Permanent ban, never remove it here.
			if ( $array['duration'] === 0 ) {
				continue;
			}

			// Remove bans which reached end of duration
			if ( PMA_TIME > ( $array['start'] + $array['duration'] ) ) {

				$this->delete( $key );
				continue;
			}
		}
	}

	/**
	* Check if current user is banned, kill script if true
	*/
	function check_current_user() {

		msg_debug( 'Checking if your ip address is banned', 2 );

		foreach( $this->datas as $key => $array ) {

			if ( $array['ip'] === $this->_ip ) {

				// Update datas before kill the script.
				$this->save_datas();

				pma_fatal_error( 'YOU ARE BANNED !!!' );
			}
		}

		msg_debug( '<span class="safe b">You can read this message</span>, good for you ! :D', 2 );
	}

	function add( $ip, $duration, $type ) {

		$this->datas[] = array(
			'ip' => $ip,
			'start' => PMA_TIME,
			'duration' => $duration,
			'type' => $type
		);

		$this->save_datas();
	}
}

?>
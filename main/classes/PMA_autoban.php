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

class PMA_autoban extends PMA_abs_datas {

	protected $datas_key = 'autoban_attempts';

	private $_ip;

	private $_attempts;
	private $_duration;
	private $_frame;

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

		$this->_attempts = PMA_config::instance()->get( 'autoban_attempts' );
		$this->_duration = PMA_config::instance()->get( 'autoban_duration' );
		$this->_frame = PMA_config::instance()->get( 'autoban_frame' );

		$this->get_datas();

		$this->sanity();
	}

	private function sanity() {

		foreach( $this->datas as $key => $array ) {

			// Remove invalid attempts
			if ( count( $array ) !== 4 ) {

				$this->delete( $key );
				continue;
			}

			// Remove too old attempts
			if ( PMA_TIME > $array['end'] ) {

				$this->delete( $key );
				continue;
			}
		}
	}

	private function add_new_attempt() {

		$this->datas[] = array(
			'ip' => $this->_ip,
			'start' => PMA_TIME,
			'end' => PMA_TIME + $this->_frame,
			'attempts' => 1
		);

		$this->save_datas();
	}

	/**
	* Autoban process
	*/
	function attempts() {

		$debug = __class__ .'->'. __function__ .'()';

		$bans = PMA_bans::instance();

		foreach( $this->datas as $key => $array ) {

			// Recent attempt found
			if ( $array['ip'] === $this->_ip ) {

				// Update count.
				++$array['attempts'];
				msg_debug( $debug.' : '.$array['attempts'].' / '.$this->_attempts, 3 );

				// Attempts count limit reached, ban the IP
				if ( $array['attempts'] > $this->_attempts ) {

					$bans->add( $this->_ip, $this->_duration, 'autoban' );
					write_log( 'autoBan.info', 'IP address has been auto-banned' );
					$this->delete( $key );

					// User has been banned.
					$bans->check_current_user();

				} else {
					// Attempt limit not reached, update it.
					$this->datas[ $key ] = $array;
				}

				$this->save_datas();
				return;
			}
		}

		// No recent attempt found, add new one.
		msg_debug( $debug.' : Adding new attempt', 3 );
		$this->add_new_attempt();
	}
}

?>
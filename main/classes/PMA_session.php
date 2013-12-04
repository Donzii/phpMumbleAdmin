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

class PMA_session {

	/**
	* Session directory
	*/
	private $sess_dir = PMA_DIR_SESSIONS;

	/**
	* Session cookie name
	*/
	private $sess_name = 'phpMumbleAdmin_session';

	/**
	* String key to get "sessions last check" datas
	*/
	private $datas_key = 'sessions_check';

	/**
	* Singleton
	*/
	static function instance() {

		static $_instance;

		if ( is_null( $_instance ) ) {
			$_instance = new self();
		}

		return $_instance;
	}

	private function __construct() {

		msg_debug( '<span class="warn b">Initializing</span> session...', 2 );

		$this->init();
		$this->remove_outdated_files();
		$this->startup();
		$this->sanity();
	}

	/**
	* Cache all messages in $_SESSION.
	*/
	function cache_messages() {
		$_SESSION['messages'] = PMA::instance()->messages;
	}

	/**
	* Flush all cached messages in $_SESSION
	*/
	private function flush_cached_messages() {

		if ( ! isset( $_SESSION['messages'] ) ) {
			return;
		}

		global $PMA;

		$PMA->messages = array_merge_recursive( $_SESSION['messages'], $PMA->messages );

		unset( $_SESSION['messages'] );
	}

	private function init() {

		if ( ! is_dir( $this->sess_dir ) OR ! is_writeable( $this->sess_dir ) ) {
			pma_fatal_error( $this->sess_dir.' directory is not writeable.' );
		}
	}

	/**
	* Remove outdated sessions files.
	*/
	private function remove_outdated_files() {

		$datas = PMA_db::instance();

		$last_check = $datas->get( $this->datas_key );

		// Sanity
		if ( ! is_int( $last_check['last'] ) ) {
			$datas->queue( $this->datas_key, array( 'last' => PMA_TIME ) );
			return;
		}

		// Sessions sanity every 15 secondes to avoid flood on files
		if ( ( PMA_TIME - $last_check['last'] ) < 15 ) {
			return;
		}

		// Update modification time of the "session last check" file
		$datas->queue( $this->datas_key, array( 'last' => PMA_TIME ) );

		$list = scan_dir( $this->sess_dir );
		$auto_logout = ( PMA_config::instance()->get( 'auto_logout' ) * 60 );

		foreach( $list as $file ) {

			if ( substr( $file, 0, 5 ) !== 'sess_' ) {
				continue;
			}

			$path = $this->sess_dir.$file;

			// Delete too old sessions
			if ( ( PMA_TIME - filemtime( $path ) ) > $auto_logout ) {
				unlink( $path );
			}
		}
	}

	/**
	* Start user session
	*/
	private function startup() {

		if ( ! defined( 'COOKIE_ACCEPTED' ) OR ! COOKIE_ACCEPTED ) {
			msg_debug( '<span class="unsafe b">You dont accept cookie, session didn\'t started</span>', 2 );
			return;
		}

		session_save_path( $this->sess_dir );

		session_name( $this->sess_name );

		session_set_cookie_params( 0, PMA_HTTP_PATH );

		session_start();

		$this->flush_cached_messages();

		msg_debug( '<span class="safe b">Session started</span>', 2 );
	}

	private function sanity() {

		if ( ! isset( $_SESSION['auth']['class'] ) ) {
			$_SESSION['auth']['class'] = CLASS_UNAUTH;
		}

		if ( ! isset( $_SESSION['auth']['login'] ) ) {
			$_SESSION['auth']['login'] = '';
		}

		if ( ! isset( $_SESSION['page'] ) ) {
			$_SESSION['page'] = '';
		}

		// update last activity time
		$_SESSION['last_activity'] = PMA_TIME;

		// Keep referer in session
		if ( PMA_MODE === 'output' ) {
			$_SESSION['referer'] = PMA_HTTP_HOST.$_SERVER['REQUEST_URI'];
		}

		// Mark user as proxyed if his IP address have changed at least one time, keep last ip.
		if ( isset( $_SESSION['current_ip'] ) ) {
			if ( $_SESSION['current_ip'] !== PMA_USER_IP ) {
				$_SESSION['proxy'] = $_SESSION['current_ip'];
			}
		}

		// Update current IP
		$_SESSION['current_ip'] = PMA_USER_IP;
	}
}

?>
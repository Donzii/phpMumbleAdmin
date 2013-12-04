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

/*
* DB Class for datas stored in files.
*/
class PMA_db {

	/**
	* All files PMA store datas
	*/
	private $files;

	/**
	* Queue.
	*/
	private $queue = array();

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

		$this->files = array(

			// Config files
			'admins'  => PMA_DIR_CONFIG.'admins.php',
			'config' => PMA_DIR_CONFIG.'config.php',
			'profiles' => PMA_DIR_CONFIG.'profiles.php',

			// Cache files
			'autoban_attempts' => PMA_DIR_CACHE.'autoban_attempts.php',
			'bans' => PMA_DIR_CACHE.'bans.php',
			'sessions_check' => PMA_DIR_CACHE.'sessions_chk.php',
			'pw_requests' => PMA_DIR_CACHE.'password_requests.php',
			'updates' => PMA_DIR_CACHE.'updates.php',
			'vservers' => PMA_DIR_CACHE.'vservers.php',
			'whos_online' => PMA_DIR_CACHE.'whos_online.php',
		);
	}

	private function debug( $message, $level = 3, $error = FALSE ) {
		msg_debug( '<span class="maroon b">'. __class__ .'</span>->'.$message, $level, $error );
	}

	/**
	* Get stored datas
	*/
	function get( $key ) {

		if ( ! isset( $this->files[ $key ] ) ) {
			return;
		}

		$file = $this->files[ $key ];

		if ( check_file( $file ) ) {

			return get_array_file( $file );

		} else {
			pma_fatal_error( 'Invalid rights for <b>'.$file.'</b>. Write access is required.' );
		}
	}

	/**
	* Attach to queue keys datas needed to be updated
	*/
	function queue( $key, $datas ) {

		if ( ! isset( $this->files[ $key ] ) ) {
			return;
		}

		$this->debug( __function__ .'(): Add "<i>'.$key.'</i>"' );
		$this->queue[ $key ] = $datas;
	}

	/**
	* Save all datas stored in $queue
	*/
	function save_all_datas() {

		$this->debug( __function__ .'()' );

		foreach( $this->queue as $key => $datas ) {

			// Force to compact vservers cache file
			$compact = ( $key === 'vservers' );

			new PMA_array_to_file( $this->files[ $key ], $datas, $compact );
		}
	}

	/**
	* Return all datas files
	*/
	function get_files() {
		return $this->files;
	}
}

?>
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

class PMA_updates {

	const URL_CHECK = 'http://phpmumbleadmin.sourceforge.net/CURRENT_VERSION';
	const URL_DOWNLOAD = 'http://sourceforge.net/projects/phpmumbleadmin/';

	private $error = FALSE;

	private $errstr = '';

	// Last check timestamp - by default, never checked
	private $last_chk = 1;

	// Bool - an update exists or not
	// By default, no update exists
	private $exists = FALSE;

	// Current update string version
	private $current_version = '0';

	function __construct() {
		$this->get_cache();
	}

	private function get_cache() {

		$cache = PMA_db::instance()->get( 'updates' );

		foreach( $cache as $key => $value ) {

			switch( $key ) {

				case 'last_chk';
				case 'exists';
				case 'current_version';
					$this->$key = $value;
					break;
			}
		}
	}

	private function set_cache() {

		$array['last_chk_human'] = PMA_helpers_dates::complet( $this->last_chk, 'conf=default' );
		$array['last_chk'] = $this->last_chk;
		$array['exists'] = $this->exists;
		$array['current_version'] = $this->current_version;
		$array['last_chk_error'] = $this->errstr;

		PMA_db::instance()->queue( 'updates', $array );
	}

	/**
	* Get PMA current version file infos.
	*/
	private function fetch() {

		if ( ini_get( 'allow_url_fopen' ) !== '1' ) {
			$this->error( 'php "allow_url_fopen" parameter is off. Failed to check for update' );
			return;
		}

		$url = self::URL_CHECK;

		// Debug
		if ( isset( $_GET['check_for_update'] ) && $_GET['check_for_update'] === 'debug' ) {
			$url .= '_DEBUG';
		}

		$file = @file( $url );

		if ( $file === FALSE ) {
			$this->error( 'Failed to get '.$url );
			return;
		}

		// Check for a valid update file
		if ( substr( $file[0], 0, 6 ) !== 'INT = ' OR substr( $file[1], 0, 6 ) !== 'STR = ' ) {
			$this->error( $url.': file is invalid' );
			return;
		}

		$fetch['int'] = (int) str_replace( 'INT = ', '', $file[0] );
		$fetch['str'] = str_replace( 'STR = ', '', $file[1] );

		msg_debug( 'Check for update proceeded.' );

		return $fetch;
	}

	private function error( $msg ) {
		$this->error = TRUE;
		$this->errstr = $msg;
		msg_debug( $msg, 1, TRUE );
	}

	function autocheck( $days ) {

		if ( $this->error ) {
			return;
		}

		if ( $days > 0 && $this->last_chk > 0 ) {

			$timestamp = $days * 86400 + $this->last_chk;

			if ( PMA_TIME > $timestamp ) {
				$this->check();
			}
		}
	}

	function check() {

		if ( $this->error ) {
			return;
		}

		$this->last_chk = PMA_TIME;

		// Get current version
		$fetch = $this->fetch();

		if ( ! $this->error ) {

			$this->current_version = $fetch['str'];

			// Check version
			if (
				( $fetch['int'] > PMA_VERS_INT )
				// An empty PMA_VERS_DESC means final version.
				OR ( $fetch['int'] === PMA_VERS_INT &&  PMA_VERS_DESC !== '' )
			) {
				$this->exists = TRUE;
				msg_box( 'new_pma_version', 'success', 'sprintf='.$fetch['str'] );

			} else {

				$this->exists = FALSE;

				if ( isset( $_GET['check_for_update'] ) ) {
					msg_box( 'no_update_found', 'error' );
				}
			}
		}

		$this->set_cache();
	}

	function exists() {
		return $this->exists;
	}

	/**
	* Return the fill text.
	*/
	function fill() {

		global $TEXT;

		return '<a href="'.self::URL_DOWNLOAD.'">'.sprintf( $TEXT['pma_available'], $this->current_version ).'</a>';
	}
}

?>
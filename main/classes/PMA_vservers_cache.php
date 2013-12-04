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

class PMA_vservers_cache extends PMA_abs_datas {

	protected $datas_key = 'vservers';

	private $current;

	private $profile_id;

	private $auto_refresh_enable;

	private $auto_refresh_time;

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

		$this->profile_id = PMA_user::instance()->profile_id;

		$this->auto_refresh_enable = ( PMA_config::instance()->get( 'ddl_refresh' ) > 0 );

		$this->auto_refresh_time = PMA_config::instance()->get( 'ddl_refresh' ) * 3600;

		$this->get_datas();
	}

	function save_datas() {

		// Sanity
		foreach( $this->datas as $key => $array ) {

			// Invalid datas
			if ( ! is_array( $array ) ) {

				unset( $this->datas[ $key ] );
				continue;
			}

			// Remove vserver list without valid profile id.
			if ( NULL === PMA_profiles::instance()->get( $key ) ) {
				unset( $this->datas[ $key ] );
			}
		}

		// Sort by key
		ksort( $this->datas );

		// Compact
		parent::save_datas();
	}

	function get_current() {

		// Return the cache
		if ( is_array( $this->current ) ) {
			return $this->current;
		}

		// No cache found.
		return $this->current = $this->get( $this->profile_id );
	}

	function get( $id ) {

		$profile = PMA_profiles::instance()->get( $id );

		// Not found, invalid : refresh
		if ( ! isset( $this->datas[ $id ] ) OR ! is_array( $this->datas[ $id ] ) ) {
			return $this->refresh( $id );
		}

		// profile host / port don't match with cache : refresh
		if ( $this->datas[ $id ]['profile_host'] !== $profile['host'] OR $this->datas[ $id ]['profile_port'] !== $profile['port'] ) {

			$refresh = $this->refresh( $id );

			if ( ! empty( $refresh ) ) {
				return $refresh;
			}
		}

		if ( $this->auto_refresh_enable ) {

			// Get next refresh timestamp:
			$time = $this->datas[ $id ]['cache_time'] + $this->auto_refresh_time;

			if ( PMA_TIME > $time ) {

				msg_debug( '<b>Auto refresh of vservers cache list requested</b>', 2 );

				$refresh = $this->refresh( $id );

				if ( ! empty( $refresh ) ) {
					return $refresh;
				}
			}
		}

		// Return current
		return $this->datas[ $id ];
	}

	function refresh( $id ) {

		msg_debug( 'Refreshing the vservers cache list' );

		$construct = $this->construct( $id );

		if ( ! empty( $construct ) ) {

			$this->datas[ $id ] = $construct;
			$this->save_datas();
		}

		return $construct;
	}

	/**
	* Construct vserver list
	*
	* @ Return array
	*/
	private function construct( $id ) {

		// Memo: dont try to init connection, on ICE error, it can be a issue
		// Check for a valid instance.
		if ( ! pma_ice_conn_is_valid() ) {

			msg_debug( '<b>No Ice connection found</b>, abort vservers cache list construction.' );
			return array();
		}

		$vservers = PMA_meta::instance()->getAllServers();

		$profile = PMA_profiles::instance()->get( $id );

		$array['cache_time'] = PMA_TIME;
		$array['profile_host'] = $profile['host'];
		$array['profile_port'] = $profile['port'];
		$array['vservers'] = array();

		foreach( $vservers as $prx ) {

			$prx = new PMA_vserver( $prx );

			$sid = $prx->sid();

			$name = $prx->get_conf( 'registername' );

			$webaccess = ( $prx->get_conf( 'PMA_permitConnection' ) === 'TRUE' );

			$array['vservers'][ $sid ]['id'] = $sid;
			$array['vservers'][ $sid ]['name'] = html_encode( cut_long_str( $name, 40 ) );
			$array['vservers'][ $sid ]['access'] = $webaccess;
		}

		return $array;
	}
}

?>
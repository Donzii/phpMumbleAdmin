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
* Ice connection class
*/

class PMA_meta extends PMA_abs_ice_prx {

	const URL_ICE = 'http://mumble.sourceforge.net/Ice';
	const URL_SLICE_PROFILES = 'http://mumble.sourceforge.net/Ice#Using_different_ice.slice_on_same_host';
	const URL_INVALID_SLICE_FILE = 'docs/invalid_slice_definition_file.txt';

	private $meta;

	private $profile;

	/**
	* Singleton
	*/
	static function instance( $profile = NULL ) {

		static $_instance;

		if ( is_null( $_instance ) ) {
			return $_instance = new self( $profile );
		}

		return $_instance;
	}

	private function __construct( $profile ) {

		$this->debug( '<span class="warn b">Initializing</span> Ice connection...' );

		$this->stats_enabled = PMA_config::instance()->get( 'debug_stats' );

		if ( ! extension_loaded( 'ice' ) ) {
			return $this->fatal_error( 'ice_module_not_found', '', 'url='.self::URL_ICE );
		}

		if ( ! is_array( $profile ) ) {
			return $this->fatal_error( 'ice_invalid_profile_submitted' );
		}

		$this->profile = $profile;

		// Continue
		$this->profile_sanity();
	}

	private function profile_sanity() {

		if ( isset( $this->profile['invalid_slice_file'] ) ) {
			return $this->fatal_error( 'ice_invalid_slice_file', 'ice_help_slice_file', 'url='.self::URL_INVALID_SLICE_FILE );
		}

		/**
		* Theses invalid parameters return a global "EndpointParseException".
		* Prevent it and send to SuperAdmin a specific error message.
		*/
		if ( $this->profile['host'] === '' ) {
			return $this->fatal_error( 'ice_host_not_found' );
		}

		if ( ! check_port( $this->profile['port'] ) ) {
			return $this->fatal_error( 'invalid_port' );
		}

		if ( ! is_int( $this->profile['timeout'] ) && $this->profile['timeout'] <= 0 ) {
			return $this->fatal_error( 'invalid_numerical' );
		}

		/**
		* Memo:
		* Timeout in millisecondes.
		* Zeroc ice use a retry function if timeout is reached to be sure it's a timeout.
		* It's powerfull but it's multiply by 2 the delay. So divide by 2 the timeout parameter.
		*/
		$this->profile['timeout'] = $this->profile['timeout'] * 500;

		if ( $this->profile['secret'] !== '' ) {
			$this->profile['secret'] = array( 'secret' => $this->profile['secret'] );
		} else {
			$this->profile['secret'] = array();
		}

		// Continue
		$this->init_ICE();
	}

	private function init_ICE() {

		// ICE 3.2 / 3.3
		if ( function_exists( 'Ice_loadProfile' ) ) {

			global $ICE;

			// check if slice profiles are activated
			$slice_profiles_file = ini_get( 'ice.profiles' );

			if ( $slice_profiles_file === '' ) {
				$this->profile['slice_profile'] = '';
			}

			try {
				Ice_loadProfile( $this->profile['slice_profile'] );

			} catch ( Ice_ProfileNotFoundException $Ex ) {

				return $this->fatal_error( 'ice_slice_profile_not_exists', '', 'url='.self::URL_SLICE_PROFILES, $Ex );

			} catch ( Exception $Ex ) {

				return $this->fatal_error( 'ice_unknown_error', '', '', $Ex );
			}

		// ICE 3.4
		} elseif ( function_exists( 'Ice_initialize' ) ) {

			$ICE = Ice_initialize();

			global $workaround_ice34_inc_IcePhp;

			// Load Ice.php
			if ( $workaround_ice34_inc_IcePhp !== 1 ) {

				return $this->fatal_error( 'ice_icephp_not_found', 'ice_help_ice34' );
			}

			global $workaround_ice34_inc_slice_file;

			// Load slice2php file.
			if ( ! $workaround_ice34_inc_slice_file ) {

				return $this->fatal_error( 'ice_no_slice_definition_found', 'ice_help_no_slice_definition_found', 'url='.self::URL_ICE );
			}

		} else {

			// This should never happen as we checked that ICE module exists before...
			$this->debug( 'php-Ice module is loaded but no Ice init function found.', 1, TRUE );

			return $this->fatal_error( 'ice_module_loaded_but_no_init_function' );
		}

		// Continue
		$this->init_connection( $ICE );
	}

	private function init_connection( $ICE ) {

		try {

			$proxy = $ICE->stringToProxy( 'Meta:tcp -h '.$this->profile['host'].' -p '.$this->profile['port'].' -t '.$this->profile['timeout'] );

			if ( interface_exists( 'Murmur_Meta' ) ) {

				// MEMO: do not separate ice_checkedCast() and stringToProxy() of this catch block.
				if ( ! empty( $this->profile['secret'] ) ) {
					$this->meta = $proxy->ice_checkedCast( '::Murmur::Meta' )->ice_context( $this->profile['secret'] );
				} else {
					$this->meta = $proxy->ice_checkedCast( '::Murmur::Meta' );
				}

			} else {
				return $this->fatal_error( 'ice_no_slice_definition_found', 'ice_help_no_slice_definition_found', 'url='.self::URL_ICE );
			}

		} catch ( Exception $Ex ) {

			$array = pma_get_exception( $Ex );

			if ( is_ice_dns_exception( $array ) ) {

				return $this->fatal_error( 'ice_host_not_found', '', '', $array );

			} elseif ( is_ice_connection_refused_exception( $array ) ) {

				return $this->fatal_error( 'ice_connection_refused', '', '', $array );

			} elseif ( is_ice_connection_timeout_exception( $array ) ) {

				return $this->fatal_error( 'ice_connection_timeout', '', '', $array );

			} else {
				return $this->fatal_error( 'ice_unknown_error', '', '', $array );
			}
		}

		// Continue
		$this->get_murmur_version();
	}

	/**
	* Get murmur version.
	*/
	private function get_murmur_version() {

		try {
			$this->stats_start( __function__ );
			$this->meta->getVersion( $a, $b, $c, $d );
			$this->stats_stop();

		} catch ( Exception $Ex ) {

			$array = pma_get_exception( $Ex );

			if ( is_murmur_secret_exception( $array ) ) {

				return $this->fatal_error( 'ice_invalid_secret', '', '', $array );

			} else {
				return $this->fatal_error( 'ice_unknown_error', '', '', $array );
			}
		}

		$this->int_version = intval( $a.$b.$c );
		$this->str_version = $a.'.'.$b.'.'.$c;

		if ( $d !== '' && $d !== $this->str_version ) {

			$this->txt_version = $this->str_version.' - '.$d;

		} else {
			$this->txt_version = $this->str_version;
		}

		// PMA works for murmur 1.2.0 and higher only
		if ( $this->int_version < 120 ) {
			return $this->fatal_error( 'ice_invalid_murmur_version', 'ice_help_upgrade_murmur' );
		}

		// Continue
		$this->get_default_murmur_conf();
	}

	/**
	* Check for a valid secret / readsecret
	* MEMO: murmur 1.2.2 check for a valid secret with getVersion(), murmur doesn't.
	* So getDefaultConf() is required to check murmur secret too.
	*/
	private function get_default_murmur_conf() {

		try {
			$this->getDefaultConf();

		} catch ( Exception $Ex ) {

			$array = pma_get_exception( $Ex );

			if ( is_murmur_secret_exception( $array ) ) {

				return $this->fatal_error( 'ice_invalid_secret', '', '', $array );

			} else {
				return $this->fatal_error( 'ice_unknown_error', '', '', $array );
			}
		}

		// Continue
		$this->slice_definitions_sanity();
	}

	/**
	* Last init function
	*/
	private function slice_definitions_sanity() {

		// ICE 3.2 : If getRegistration method do not exists, Web master need to hack Murmur.ice.
		if ( ! method_exists( 'Murmur_Server', 'getRegistration' ) ) {
			return $this->fatal_error( 'ice_invalid_slice_file', 'ice_help_slice_32', 'url='.self::URL_INVALID_SLICE_FILE );
		}

		// getUsers() method comes with murmur 1.2.0, if not exists, slice file is invalid.
		if ( ! method_exists( 'Murmur_Server', 'getUsers' ) ) {

			PMA_profiles::instance()->set_as_invalid_slice_file( $this->profile['id'], 'can\'t find getUsers method' );
			pma_redirect();
		}

		// Success !
		define( 'PMA_ICE_CONN_IS_VALID', TRUE );

		$this->debug( '<span class="safe b">Ice connection is valid...</span>' );
	}

	/**
	* Fatal error function
	*/
	private function fatal_error( $title_key, $help_key = '', $options = '', $ex = NULL ) {

		// Reset Murmur_meta obj.
		$this->meta = NULL;

		if ( $help_key === '' ) {
			$help_key = 'ice_help_common';
		}

		if ( is_array( $ex ) ) {
			$this->debug( 'Ice initialization exception => '.$ex['class'].' : message => '.$ex['text'], 1, TRUE );
		}

		// Add configure Ice URL
		$options = $options.',top_url=./?page=configuration&amp;tab=ICE, top_url_text=Configure Ice';

		$this->debug( 'Fatal error during Ice initializatoin.', 1, TRUE );

		msg_ice_fatal_error( $title_key, $help_key, $options );
	}

	private function debug( $message, $level = 1, $error = FALSE ) {
		msg_debug( '<span class="maroon b">'.__class__.'</span>-> '.$message, $level, $error );
	}

	private function message( $key, $type = 'error', $options = NULL ) {
		msg_box( $key, $type, $options );
	}

	/**
	* Count all user of the current murmur daemon.
	* One query allowed, cache result
	*/
	function count_all_users() {

		static $count;

		if ( ! is_null( $count ) ) {
			return $count;
		}

		$count = 0;

		foreach( $this->getBootedServers() as $prx ) {

			$prx = new PMA_vserver( $prx );

			try {

				$count += count( $prx->getUsers() );

			} catch ( Exception $Ex ) {

				$count = 'error';
				$array = pma_get_exception( $Ex );
				break;
			}
		}

		return $count;
	}

	/**
	* Add secret context to ICE proxy if SuperAdmin has configured one.
	*/
	function get_secret_ctx( &$prx ) {

		if ( ! empty( $this->profile['secret'] ) ) {
			$prx = $prx->ice_context( $this->profile['secret'] );
		}
	}

	/**
	* **********************************************
	* Murmur_Meta methods with queries_stats.
	* **********************************************
	*/

	/**
	* One query allowed, cache result
	*/
	function getAllServers() {

		static $array;

		if ( ! is_array( $array ) ) {

			$this->stats_start( __function__ );
			$array = $this->meta->getAllServers();
			$this->stats_stop();
		}

		return $array;
	}

	/**
	* One query allowed, cache result
	*/
	function getBootedServers() {

		static $array;

		if ( is_array( $array ) ) {
			return $array;
		}

		$this->stats_start( __function__ );
		$array = $this->meta->getBootedServers();
		$this->stats_stop();

		return $array;
	}

	function getServer( $id ) {

		$this->stats_start( __function__ );
		$prx = $this->meta->getServer( (int) $id );
		$this->stats_stop();

		if  ( ! is_null( $prx ) ) {

			$prx = new PMA_vserver( $prx );

		} else {
			// Unauth users dont need to know that a vserver dont exists...
			if ( class_exists( 'PMA_user', FALSE ) && ! PMA_user::instance()->is( CLASS_UNAUTH ) ) {
				$this->message( 'vserver_dont_exists' );
			}
		}

		return $prx;
	}

	/**
	* One query allowed, cache result
	*/
	function getUptime() {

		static $uptime;

		if ( ! method_exists( 'Murmur_Meta', 'getUptime' ) ) {
			$uptime = 0;
		}

		if ( is_null( $uptime ) ) {

			$this->stats_start( __function__ );
			$uptime = $this->meta->getUptime();
			$this->stats_stop();
		}

		return $uptime;
	}

	function newServer() {

		$this->stats_start( __function__ );
		$prx = $this->meta->newServer();
		$this->stats_stop();

		$this->get_secret_ctx( $prx );

		return $prx;
	}

	/**
	* One query allowed, cache result
	*/
	function getDefaultConf() {

		static $array;

		if ( ! is_array( $array ) ) {

			$this->stats_start( __function__ );
			$array = $this->meta->getDefaultConf();
			$this->stats_stop();
		}

		return $array;
	}
}

/**
* Different Exceptions betwin ice 3.3 and 3.4, and with murmur versions.
*/
function is_murmur_secret_exception( $array ) {

	if (
		$array['class'] === 'Murmur_InvalidSecretException'
		OR in_istring( $array['text'], 'InvalidSecretException' )
	) {
		return TRUE;
	}
	return FALSE;
}

function is_ice_dns_exception( $array ) {

	if (
		$array['class'] === 'Ice_DNSException'
		OR in_istring( $array['text'], 'DNSException' )
	) {
		return TRUE;
	}
	return FALSE;
}

function is_ice_connection_refused_exception( $array ) {

	if (
		$array['class'] === 'Ice_ConnectionRefusedException'
		OR in_istring( $array['text'], 'ConnectionRefusedException' )
	) {
		return TRUE;
	}
	return FALSE;
}

function is_ice_connection_timeout_exception( $array ) {

	if (
		$array['class'] === 'Ice_ConnectTimeoutException'
		OR in_istring( $array['text'], 'ConnectTimeoutException' )
	) {
		return TRUE;
	}
	return FALSE;
}

?>
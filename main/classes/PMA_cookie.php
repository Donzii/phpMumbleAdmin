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

class PMA_cookie {

	private $properties;

	// Assume by default that users accept cookies
	private $accept = TRUE;

	// Config cookie name
	private $name = 'phpMumbleAdmin_conf';

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

		$this->set_default_properties();

		$this->get_user_cookie();

		define( 'COOKIE_ACCEPTED', $this->accept );

		$this->setup_user_params();
	}

	private function set_default_properties() {

		$config = PMA_config::instance();

		$this->properties['lang'] = $config->get( 'default_lang' );
		$this->properties['skin'] = $config->get( 'default_skin' );
		$this->properties['timezone'] = $config->get( 'default_timezone' );
		$this->properties['time'] = $config->get( 'default_time' );
		$this->properties['date'] = $config->get( 'default_date' );
		$this->properties['profile_id'] = $config->get( 'default_profile' );
		$this->properties['installed_localeFormat'] = $config->get( 'default_installed_locales' );
		$this->properties['uptime'] = 2;
		$this->properties['vserver_login'] = '';
		$this->properties['logsFilters'] = 1008;
		$this->properties['highlight_logs'] = TRUE;
		$this->properties['infoPanel'] = TRUE;
		$this->properties['highlight_pmaLogs'] = TRUE;
	}

	private function get_user_cookie() {

		// PMA 0.4.1 : User config cookie become "phpMumbleAdmin_conf"
		$old = 'phpMumbleADMIN_conf';

		if ( isset( $_COOKIE[ $old ] ) ) {

			$_COOKIE[ $this->name ] = $_COOKIE[ $old ];

			// Remove old cookie
			setcookie( $old, '', 0, '/' );
		}

		if ( isset( $_COOKIE[ $this->name ] ) ) {

			$this->load_user_conf_cookie( $_COOKIE[ $this->name ] );

		} else {
			// No conf cookie found, check if user realy accept cookie or if it is the first connection to PMA.
			$this->check_user_accept_cookie();
		}
	}

	private function load_user_conf_cookie( $cookie ) {

		$cookie = @unserialize( $cookie );

		// Invalid cookie var, updates with default params
		if ( ! is_array( $cookie ) ) {

			$this->update();
			return;
		}

		// Add custom params
		foreach ( $cookie as $key => $value ) {

			if ( $this->is_valid_param( $key, $value ) ) {
				$this->properties[ $key ] = $value;
			}
		}
	}

	/**
	*
	* Check for a valid value
	*
	* @return bool
	*/
	private function is_valid_param( $key, $value ) {

		if ( ! isset( $this->properties[ $key ] ) ) {
			return FALSE;
		}

		if ( is_bool( $this->properties[ $key ] ) && is_bool( $value ) ) {
			return TRUE;
		}

		if ( is_string( $this->properties[ $key ] ) && is_string( $value ) ) {
			return TRUE;
		}

		if ( is_int( $this->properties[ $key ] ) && is_int( $value ) ) {

			if ( $key === 'uptime' ) {
				return ( $value > 0 && $value <= 3 );
			}

			return TRUE;
		}

		return FALSE;
	}

	/**
	* PMA config cookie was not found.
	* Check if it's because it's the first time connection, or because user don't accept cookies.
	*/
	private function check_user_accept_cookie() {

		if ( PMA_INSTALL ) {
			return;
		}

		// "Check cookie url"
		$check_cookie_url = 'check_cookie';

		// No config cookie found, so send it to user.
		$this->update();

		// Redirect user to check that he accepted the config cookie with the "check cookie url"
		if ( ! isset( $_GET[ $check_cookie_url ] ) ) {
			pma_redirect( '?'.$check_cookie_url );
		}

		// Still no cookie: user don't accept cookies
		$this->accept = FALSE;
		msg_box( 'refuse_cookies', 'error', 'nobutton, url=./, url_text=Check again' );
	}

	private function setup_user_params() {

		set_timezone( $this->properties['timezone'] );

		// Memo: on invalid value, setLocale return FALSE and dont change anything.
		$locales_profiles = PMA_config::instance()->get( 'installed_localesProfiles' );

		if ( isset( $locales_profiles[ $this->properties['installed_localeFormat'] ] ) ) {
			setLocale( LC_ALL, $this->properties['installed_localeFormat'] );
		} else {
			setLocale( LC_ALL, PMA_config::instance()->get( 'default_installed_locales' ) );
		}
	}

	function set( $key, $value ) {

		if ( $this->is_valid_param( $key, $value ) && $this->properties[ $key ] !== $value ) {

			$this->properties[ $key ] = $value;
		}
	}

	function get( $key ) {
		return $this->properties[ $key ];
	}

	function update() {

		if ( ! is_array( $this->properties ) ) {
			msg_debug( __class__ .'->'. __function__ .'() : invalid submitted datas !', 1, TRUE );
			return;
		}

		// 6 months
		$duration = PMA_TIME+180*24*3600;

		setcookie( $this->name, serialize( $this->properties ), $duration, '/' );

		msg_debug( 'User cookie updated', 3 );
	}
}

?>
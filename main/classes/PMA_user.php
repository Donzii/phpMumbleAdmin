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

class PMA_user {

	private $login;
	private $class;

	private $profile;
	private $profile_id;
	private $profiles_avalaible = array();

	// PMA Admin
	private $admin_id;
	private $admin_servers;

	// Mumble user
	private $mumble_id;
	private $profile_host;
	private $profile_port;
	private $sid;

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

		$this->class = $_SESSION['auth']['class'];
		$this->login = $_SESSION['auth']['login'];
		$this->profile_id = PMA_cookie::instance()->get( 'profile_id' );

		if ( isset( $_SESSION['auth']['adm_id'] ) ) {
			$this->init_admin();
		}

		if ( isset( $_SESSION['auth']['mumble_id'] ) ) {
			$this->init_mumble_user();
		}

		$this->setup_avalaible_profiles();
		$this->profiles_sanity();
		$this->get_profile();

		if ( $this->is( CLASS_ADMIN ) ) {
			$this->setup_admin_sid();
		}
	}

	private function init_admin() {

		$this->admin_id = $_SESSION['auth']['adm_id'];

		$admin = PMA_admins::instance()->get( $this->admin_id );

		if ( $admin === NULL ) {
			pma_logout();
			msg_debug( 'Admin do not exist. Logout.' );
			pma_redirect();
		}

		$this->class = $admin['class'];

		$this->admin_servers = array();

		$this->admin_profiles_access = $admin['access'];
	}

	private function init_mumble_user() {

		$this->mumble_id = $_SESSION['auth']['mumble_id'];

		$this->profile_id = $_SESSION['auth']['profile_id'];

		$this->profile_host = $_SESSION['auth']['profile_host'];

		$this->profile_port = $_SESSION['auth']['profile_port'];

		$this->sid = $_SESSION['auth']['server_id'];
	}

	private function setup_avalaible_profiles() {

		if ( PMA_INSTALL OR ! COOKIE_ACCEPTED ) {
			return;
		}

		$profiles = PMA_profiles::instance();

		switch( $this->class ) {

			case CLASS_SUPERADMIN:
			case CLASS_ROOTADMIN:

				$this->profiles_avalaible = $profiles->get_all_ids();
				break;

			case CLASS_ADMIN:

				$list = $profiles->get_all_ids();

				foreach( $list as $id ) {

					if ( isset( $this->admin_profiles_access[ $id ] ) ) {
						$this->profiles_avalaible[ $id ] = $id;
					}
				}
				break;

			case CLASS_SUPERUSER:
			case CLASS_SUPERUSER_RU:
			case CLASS_USER:

				$profile = $profiles->get( $this->profile_id );

				// Profile must be public, and check if host / port didnt change.
				if (
					$profile !== NULL
					&& $profile['public'] === TRUE
					&& $profile['host'] === $this->profile_host
					&& $profile['port'] === $this->profile_port
				) {

					$this->profiles_avalaible[ $profile['id'] ] = $profile['id'];

				} else {

					pma_logout();
					msg_box( 'iceProfile_sessionError', 'error' );
					pma_redirect();
				}

				break;

			case CLASS_UNAUTH:

				$this->profiles_avalaible = $profiles->get_all_ids( 'publics' );
				break;
		}
	}

	private function profiles_sanity() {

		if ( PMA_INSTALL OR ! COOKIE_ACCEPTED ) {
			return;
		}

		if ( empty( $this->profiles_avalaible ) ) {

			$this->profile_id = NULL;
			return;
		}

		// User requested to change his profile ( from the tab bar ).
		if ( isset( $_GET['profile'] ) && ctype_digit( $_GET['profile'] ) ) {

			$id = (int) $_GET['profile'];

			if ( $this->is_min( CLASS_ADMIN ) && $id !== $this->profile_id ) {

				unset( $_SESSION['page_vserver'], $_SESSION['page_overview']['page'] );

				if ( $_SESSION['page'] === 'vserver' ) {
					$_SESSION['page'] = 'overview';
				}
			}

			$this->set_profile( $id );
		}

		// Profile id sanity, on false, get the first profile found
		if ( ! isset( $this->profiles_avalaible[ $this->profile_id ] ) ) {
			$this->set_profile( key( $this->profiles_avalaible ) );
		}
	}

	/**
	* Set admin servers access to current profile.
	*
	* @param $id integer - profile id
	*/
	private function setup_admin_sid() {

		$id = $this->profile_id;

		if ( isset( $this->admin_profiles_access[ $id ] ) ) {

			// Set as full access admin.
			if ( $this->admin_profiles_access[ $id ] === '*' ) {

				$this->admin_servers = '*';

				$this->class = CLASS_ADMIN_FULL_ACCESS;

			} else {

				$this->admin_servers = explode( ';', $this->admin_profiles_access[ $id ] );
			}
		}

		unset( $this->admin_profiles_access );
	}

	// All params must be accessible
	function __get( $key ) {
		return $this->$key;
	}

	// Check if current user class is...
	function is( $class ) {
		return ( $this->class === $class );
	}

	// Check if current user is minimum class...
	function is_min( $class ) {
		return ( $this->class <= $class );
	}

	// Check if current user class is superior
	function is_superior( $class ) {
		return ( $this->class < $class );
	}

	// Check if current user is in the bitmask
	function is_in( $bitmask ) {

		$allow = bitmask_decompose( $bitmask );

		return in_array( $this->class, $allow, TRUE );
	}

	function set_profile( $id ) {

		$this->profile_id = $id;

		$cookie = PMA_cookie::instance();
		$cookie->set( 'profile_id', $id );
		$cookie->update();
	}

	/**
	* Return user profile
	*
	* @return array or NULL on invalid profile id
	*/
	function get_profile() {

		if ( is_array( $this->profile ) ) {

			if ( empty( $this->profile ) ) {
				return NULL;
			}

			return $this->profile;
		}

		// Password request - Load the profile of the request id
		if ( isset( $_GET['confirm_pw_request'] ) ) {

			if ( ! $this->is( CLASS_UNAUTH ) ) {

				msg_box( 'gen_pw_authenticated', 'error' );

			} else {

				$pw_requests = PMA_pw_requests::instance();

				$request = $pw_requests->get( $_GET['confirm_pw_request'] );

				if ( $request !== NULL ) {

					$pw_requests->found = $request;
					$this->set_profile( $request['profile_id'] );
				}
			}
		}

		$this->profile = PMA_profiles::instance()->get( $this->profile_id );

		// Success
		if ( is_array( $this->profile ) ) {
			return $this->profile;
		}

		// Error
		if ( $this->is( CLASS_ADMIN ) ) {
			msg_box( 'iceprofiles_admin_none', 'error', 'nobutton' );
		}

		msg_debug( __class__ .'->'. __function__ .'() : Invalid profile id', 1, TRUE );

		$this->profile = array();
		return NULL;
	}

	function set_class( $class ) {
		$this->class = $_SESSION['auth']['class'] = $class;
	}

	// Check if admin have access to a sid
	function check_admin_sid( $sid ) {

		if ( is_string( $sid ) && $sid === '*' ) {
			return TRUE;
		}

		if ( in_array( (string)$sid, $this->admin_servers, TRUE ) ) {
			return TRUE;
		}

		return FALSE;
	}
}

?>
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

class PMA_cmd_auth extends PMA_cmd {

	private $login;
	private $password;
	private $sid;
	private $ip = PMA_USER_IP;

	function process() {

		$this->sanity();

		if ( $this->sid === '' ) {
			$this->auth_pma_users();
		} else {
			$this->auth_mumble_users();
		}
	}

	/**
	* Common auth error
	*/
	private function auth_error() {
		$this->error( 'auth_error' );
	}

	private function sanity() {

		if ( ! $this->PMA->user->is( CLASS_UNAUTH ) ) {
			$this->error( 'already_authenticated' );
		}

		// Autoban attempts
		if ( $this->PMA->config->get( 'autoban_attempts' ) > 0 ) {
			PMA_autoban::instance()->attempts();
		}

		// Empty login is always an error :)
		if ( ! isset( $this->POST['login'] ) OR  $this->POST['login'] === '' ) {
			$this->log( 'auth.error', 'empty login' );
			$this->auth_error();
		}

		// Empty password is always an error too :)
		if ( ! isset( $this->POST['password'] ) OR $this->POST['password'] === '' ) {
			$this->log( 'auth.error', 'empty password' );
			$this->auth_error();
		}

		if ( ! isset( $this->POST['server_id'] ) ) {
			$this->POST['server_id'] = '';
		}

		if ( $this->POST['server_id'] !== '' && ! ctype_digit( $this->POST['server_id'] ) ) {
			$this->log( 'auth.error', 'invalid server id "'.$this->POST['server_id'].'"' );
			$this->auth_error();
		}

		$this->login = $this->POST['login'];
		$this->password = $this->POST['password'];
		$this->sid = $this->POST['server_id'];
	}

	private function auth_pma_users() {

		// SuperAdmin
		if  ( $this->login === $this->PMA->config->get( 'SA_login' ) ) {

			if ( check_crypted_pw( $this->password, $this->PMA->config->get( 'SA_pw' ) ) ) {

				// Succesfull login, setup auth session
				$this->PMA->user->set_class( CLASS_SUPERADMIN );
				$_SESSION['auth']['login'] = $this->PMA->config->get( 'SA_login' );
				$_SESSION['auth']['ip'] = $this->ip;
				$_SESSION['page'] = 'overview';

				$this->PMA->cookie->update();

				$this->log( 'auth.info', 'Successful login for SuperAdmin' );

			} else {
				$this->log( 'auth.error',  'Password error for SuperAdmin' );
				$this->auth_error();
			}

			$this->end();
		}

		// Admins
		$this->PMA->admins = PMA_admins::instance();

		$adm = $this->PMA->admins->auth( $this->login, $this->password );

		if ( is_array( $adm ) ) {

			// Succesfull login, setup session
			$this->PMA->user->set_class( $adm['class'] );

			$_SESSION['auth']['login'] = $adm['login'];
			$_SESSION['auth']['adm_id'] = $adm['id'];
			$_SESSION['auth']['ip'] = $this->ip;

			$_SESSION['page'] = 'overview';

			// Update last connection to current time.
			$adm['last_conn'] = PMA_TIME;

			$this->PMA->admins->modify( $adm );

			$this->PMA->cookie->update();

			$this->log( 'auth.info', 'Successful login for '.pma_class_name( $adm['class'] ).' "'.$this->login.'"' );

		} elseif ( $adm === 1 ) {

			$this->log( 'auth.error',  'Password error for admin "'.$this->login.'"' );
			$this->auth_error();

		} else {
			$this->log( 'auth.error',  'Login error: no admin "'.$this->login.'" found.' );
			$this->auth_error();
		}
	}

	private function auth_mumble_users() {

		$meta = PMA_meta::instance( $this->PMA->user->get_profile() );

		if ( ! pma_ice_conn_is_valid() ) {
			$this->end();
		}

		$sid = (int) $this->sid;

		$profile = $this->PMA->user->get_profile();

		// Common log infos
		if ( $this->PMA->profiles->total() > 1 ) {
			$common_infos = ' ( profile: '.$profile['id'].'# '.$profile['name'].' - server id: '.$sid.' -  login: '.$this->login.' )';
		} else {
			$common_infos = ' ( server id: '.$sid.' -  login: '.$this->login.' )';
		}

		if ( NULL === $prx = $meta->getServer( $sid ) ) {
			$this->log( 'auth.error', 'Server id do not exists'.$common_infos );
			$this->auth_error();
		}

		// Check web access
		$webaccess = $prx->get_conf( 'PMA_permitConnection' );

		if ( $webaccess !== 'TRUE' ) {
			$this->log( 'auth.warn', 'Web access denied' .$common_infos );
			$this->error( 'web_access_disabled' );
		}

		$isRunning = $prx->isRunning();

		// Start the server if it's stopped
		if ( ! $isRunning ) {
			$prx->start();
		}

		// verifyPassword return user ID on successfull authentification, else
		// -1 for failed authentication and -2 for unknown usernames.
		$auth_id = $prx->verifyPassword( $this->login, $this->password );

		// On succes, fetch registration before stop the vserver
		if ( $auth_id >= 0 ) {
			$user_registration = $prx->getRegistration( $auth_id );
		}

		// Check if registered user have SuperUser_ru rights
		$is_SuperUser_ru = $prx->is_superuser_ru( $auth_id );

		// Stop the server if it was not running before.
		if ( ! $isRunning ) {
			$prx->stop();
		}

		// PASSWORD ERROR
		if ( $auth_id === -1 ) {
			$this->log( 'auth.error', 'Password error:' .$common_infos );
			$this->auth_error();
		}

		// INVALID LOGIN
		if ( $auth_id === -2 ) {
			$this->log( 'auth.error', 'Login error:' .$common_infos );
			$this->auth_error();
		}

		// Check if SuperUser connection is authorized.
		if ( $auth_id === 0 && ! $this->PMA->config->get( 'SU_auth' ) ) {
			$this->log( 'auth.warn', 'SuperUsers not allowed' .$common_infos );
			$this->error( 'auth_su_disabled' );
		}

		// Check if registered user connection is authorized, but let connect SuperUser_ru anyway.
		if ( $auth_id > 0 && ! $this->PMA->config->get( 'RU_auth' ) && ! $is_SuperUser_ru ) {
			$this->log( 'auth.warn', 'Registered users not allowed' .$common_infos );
			$this->error( 'auth_ru_disabled' );
		}

		// Succesfull login, setup the session
		if ( $auth_id === 0 ) {

			$this->PMA->user->set_class( CLASS_SUPERUSER );

		} elseif ( $is_SuperUser_ru ) {

			$this->PMA->user->set_class( CLASS_SUPERUSER_RU );

		} else {
			$this->PMA->user->set_class( CLASS_USER );
		}

		$this->log( 'auth.info', 'Successful login for '.pma_class_name( $this->PMA->user->class ).$common_infos );

		$_SESSION['auth']['profile_id'] = $profile['id'];
		$_SESSION['auth']['profile_host'] = $profile['host'];
		$_SESSION['auth']['profile_port'] = $profile['port'];
		$_SESSION['auth']['login'] = $user_registration[0];
		$_SESSION['auth']['server_id'] = $sid;
		$_SESSION['auth']['mumble_id'] = $auth_id;
		$_SESSION['auth']['ip'] = $this->ip;
		$_SESSION['page'] = 'vserver';

		$this->PMA->cookie->update();
		$this->end();
	}
}

?>

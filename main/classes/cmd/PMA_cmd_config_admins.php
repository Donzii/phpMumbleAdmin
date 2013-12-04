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

class PMA_cmd_config_admins extends PMA_cmd {

	function process() {

		if ( ! $this->PMA->user->is_min( CLASS_ADMIN ) ) {
			$this->illegal_operation();
		}

		$this->PMA->admins = PMA_admins::instance();

		if ( isset( $this->POST['change_own_pw'] ) ) {
			$this->change_own_pw();

		} elseif ( isset( $this->POST['add_new_admin'] ) ) {
			$this->add_new_admin();

		} elseif ( isset( $this->POST['remove_admin'] ) ) {
			$this->remove_admin( $this->POST['remove_admin'] );

		} elseif ( isset( $this->POST['edit_registration'] ) ) {
			$this->edit_registration();

		} elseif ( isset( $this->POST['edit_admin_access'] ) ) {
				$this->edit_access();

		} elseif ( isset( $this->POST['edit_SuperAdmin'] ) ) {
			$this->edit_SuperAdmin();
		}
	}

	private function change_own_pw() {

		if ( ! is_int( $this->PMA->user->admin_id ) ) {
			$this->illegal_operation();
		}

		$id = $this->PMA->user->admin_id;
		$adm = $this->PMA->admins->get( $id );

		// Check current admin password
		if ( ! check_crypted_pw( $this->POST['current'], $adm['pw'] ) ) {

			$this->redirection = 'referer';
			$this->error( 'auth_error' );
		}

		if ( ! confirm_new_pw( $this->POST['new_pw'], $this->POST['confirm_new_pw'] ) ) {

			$this->redirection = 'referer';
			$this->error( 'password_check_failed' );
		}

		$adm['pw'] = crypt_pw( $this->POST['new_pw'] );
		$this->PMA->admins->modify( $adm );
		$this->success( 'change_pw_success' );
	}

	private function add_new_admin() {

		if ( ! $this->PMA->user->is_min( CLASS_ROOTADMIN ) ) {
			$this->illegal_operation();
		}

		$login = $this->POST['login'];

		if ( ! $this->PMA->admins->validate_login( $login ) ) {
			$this->error( 'invalid_username' );
		}

		if (  $this->PMA->admins->login_exists( $login ) ) {
			$this->error( 'username_exists' );
		}

		if ( ! confirm_new_pw( $this->POST['new_pw'], $this->POST['confirm_new_pw'] ) ) {
			$this->error( 'password_check_failed' );
		}

		$id = $this->PMA->admins->add( $login, $this->POST['new_pw'], $this->POST['email'], $this->POST['name'], $this->POST['class'] );

		$this->log_action( 'Admin account created ( '.$id.'# '.$login.' )' );
		$this->success( 'registration_created_success' );
	}

	private function remove_admin( $id ) {

		if ( ! $this->PMA->user->is_min( CLASS_ROOTADMIN ) ) {
			$this->illegal_operation();
		}

		if ( ! isset( $this->POST['confirmed'] ) ) {
			$this->end();
		}

		$adm = $this->PMA->admins->get( $id );

		if ( $adm === NULL OR ! $this->PMA->user->is_superior( $adm['class'] ) ) {
			$this->illegal_operation();
		}

		$this->PMA->admins->delete( $id );

		$this->log_action( 'Admin account deleted ( '.$id.'# '.$adm['login'].' )' );
		$this->success( 'registration_deleted_success' );
	}

	private function edit_registration() {

		if ( ! $this->PMA->user->is_min( CLASS_ROOTADMIN ) ) {
			$this->illegal_operation();
		}

		$adm = $original = $this->PMA->admins->get( $_SESSION['page_administration']['adm_id'] );

		if ( $adm === NULL OR ! $this->PMA->user->is_superior( $adm['class'] ) ) {
			$this->illegal_operation();
		}

		// Login
		if ( $this->POST['login'] !== $adm['login'] ) {

			if ( ! $this->PMA->admins->validate_login( $this->POST['login'] ) ) {
				$this->redirection = 'referer';
				$this->error( 'invalid_username' );
			}

			if ( $this->PMA->admins->login_exists( $this->POST['login'] ) ) {
				$this->redirection = 'referer';
				$this->error( 'username_exists' );
			}

			$adm['login'] = $this->POST['login'];
		}

		// Password
		if ( $this->POST['new_pw'] !== '' ) {

			if ( ! confirm_new_pw( $this->POST['new_pw'], $this->POST['confirm_new_pw'] ) ) {
				$this->redirection = 'referer';
				$this->error( 'password_check_failed' );
			}

			$adm['pw'] = crypt_pw( $this->POST['new_pw'] );
		}

		// Class
		$class = (int) $this->POST['class'];

		if ( $this->PMA->user->is_superior( $class ) ) {
			$adm['class'] = $class;
		}

		// Email
		$adm['email'] = $this->POST['email'];

		// Name
		$adm['name'] = $this->POST['name'];

		// Check if the registration has been modified
		$diff = array_diff_strict( $adm, $original );

		if ( ! empty( $diff ) ) {

			$this->PMA->admins->modify( $adm );

			if ( isset( $diff['login'] ) ) {
				$this->log_action( 'Admin login updated ( '.$adm['id'].'# '.$original['login'].' => '.$adm['login'].' )' );
			}

			if ( isset( $diff['pw'] ) ) {
				$this->success( 'change_pw_success' );
				$this->log_action( 'Admin password updated ( '.$adm['id'].'# '.$adm['login'].' )' );
			}

			if ( isset( $diff['class'] ) ) {
				$this->log_action( 'Admin class updated ( '.$adm['id'].'# '.$adm['login'].' => '.pma_class_name( $adm['class'] ).' )' );
			}
		}
	}

	private function edit_access() {

		if ( ! $this->PMA->user->is_min( CLASS_ROOTADMIN ) ) {
			$this->illegal_operation();
		}

		$profile_id = $this->PMA->cookie->get( 'profile_id' );

		$adm = $this->PMA->admins->get( $_SESSION['page_administration']['adm_id'] );

		if ( $adm === NULL OR ! $this->PMA->user->is_superior( $adm['class'] ) ) {
			$this->illegal_operation();
		}

		// Full Access
		if ( isset( $this->POST['full_access'] ) ) {

			$adm['access'][ $profile_id ] = '*';

		} else {

			$access = '';

			foreach( $this->POST as $key => $value ) {

				// Memo, a digital key in POST return an integer
				// example $this->POST[0], $this->POST[1]
				if ( is_int( $key ) && $value === 'on' ) {
					$access .= $key.';';
				}
			}

			if ( $access !== '' ) {
				$adm['access'][ $profile_id ] = substr( $access, 0, -1 );
			} else {
				unset( $adm['access'][ $profile_id ] );
			}
		}

		$this->PMA->admins->modify( $adm );

		$this->log_action( 'Admin access updated ( '.$adm['id'].'# '.$adm['login'].' )' );
	}

	private function edit_SuperAdmin() {

		if ( ! $this->PMA->user->is( CLASS_SUPERADMIN ) ) {
			$this->illegal_operation();
		}

		$this->redirection = 'referer';

		// redirect on wrong SA password
		if ( ! check_crypted_pw( $this->POST['current' ], $this->PMA->config->get( 'SA_pw' ) ) ) {
			$this->error( 'auth_error' );
		}

		if ( $this->POST['login'] !== $this->PMA->config->get( 'SA_login' ) ) {

			if ( ! $this->PMA->admins->validate_login( $this->POST['login'] ) ) {
				$this->error( 'invalid_username' );
			}

			if ( $this->PMA->admins->login_exists( $this->POST['login'] ) ) {
				$this->error( 'username_exists' );
			}

			$this->PMA->config->set( 'SA_login', $this->POST['login'] );
			$this->log_action( 'SuperAdmin login updated' );
		}

		// Change SA password
		if ( $this->POST['new_pw'] !== '' ) {

			if ( confirm_new_pw( $this->POST['new_pw'], $this->POST['confirm_new_pw'] ) ) {

				$this->PMA->config->set( 'SA_pw', crypt_pw( $this->POST['new_pw'] ) );
				$this->log_action( 'SuperAdmin password updated' );
				$this->success( 'change_pw_success' );

			} else {
				$this->error( 'password_check_failed' );
			}
		}

		$_SESSION['auth']['login'] = $this->POST['login'];
	}
}


?>

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

class PMA_cmd_murmur_registrations extends PMA_cmd {

	private $prx;

	// Registration id of the session
	private $id;

	// Registration array of the session
	private $registration;

	// By default, we assume that user do not modify it's own account.
	private $own_account = FALSE;

	function process() {

		if ( ! $this->PMA->user->is_min( CLASS_USER ) ) {
			$this->illegal_operation();
		}

		if ( NULL === $this->prx = $this->PMA->meta->getServer( $_SESSION['page_vserver']['id'] ) ) {
			$this->end();
		}

		if ( isset( $_SESSION['page_vserver']['registration_id'] ) ) {

			$this->id = $_SESSION['page_vserver']['registration_id'];

			// SuperUser_ru cant have access to SuperUser account
			if ( $this->id === 0 && $this->PMA->user->is( CLASS_SUPERUSER_RU ) ) {
				$this->illegal_operation();
			}

			$this->registration = $this->prx->getRegistration( $this->id );

			// Check if user modify it's own account.
			if ( isset( $_SESSION['auth']['mumble_id'] ) && $_SESSION['auth']['mumble_id'] === $this->id ) {
				$this->own_account = TRUE;
			}
		}

		if ( isset( $this->POST['add_new_account'] ) ) {
			$this->add_new_account( $this->POST['add_new_account'] );

		} elseif ( isset( $this->POST['delete_account_id'] ) ) {
			$this->delete_account_id( $this->POST['delete_account_id'] );

		} elseif ( isset( $this->POST['delete_account'] ) ) {
			$this->delete_account();

		} elseif ( isset( $this->POST['change_login'] ) ) {
			$this->change_login( $this->POST['change_login'] );

		} elseif ( isset( $this->POST['change_email'] ) ) {
			$this->change_email( $this->POST['change_email'] );

		} elseif ( isset( $this->POST['change_desc'] ) ) {
			$this->change_desc( $this->POST['change_desc'] );

		} elseif ( isset( $this->POST['change_password'] ) ) {
			$this->change_password();

		} elseif ( isset( $this->POST['remove_avatar'] ) ) {
			$this->remove_avatar();

		} elseif ( isset( $this->POST['registrations_search'] ) ) {
			$this->registrations_search( $this->POST['registrations_search'] );

		} elseif ( isset( $this->GET['reset_registrations_search'] ) ) {
			$this->reset_registrations_search();
		}
	}

	private function add_new_account( $name ) {

		if ( ! $this->PMA->user->is_min( CLASS_SUPERUSER_RU ) ) {
			$this->illegal_operation();
		}

		// Memo : registerUser() return the uid of the new account
		// Memo : registerUser() verify for invalid characters but not updateRegistration()
		if ( ! $this->prx->validate_chars( 'username', $name ) ) {
			$this->error( 'invalid_username' );
		}

		$new_uid = $this->prx->registerUser( array( $name ) );

		$this->success( 'registration_created_success' );

		if ( isset( $this->POST['redirect_to_new_account'] ) ) {
			$this->redirection = './?registration_id='.$new_uid;
		}
	}

	private function delete_account_id( $id ) {

		if ( ! $this->PMA->user->is_min( CLASS_SUPERUSER_RU ) ) {
			$this->illegal_operation();
		}

		if ( ! isset( $this->POST['confirmed'] ) ) {
			$this->end();
		}

		if ( ! ctype_digit( $id ) ) {
			$this->error( 'invalid_numerical' );
		}

		$id = (int) $id;

		if ( $id > 0 ) {
			$this->prx->unregisterUser( $id );
			$this->success( 'registration_deleted_success' );
		} else {
			$this->illegal_operation();
		}
	}

	private function delete_account() {

		// Check if registered user have the right to delete his account
		if ( $this->PMA->user->is( CLASS_USER ) && ! $this->PMA->config->get( 'RU_delete_account' ) ) {
			$this->illegal_operation();
		}

		if ( ! isset( $this->POST['confirmed'] ) ) {
			$this->end();
		}

		if ( $this->id > 0 ) {

			$this->prx->unregisterUser( $this->id );

			if ( $this->own_account ) {
				pma_logout();
			} else {
				unset( $_SESSION['page_vserver']['registration_id'] );
			}

			$this->success( 'registration_deleted_success' );

		} else {
			$this->illegal_operation();
		}
	}

	private function change_login( $login ) {

		// Check if simple user have the right to modify his login
		if ( $this->PMA->user->is( CLASS_USER ) && ! $this->PMA->config->get( 'RU_edit_login' ) ) {
			$this->illegal_operation();
		}

		// Setup login
		if ( ! isset( $this->registration[0] ) ) {
			$this->registration[0] = '';
		}

		if ( $login !== '' && $login !== $this->registration[0] ) {

			if ( ! $this->prx->validate_chars( 'username', $login ) ) {
				$this->error( 'invalid_username' );
			}

			$this->registration[0] = $login;

			// if the PMA user change he's own login, change the $_SESSION['login'] name
			if ( $this->own_account ) {
				$_SESSION['auth']['login'] = $login;
			}

			$this->prx->updateRegistration( $this->id, $this->registration );
		}
	}

	private function change_email( $email ) {

		// Setup email
		if ( ! isset( $this->registration[1] ) ) {
			$this->registration[1] = '';
		}

		if ( $email !== $this->registration[1] ) {

			$this->registration[1] = $email;
			$this->prx->updateRegistration( $this->id, $this->registration );
		}
	}

	private function change_desc( $description ) {

		// Setup comment
		if ( ! isset( $this->registration[2] ) ) {
			$this->registration[2] = '';
		}

		if ( $description !== $this->registration[2] ) {

			// Remove eol to avoid a bug with change comment js textarea.
			$this->registration[2] =  replace_eol( $this->prx->remove_html_tags( $description ) );
			$this->prx->updateRegistration( $this->id, $this->registration );
		}
	}

	private function change_password() {

		// Check if SuperUsers can change users password
		if ( $this->PMA->user->is_in( ALL_SUPERUSERS ) && ! $this->PMA->config->get( 'SU_edit_user_pw' ) ) {

			if ( ! $this->own_account ) {
				$this->illegal_operation();
			}
		}

		// Verify current password if we edit our account.
		if ( $this->own_account ) {

			if ( ! isset( $this->POST['current'] ) ) {
				$this->illegal_operation();
			}

			// verifyPassword() return user ID on successfull authentification, else
			// -1 for failed authentication and -2 for unknown usernames.
			$auth = $this->prx->verifyPassword( $_SESSION['auth']['login'], $this->POST['current'] );

			if ( $auth !== $_SESSION['auth']['mumble_id'] ) {

				$this->redirection = 'referer';
				$this->error( 'auth_error' );
			}
		}

		if ( $this->POST['new_pw'] === '' OR $this->POST['new_pw'] !== $this->POST['confirm_new_pw'] ) {
			$this->error( 'password_check_failed' );
		}

		$this->registration[4] = $this->POST['new_pw'];

		$this->prx->updateRegistration( $this->id, $this->registration );

		// Verify that's the password has changed:
		$verifyPassword = $this->prx->verifyPassword( $this->registration[0], $this->POST['new_pw'] );

		if ( $verifyPassword === $this->id ) {
			$this->success( 'change_pw_success' );
		} else {
			$this->error( 'change_pw_error' );
		}
	}

	private function remove_avatar() {

		if ( isset( $this->POST['confirmed'] ) ) {
			$this->prx->setTexture( $this->id, array() );
		}
	}

	private function registrations_search( $search ) {

		if ( ! $this->PMA->user->is_min( CLASS_SUPERUSER_RU ) ) {
			$this->illegal_operation();
		}

		if ( $search === '' ) {
			unset( $_SESSION['search']['registrations'] );
		} else {
			$_SESSION['search']['registrations'] = $search;
		}
	}

	private function reset_registrations_search() {

		if ( ! $this->PMA->user->is_min( CLASS_SUPERUSER_RU ) ) {
			$this->illegal_operation();
		}

		unset( $_SESSION['search']['registrations'] );
	}
}

?>

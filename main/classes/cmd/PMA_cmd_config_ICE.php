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

class PMA_cmd_config_ICE extends PMA_cmd {

	function process() {

		if ( ! $this->PMA->user->is_min( CLASS_ROOTADMIN ) ) {
			$this->illegal_operation();
		}

		if ( isset( $this->POST['add_profile'] ) ) {
			$this->add_profile( $this->POST['add_profile'] );

		} elseif ( isset( $this->POST['delete_profile'] ) ) {
			$this->delete_profile();

		} elseif ( isset( $this->GET['set_default_profile'] ) ) {
			$this->set_default_profile();

		} elseif ( isset( $this->GET['enable_profile'] ) ) {
			$this->enable_profile();

		} elseif ( isset( $this->POST['edit_profile'] ) ) {
			$this->edit_profile();
		}
	}

	private function add_profile( $name ) {

		if ( $name === '' ) {
			$this->error( 'empty_profile_name' );
		}

		$id = $this->PMA->profiles->add( $name );

		$this->log_action( 'profile created ( '.$id.'# '.$name.' )' );

		$this->PMA->cookie->set( 'profile_id', $id );
		$this->PMA->cookie->update();
	}

	private function delete_profile() {

		if ( ! isset( $this->POST['confirmed'] ) ) {
			$this->end();
		}

		$id = $this->PMA->cookie->get( 'profile_id' );

		$profile = $this->PMA->user->get_profile();

		$this->PMA->profiles->delete( $id );

		$this->log_action( 'profile deleted ( '.$id.'# '.$profile['name'].' )' );

		PMA_admins::instance()->del_profile_access( $id );

		// Set profile_id to a valid profile id
		if ( $id === $this->PMA->config->get( 'default_profile' ) ) {

			$first = $this->PMA->profiles->get_first();
			$this->PMA->cookie->set( 'profile_id', $first['id'] );

		} else {
			$this->PMA->cookie->set( 'profile_id', $this->PMA->config->get( 'default_profile' ) );
		}

		$this->PMA->cookie->update();
	}

	private function set_default_profile() {

		$id = $this->PMA->cookie->get( 'profile_id' );

		$this->PMA->config->set( 'default_profile', $id );

		$this->log_action( 'Default profile ( '.$id.'# '.$this->PMA->profiles->get_name( $id ).' )' );
	}

	private function enable_profile() {
		$this->PMA->profiles->remove_invalid_slice_file( $this->PMA->cookie->get( 'profile_id' ) );
	}

	private function edit_profile() {

		$profile = $original = $this->PMA->user->get_profile();

		// Name
		if ( $this->POST['name'] !== '' ) {
			$profile['name'] = $this->POST['name'];
		}

		// Toggle public
		$profile['public'] = isset( $this->POST['public'] );

		// Host
		if ( $this->POST['host'] !== '' ) {

			// A digit host return an ice exection, deny it.
			if ( ! ctype_digit( $this->POST['host'] ) ) {

				$profile['host'] = $this->POST['host'];
				unset( $_SESSION['page_vserver'] );
			}

		} else {
			msg_box( 'empty_value_not_allowed', 'error' );
		}

		// Port
		$port = (int) $this->POST['port'];

		if ( check_port( $port ) ) {

			$profile['port'] = $port;
			unset( $_SESSION['page_vserver'] );

		} else {
			msg_box( 'invalid_port', 'error' );
		}

		// Timeout
		$timeout = (int) $this->POST['timeout'];

		if ( $timeout > 0 ) {

			$profile['timeout'] = $timeout;

		} else {
			msg_box( 'invalid_numerical', 'error', 'sprintf=timeout > 0' );
		}

		// Secret
		$profile['secret'] = $this->POST['secret'];

		// Slprofile
		if ( isset( $this->POST['slice_profile'] ) ) {
			$profile['slice_profile'] = $this->POST['slice_profile'];
		}

		// PHP-slice
		if ( isset( $this->POST['slice_php'] ) ) {
			$profile['slice_php'] = $this->POST['slice_php'];
		}

		// HTTP address
		$profile['http-addr'] = $this->POST['http_addr'];

		// Check if the profile has been modified
		$diff = array_diff_strict( $profile, $original );

		if ( ! empty( $diff ) ) {

			$this->PMA->profiles->modify( $profile );
			$this->log_action( 'profile updated ( '.$profile['id'].'# '.$profile['name'].' )' );
		}
	}
}

?>

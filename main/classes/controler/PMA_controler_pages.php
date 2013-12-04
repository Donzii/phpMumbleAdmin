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

class PMA_controler_pages {

	private $user;

	private $name;

	private $availables = array();

	function __construct( $user, $name ) {

		$this->user = $user;

		$this->name = $name;

		$this->define_availables();

		if ( isset( $_GET['page'] ) ) {

			if ( isset( $_GET['sid'] ) ) {
				$this->set_sid( $_GET['sid'] );
			}

			$this->set( $_GET['page'] );
		}

		$this->sanity();
	}

	/**
	* Define current user available pages
	*/
	private function define_availables() {

		if ( PMA_INSTALL ) {

			$this->availables[] = 'install';
			$this->set( 'install' );
			return;
		}

		if ( $this->user->is( CLASS_UNAUTH ) ) {

			$this->availables[] = 'auth';
			$this->set( 'auth' );
			return;
		}

		// Available for all auth users
		$this->availables[] = 'configuration';
		$this->availables[] = 'vserver';

		if ( $this->user->is_min( CLASS_ROOTADMIN ) ) {
			$this->availables[] = 'administration';
			$this->availables[] = 'overview';
			return;
		}

		if ( $this->user->is_min( CLASS_ADMIN ) ) {
			$this->availables[] = 'overview';
			return;
		}
	}

	private function sanity() {

		if ( ! in_array( $this->name, $this->availables, TRUE ) ) {

			// Get first avalaible page.
			$this->set( current( $this->availables ) );
		}
	}

	/**
	* Change vserver id
	*/
	private function set_sid( $sid ) {

		if ( ! ctype_digit( $sid ) OR ! $this->user->is_min( CLASS_ADMIN ) ) {
			return;
		}

		$sid = (int) $sid;

		// Reset $_SESSION['page_vserver'] if server id is different.
		if ( isset( $_SESSION['page_vserver']['id'] ) && $_SESSION['page_vserver']['id'] !== $sid ) {

			// Keep tab in session:
			if ( $this->name === 'vserver' ) {
				$tab = $_SESSION['page_vserver']['tab'];
			}

			unset( $_SESSION['page_vserver'] );
		}

		// Set new vserver id
		$_SESSION['page_vserver']['id'] = $sid;

		if ( isset( $tab ) ) {
			$_SESSION['page_vserver']['tab'] = $tab;
		}
	}

	/**
	* Modify user current page
	*/
	function set( $page ) {

		if ( in_array( $page, $this->availables, TRUE ) ) {
			$this->name = $_SESSION['page'] = $page;
		}
	}

	/**
	* Return user current page name
	*/
	function current() {
		return $this->name;
	}

	function get_avalaibles() {
		return $this->availables;
	}
}

?>

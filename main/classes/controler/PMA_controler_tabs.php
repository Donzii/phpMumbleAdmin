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

class PMA_controler_tabs {

	private $availables;

	private $page;

	private $default;

	function __construct( $page, $default, $availables ) {

		$this->page = $page;
		$this->default = $default;
		$this->availables = $availables;

		$this->sanity();
	}

	/**
	* Change current page tab
	*/
	private function set( $tab_name ) {

		foreach( $this->availables as $tab ) {

			if ( $tab_name === $tab ) {

				$_SESSION['page_'.$this->page ]['tab'] = $tab;
				break;
			}
		}
	}

	/**
	* Tabs sanity
	*/
	private function sanity() {

		if ( isset( $_GET['tab'] ) ) {

			$this->set( $_GET['tab'] );

		} elseif ( ! isset( $_SESSION['page_'.$this->page ]['tab'] ) ) {

			$this->set( $this->default );
		}
	}

	/**
	* Return current tab
	*/
	function current() {
		return $_SESSION['page_'.$this->page ]['tab'];
	}

	function get_avalaibles() {
		return $this->availables;
	}
}

?>
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

class PMA_cmd_murmur_logs extends PMA_cmd {

	function process() {

		if ( ! $this->PMA->user->is_min( CLASS_SUPERUSER_RU ) ) {
			$this->illegal_operation();
		}

		if ( isset( $this->GET['toggle_log_filter'] ) ) {
			$this->toggle_log_filter( $this->GET['toggle_log_filter'] );

		} elseif ( isset( $this->GET['toggle_highlight'] ) ) {
			$this->toggle_highlight();

		} elseif ( isset( $this->POST['logs_search'] ) ) {
			$this->logs_search();

		} elseif ( isset( $this->GET['reset_logs_search'] ) ) {
			$this->reset_logs_search();
		}
	}

	private function toggle_log_filter( $mask ) {

		// Get $vlogs
		require 'main/include/vars.logs_filters.php';

		if ( ! ctype_digit( $mask ) OR ! isset( $vlogs['filters'][ $mask ] ) ) {

			$this->debug(  __function__ .'() : bitmask "'.$mask.'" is invalid.', 1, TRUE );
			$this->illegal_operation();
		}

		$mask = (int) $mask;

		$count = $this->PMA->cookie->get( 'logsFilters' );

		if ( in_array( $mask, $vlogs['filters_actived'], TRUE ) ) {

			$count -= $mask;

		} else {
			$count += $mask;
		}

		$this->PMA->cookie->set( 'logsFilters', $count );
		$this->PMA->cookie->update();
	}

	private function toggle_highlight() {

		$this->PMA->cookie->set( 'highlight_logs', ! $this->PMA->cookie->get( 'highlight_logs' ) );
		$this->PMA->cookie->update();
	}

	private function logs_search() {

		if ( $this->POST['logs_search'] === '' ) {

			unset( $_SESSION['search']['logs'] );

		} else {
			$_SESSION['search']['logs'] = $this->POST['logs_search'];
		}
	}

	private function reset_logs_search() {
		unset( $_SESSION['search']['logs'] );
	}
}

?>
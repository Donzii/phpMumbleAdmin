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

abstract class PMA_cmd {

	protected $PMA;

	protected $GET;
	protected $POST;

	protected $redirection;

	abstract public function process();

	/**
	* Init and proccess the CMD class until redirect to main.
	*/
	function __construct( $ice_required ) {

		$this->debug( '<span class="warn">Initializing</span> CMD mode <span class="maroon b">'.PMA_CMD_MODE.'</span>', 2 );

		$this->PMA = PMA::instance();
		$this->GET = $_GET;
		$this->POST = $_POST;

		// Process
		if ( $ice_required ) {

			$this->init_ice_conn();

			try {
				$this->process();
			} catch ( Exception $ex ) {
				pma_murmur_exception( $ex );
			}

		} else {
			$this->process();
		}

		$this->end();
	}

	private function init_ice_conn() {

		$this->PMA->meta = PMA_meta::instance( $this->PMA->user->get_profile() );

		if ( ! pma_ice_conn_is_valid() ) {
			$this->end();
		}
	}

	protected function error( $key, $options = NULL ) {
		msg_box( $key, 'error', $options );
		$this->end();
	}

	protected function success( $key, $options = NULL ) {
		msg_box( $key, 'success', $options );
	}

	protected function illegal_operation() {
		pma_illegal_operation();
	}

	protected function debug( $message, $level = 1, $error = FALSE ) {
		msg_debug( $message, $level, $error );
	}

	protected function log( $level, $message ) {
		write_log( $level, $message );
	}

	protected function log_action( $message ) {

		$user = $this->PMA->user;

		// Dont to log SuperAdmin actions if not requested
		if ( ! $this->PMA->config->get( 'pmaLogs_SA_actions' ) && $user->is( CLASS_SUPERADMIN ) ) {
			return;
		}

		if ( $user->admin_id !== NULL ) {
			$message = $user->admin_id.'# '.$user->login.' - '.$message;
		} else {
			$message = pma_class_name( $user->class ).' - '.$message;
		}

		$this->log( 'action.info', $message );
	}

	/**
	* Stop the CMD directly and apply common actions before redirection.
	*/
	protected function end() {

		if ( $this->redirection === 'referer' && isset( $_SESSION['referer'] ) ) {
			$this->redirection = $_SESSION['referer'];
		}

		if ( PMA_DEBUG > 0 ) {
			$_SESSION['cmd_stats']['duration'] = PMA_helpers_stats::duration( PMA_STARTED );
			$_SESSION['cmd_stats']['memory'] = PMA_helpers_stats::memory();
			$_SESSION['cmd_stats']['ice'] = PMA_helpers_stats::ice_queries();
		}

		pma_redirect( $this->redirection );
	}
}

?>
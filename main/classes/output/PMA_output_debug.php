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

class PMA_output_debug extends PMA_output {

	private function debug_div( $output ) {
		$this->cache( '<div class="debug oBox black mtop">'.EOL.$output.'</div>'.EOL.EOL );
	}

	function get_stats() {

		$output = '';

		// CMDs stats
		if ( isset( $_SESSION['cmd_stats'] ) ) {

			$stats = $_SESSION['cmd_stats'];
			unset( $_SESSION['cmd_stats'] );

			$output .= '<div class="b">'.'cmd duration: '.$stats['duration']. 's - '.$stats['memory'].'</div>'.EOL;
			if ( ! is_null( $stats['ice'] ) ) {
				$output .= '<div class="b">'.'Ice queries: '.$stats['ice'][0].' during: '.$stats['ice'][1].' s</div>'.EOL;
			}
		}

		$duration = PMA_helpers_stats::duration( PMA_STARTED );
		$memory = PMA_helpers_stats::memory();
		$queries = PMA_helpers_stats::ice_queries();

		// Ext viewer debug don't load languages files.
		global $TEXT;
		if ( ! isset( $TEXT['page_generated'] ) ) {
			$TEXT['page_generated'] = 'Page generated in %1$s seconds - Memory peak: %2$s';
		}

		$output .= '<div>'.sprintf( $TEXT['page_generated'], $duration, $memory ).'</div>'.EOL;

		if ( ! is_null( $queries ) ) {
			$output .= '<div>Ice queries: '.$queries[0].' during '.$queries[1].' s</div>'.EOL;
		}

		$this->debug_div( $output );
	}

	function get_debug_messages( $messages ) {

		$output = '<div class="txtR b">Debug messages</div>'.EOL;

		foreach ( $messages as $key => $array ) {

			// Remove too high level debug messages
			if ( PMA_DEBUG < $array['level'] ) {
				continue;
			}

			$msg = $array['msg'];

			if ( $array['error'] ) {
				$msg = '<span class="unsafe">Error => '.$msg.'</span>';
			}

			if ( $array['cmd'] ) {
				$type = '<span class="safe b">[cmd]</span>';
			} else {
				$type = '';
			}

			$output .= '<div>'.$array['level'].'_ '.$type.' '.$msg.'</div>'.EOL;
		}

		$this->debug_div( $output );
	}

	function get_session( $session ) {

		ksort( $session );

		$output = '<div><b>SESSION</b>: sess_'.session_id().'</div>'.EOL;

		foreach ( $session as $key => $value ) {

			if ( is_array( $value ) ) {

				$output .= '<div>['.$key.'] : Array ( ';

				foreach ( $value as $k => $v ) {

					if ( is_array( $v ) ) {
						$output .= '['.$k.'] => ';
						$output .= replace_eol( print_r( $v, TRUE ) );

					} else {
						$output .= '['.$k.'] => '.$v.' ';
					}
				}
				$output .= ' )</div>'.EOL;

			} else {
				$output .= '<div>['.$key.'] => '.html_encode( $value ).'</div>'.EOL;
			}
		}

		$this->debug_div( $output );
	}

	function get_PMA_object( $obj ) {

		// Remove messages
		unset( $obj->messages );

		$output = '<pre>';
		$output .= print_r( $obj, TRUE );
		$output .= '</pre>';

		$this->debug_div( $output );
	}
}

?>
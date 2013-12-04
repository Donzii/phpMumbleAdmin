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

class PMA_output_info_panel {

	private $fills = array();

	/**
	* Add a fill
	*/
	function add_fill( $datas, $type = 'base' ) {
		$this->fills[] = array( 'type' => $type, 'datas' => $datas );
	}

	function add_button( $datas ) {
		$this->fills[] = array( 'type' => 'button', 'datas' => $datas );
	}

	/**
	*
	*/
	function output() {

		if ( empty( $this->fills ) ) {
			return;
		}

		$output = '<div id="info_panel" class="bg1">'.EOL;

		foreach( $this->fills as $array ) {

			$function = 'fill_'.$array['type'];

			switch( $array['type'] ) {

				case 'button':

					$output .= $array['datas'];
					break;

				case 'base':
				case 'occasional':
				case 'right':

					$output .= HTML::$function( $array['datas'] );
					break;
			}
		}

		$output .= '</div><!-- info_panel - END -->'.EOL.EOL;

		return $output;
	}
}

?>
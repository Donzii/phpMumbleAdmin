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

class PMA_output_expand_menu {

	private $output;
	private $title;
	private $entries = array();

	function __construct( $title = '' ) {
		$this->title = $title;
	}

	private function img( $img ) {

		if ( $img === '' ) {
			return IMG_SPACE_16;
		}

		return $img;
	}

	function add( $text, $img = '' ) {

		$array['link'] = FALSE;
		$array['text'] = $text;
		$array['img'] = $this->img( $img );

		$this->entries[] = $array;
	}

	function add_link( $href, $text, $img = '', $misc = '' ) {

		$array['link'] = TRUE;
		$array['href'] = $href;
		$array['text'] = $text;
		$array['img'] = $this->img( $img );
		$array['misc'] = $misc;

		$this->entries[] = $array;
	}

	function add_separation() {
		$this->entries[] = 'sep';
	}

	function output() {

		$this->output = '<div class="expand tab">'.$this->title.HTML::img( IMG_ARROW_DOWN ).'<!-- menu expend - START -->'.EOL;
		$this->output .= '<ul>'.EOL;

		foreach( $this->entries as $array ) {

			if ( is_string( $array ) && $array === 'sep' ) {
				$this->output .= '<li><hr></li>'.EOL;
				continue;
			}

			$content = HTML::img( $array['img'] ).$array['text'];

			if ( $array['link'] ) {
				$this->output .= '<li><a href="'.$array['href'].'" '.$array['misc'].'>'.$content.'</a></li>'.EOL;
			} else {
				$this->output .= '<li>'.$content.'</li>'.EOL;
			}
		}


		$this->output .= '</ul>'.EOL;
		$this->output .= '</div><!-- menu expend - END -->'.EOL;

		return $this->output;
	}
}

?>
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

class PMA_output_messageBox extends PMA_output {

	private $options;

	function __construct( $array ) {

		$this->options = pma_parse_options( $array['options'] );

		$this->cache .= '<div class="messageBox oBox">'.EOL;
		$this->top_url();
		$this->cache .= '<div class="pad oBox '.$array['type'].'">'.EOL;
		$this->close_button();
		$this->title();
		$this->body( $array['key'] );
		$this->url();
		$this->cache .= '</div>'.EOL;
		$this->cache .= '</div>'.EOL.EOL;
	}

	private function get_text( $key ) {

		global $TEXT;

		if ( isset( $TEXT[ $key ] ) ) {

			return $TEXT[ $key ];

		} else {
			return $key;
		}
	}

	private function close_button() {

		if ( isset( $this->options['nobutton'] ) ) {
			return;
		}

		// js + html link
		$this->cache .= '<a href="./" onClick="remove_element( this.parentNode.parentNode ); return false;">';
		$this->cache .= HTML::img( IMG_CANCEL_16, 'button', $this->get_text( 'close' ) ).'</a>'.EOL;
	}

	private function title() {

		if ( ! isset( $this->options['title'] ) ) {
			return;
		}

		$title = $this->get_text( $this->options['title'] );

		if ( isset( $this->options['ice_error'] ) ) {
			$title = '<span class="title">'.$this->get_text( 'ice_error' ).'</span>'.$title;
		}

		$this->cache .= '<span class="title">'.$title.'</span>'.EOL;
	}

	private function body( $key ) {

		$body = $this->get_text( $key );

		if ( isset( $this->options['sprintf'] ) ) {
			$body = sprintf( $body, $this->options['sprintf'] );
		}

		$this->cache .= '<div class="text">'.$body.'</div>'.EOL;
	}

	private function top_url() {

		if ( ! isset( $this->options['top_url'] ) ) {
			return;
		}

		if ( isset( $this->options['top_url_text'] ) ) {
			$text = $this->get_text( $this->options['top_url_text'] );
		} else {
			$text = $this->options['top_url'];
		}

		$this->cache .= '<div class="txtR"><a style="color: red;" href="'.$this->options['top_url'].'">'.$text.'</a></div>';
	}

	private function url() {

		if ( ! isset( $this->options['url'] ) ) {
			return;
		}

		if ( isset( $this->options['url_text'] ) ) {
			$text = $this->get_text( $this->options['url_text'] );
		} else {
			$text = $this->options['url'];
		}

		$this->cache .= '<a href="'.$this->options['url'].'">'.$text.'</a>';
	}
}

?>
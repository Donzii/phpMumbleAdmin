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

class PMA_output_table extends PMA_output {

	/**
	* The minimum of lines before allow to paging a table. ( do not modify this value )
	*/
	const CONF_MINIMUM_LINES = 10;

	/**
	* The minimum of lines a table must have.
	*/
	const MINIMUM_LINES = 10;

	private $datas;
	private $id;
	private $columns = array();
	private $per_lines_function;
	private $bottom = '';

	function __construct( $datas, $id, $per_lines_function ) {

		$this->datas = $datas;
		$this->id = $id;
		$this->per_lines_function = $per_lines_function;
	}

	private function headers() {

		$output = '<tr class="pad">';

		foreach( $this->columns as $array ) {
			$output .= '<th class="'.$array['css'].'">'.$array['text'].'</th>';
		}

		$output .= '</tr>'.EOL;

		return $output;
	}

	function sort_datas( $default_key, $force = FALSE ) {

		if ( ! is_string( $default_key ) OR $default_key === '' ) {
			return;
		}

		$this->sort = new PMA_output_table_sort( $this->id, $default_key );
		$this->sort->order( $this->datas, $force );
	}

	function paging_datas( $lines ) {

		if ( ! is_int( $lines ) OR $lines < self::CONF_MINIMUM_LINES ) {
			return;
		}

		$this->paging = new PMA_output_table_paging( $this->id, $this->datas, $lines );
	}

	/**
	* Expand a table to have a minimum of lines.
	*
	* @param int $total - total of lines of the current table page.
	* @param int $columns - total of columns the table have.
	*/
	private function minimum_lines() {

		$total = count( $this->datas );
		$columns = count( $this->columns );

		if ( $total > self::MINIMUM_LINES ) {
			return;
		}

		$lines = self::MINIMUM_LINES - $total;

		return str_repeat( '<tr>'.str_repeat( '<td></td>', $columns ).'</tr>'.EOL, $lines );
	}

	function add_column( $css = '', $text = '' ) {
		$this->columns[] = array(
			'css' => $css,
			'text' => $text
		);
	}

	function add_bottom( $text ) {
		$this->bottom .= $text;
	}

	function output() {

		$function = $this->per_lines_function;

		$output = '';

		if ( isset( $this->paging ) ) {
			$output .= $this->paging->menu();
		}

		$output .= '<table id="'.$this->id.'">'.EOL;
		$output .= $this->headers();
		$output .= $function( $this->datas );
		$output .= $this->minimum_lines();

		if ( is_string( $this->bottom ) && $this->bottom !== '' ) {
			$output .= '<tr class="pad"><th colspan="'.count( $this->columns ).'">'.$this->bottom.'</th></tr>'.EOL;
		}

		$output .= '</table>'.EOL.EOL;

		if ( isset( $this->paging ) ) {
			$output .= $this->paging->menu();
		}

		return $output;
	}
}


?>
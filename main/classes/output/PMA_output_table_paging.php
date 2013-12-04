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

class PMA_output_table_paging extends PMA_output {

	// Table page ( for session caching ).
	private $page;

	// Table name ( for session caching ).
	private $name;

	// Maximum of entries per pages
	private $max_of_entries;

	// Current page number
	private $current_page;

	// By default, there is always one page.
	private $total_of_pages = 1;

	function __construct( $name, &$table, $max_of_entries ) {

		$total_of_entries = count( $table );

		$this->page = $_SESSION['page'];

		$this->name = $name;

		$this->max_of_entries = $max_of_entries;

		if ( $total_of_entries > 0 && $this->max_of_entries >= 10 ) {

			$this->total_of_pages = (int) ceil( $total_of_entries / $this->max_of_entries );
		}

		$this->get_current_page();
		$this->update_session();

		// Chunk
		if ( $this->total_of_pages > 1 ) {
			$this->chunk( $table );
		}
	}

	/**
	* Get current page number
	*/
	private function get_current_page() {

		if ( isset( $_GET['table'] ) && ctype_digit( $_GET['table'] ) ) {

			$this->current_page = (int)$_GET['table'];

		} elseif ( ! isset( $_SESSION['page_'.$this->page ]['table_'.$this->name ] ) ) {

			$this->current_page = 1;

		} else {

			$this->current_page = $_SESSION['page_'.$this->page ]['table_'.$this->name ];
		}

		// Current page can't be superior than the total of page
		if ( $this->current_page > $this->total_of_pages ) {

			$this->current_page = $this->total_of_pages;
		}
	}

	/**
	* Keep current page number in session
	*/
	private function update_session() {
		$_SESSION['page_'.$this->page ]['table_'.$this->name ] = $this->current_page;
	}

	/**
	* Chunk the table
	*/
	private function chunk( &$table ) {

		// array_chunk(): chunks an array into size large chunks.
		// The last chunk may contain less than size elements.
		$chunk = array_chunk( $table, $this->max_of_entries, TRUE );

		$table = $chunk[ $this->current_page -1 ];
	}

	/**
	* Construct the menu, and cache the HTML code for the bottom of the page.
	*
	* @Return string - HTML menu
	*/
	function menu() {

		if ( ! is_null( $this->cache ) ) {
			return $this->cache;
		}

		// No need to construct the menu for one page.
		if ( $this->total_of_pages < 2 ) {
			return $this->cache;
		}

		if ( $this->total_of_pages <= 5 ) {
			$range = range( 1, $this->total_of_pages );

		// Construct the page range of the menu if superior than 5 pages ( range of 5 pages max ).
		} else {

			// 3 first pages range
			if ( $this->current_page <= 3 ) {
				$range = range( 1, 5 );

			// 3 last pages range
			} elseif ( ( $this->total_of_pages - $this->current_page ) <= 2 ) {
				$range = range( $this->total_of_pages-4, $this->total_of_pages );

			// All others range
			} else {
				$range = range( $this->current_page-2, $this->current_page+2 );
			}
		}

		global $TEXT;

		$marginL = 'style="margin-left: 10px;"';
		$marginR = 'style="margin-right: 10px;"';

		$this->cache .= '<div class="table_paging">'.EOL;

		// first / prev ( << < )
		if ( $this->current_page > 1 ) {

			// First
			$this->cache .= '<a title="'.$TEXT['go_first'].'" href="?table=1">';
			$this->cache .= HTML::img( 'tango/page_first_16.png' ).'</a>'.EOL;
			// Prev
			$this->cache .= '<a title="'.$TEXT['go_prev'].'" href="?table='.( $this->current_page - 1 ).'" '.$marginR.'>';
			$this->cache .= HTML::img( 'tango/page_prev_16.png' ).'</a>'.EOL;

		} else {
			$this->cache .= '<span>'.HTML::img( IMG_SPACE_16 ).'</span>'.EOL;
			$this->cache .= '<span '.$marginR.'>'.HTML::img( IMG_SPACE_16 ).'</span>'.EOL;
		}

		// Pages range
		foreach ( $range as $page ) {

			if ( $this->current_page === $page ) {
				$this->cache .= '<span class="selected">'.$page.'</span>'.EOL;

			} else {
				$this->cache .= '<a href="?table='.$page.'">'.$page.'</a>'.EOL;
			}
		}

		// next / last ( > >> )
		if ( $this->current_page < $this->total_of_pages ) {

			// Next
			$this->cache .= '<a  title="'.$TEXT['go_next'].'" href="?table='.( $this->current_page + 1 ).'" '.$marginL.'>';
			$this->cache .= HTML::img( 'tango/page_next_16.png' ).'</a>'.EOL;
			// Last
			$this->cache .= '<a title="'.$TEXT['go_last'].'" href="?table='.$this->total_of_pages.'">';
			$this->cache .= HTML::img( 'tango/page_last_16.png' ).'</a>'.EOL;

		} else {
			$this->cache .= '<span '.$marginL.'>'.HTML::img( IMG_SPACE_16 ).'</span>'.EOL;
			$this->cache .= '<span>'.HTML::img( IMG_SPACE_16 ).'</span>'.EOL;
		}

		// Total of pages
		$this->cache .=  '<span style="padding: 0px 5px;">'.sprintf( $TEXT['total_pages'], $this->total_of_pages ).'</span>'.EOL;

		// GO button
		if ( $this->total_of_pages > 9 ) {
			$this->cache .= '<form method="GET" action="" onSubmit="return unchanged( this.table );">';
			$this->cache .= '<input type="text" style="width: 50px;" name="table"><input type="submit" value="GO"></form>'.EOL;
		}

		$this->cache .=  '</div>'.EOL.EOL;

		return $this->cache;
	}
}


?>
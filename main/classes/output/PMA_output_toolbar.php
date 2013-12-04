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

class PMA_output_toolbar {

	/**
	* @param $tabs - array of all tabs: array( key => text );
	* @param $name - session name to keep the key.
	*
	* @return string
	*/
	static function tabs( $tabs, $name ) {

		$str = '';

		$page = $_SESSION['page'];

		// Change tab if valid
		if ( isset( $_GET['toolbartab'] ) && isset( $tabs[ $_GET['toolbartab'] ] ) ) {
			$_SESSION['page_'.$page ][ $name ] = $_GET['toolbartab'];
		}

		// Default tab - first key
		if ( ! isset( $_SESSION['page_'.$page ][ $name ] ) ) {
			$_SESSION['page_'.$page ][ $name ] = key( $tabs );
		}

		// Print tabs
		foreach( $tabs as $key => $text ) {

			if ( $text === '' ) {
				$text = $key;
			}

			if ( $key === $_SESSION['page_'.$page ][ $name ] ) {
				$str .= '<a href="?toolbartab='.$key.'" class="tab selected">'.$text.'</a>'.EOL;

			} else {
				$str .= '<a href="?toolbartab='.$key.'" class="tab">'.$text.'</a>'.EOL;
			}
		}

		return $str;
	}

	static function search( $id, $found = NULL ) {

		global $TEXT;

		if ( isset( $_SESSION['search'][ $id ] ) ) {

			$search_value = $_SESSION['search'][ $id ];

			$reset = '';
			if ( $found !== NULL ) {
				$reset .= $TEXT['found'].' : <span class="safe">'.$found.'</span>'.EOL;
			}
			$reset .= '<a href="?cmd=murmur_'.$id.'&amp;reset_'.$id.'_search">';
			$reset .= HTML::img( IMG_CANCEL_22, 'button', $TEXT['clean_search'] ).'</a>'.EOL;

		} else {
			$reset = '';
			$search_value = '';
		}

		$output = '<form id="search" action="" method="post" onSubmit="return unchanged( this.'.$id.'_search );">'.EOL;
		$output .= '<input type="hidden" name="cmd" value="murmur_'.$id.'">'.EOL;
		$output .= $reset;
		$output .= '<input type="text" name="'.$id.'_search" value="'.$search_value.'">'.EOL;
		$output .= '<input type="submit" value="'.$TEXT['search'].'">'.EOL;
		$output .= '</form>'.EOL;

		return $output;
	}
}

?>
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

// Generic class for HTML code.
class HTML {

	static function disabled( $bool ) {

		if ( $bool === TRUE ) {
			return 'disabled="disabled"';
		}
	}

	static function chked( $bool ) {

		if ( $bool === TRUE ) {
			return 'checked="checked"';
		}
	}

	static function selected( $bool ) {

		if ( $bool === TRUE ) {
			return 'selected="selected"';
		}
	}

	static function clear() {
		return '<div class="clear"></div>';
	}

	static function info_bubble( $visible, $desc, $right = FALSE ) {

		if ( $right === TRUE ) {
			$css = 'bubble r';
		} else {
			$css = 'bubble';
		}

		return '<span class="'.$css.'">'.$visible.'<span class="desc">'.$desc.'</span></span>';
	}

	static function img( $src, $css = '', $title = '', $id = '' ) {

		$str = '<img src="images/'.$src.'" ';

		if ( $css !== '' ) {
			$str .= 'class="'.$css.'" ';
		}

		if ( $title !== '' ) {
			$str .= 'title="'.$title.'" ';
		}

		if ( $id !== '' ) {
			$str .= 'id="'.$id.'" ';
		}

		return $str .'alt="">';
	}

	/**
	* @param $bool - boolean
	*
	* @return string - src image on/off ( TRUE/FALSE )
	*/
	static function bool_state( $bool ) {

		if ( $bool === TRUE ) {
			return IMG_OK_16;
		}

		return IMG_CANCEL_16;
	}

	/**
	* @return string - online users
	*/
	static function online_users( $count, $max_users ) {

		if ( is_string( $max_users ) ) {

			if ( ! ctype_digit( $max_users ) ) {
				$max_users = 0;
			} else {
				$max_users = (int)$max_users;
			}

		} elseif ( is_int( $max_users ) && $max_users < 0 ) {
			$max_users = 0;
		} else {
			$max_users = 0;
		}

		if ( $count > 0 ) {

			if ( $max_users === 0 ) {

				$css = 'unsafe';

			} elseif ( ( $count * 100 / $max_users ) <= 70 ) {

				$css = 'safe';

			} elseif ( ( $count * 100 / $max_users ) <= 90 ) {

				$css = 'warn';

			} else {

				$css = 'unsafe';
			}

			$count = '<span class="'.$css.'">'.$count.'</span>';
		}

		return $count.' / '.$max_users;
	}

	/**
	* Fills - base
	*/
	static function fill_base( $datas, $css = '' ) {
		return '<span class="fill '.$css.'">'.$datas.'</span>'.EOL;
	}

	/**
	* Fills - occasional
	*/
	static function fill_occasional( $datas ) {
		return self::fill_base( $datas, 'occ' );
	}

	/**
	* Fills - right
	*/
	static function fill_right( $datas ) {
		return self::fill_base( $datas, 'occ right' );
	}
}

?>
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

/**
* Mumble user avatar HTML box
*
* @param $prx
* @param $uid int user id.
*
* @return string
*/
class avatar {

	private $img = '';

	function __construct( $prx, $uid ) {

		$getTexture = $prx->getTexture( $uid );

		if ( empty( $getTexture ) ) {
			return;
		}

		$blob = array_dec_to_chars( $getTexture );

		if ( is_writeable( PMA_DIR_AVATARS ) ) {

			$file = PMA_DIR_AVATARS .sha1( $blob );

			if ( ! file_exists( $file ) ) {

				$this->cache_file( $file, $blob );
			}

			$this->img = $file;

		} else {

			// Print avatar anyway
			$this->img = 'data:image/png;base64, '.base64_encode( $blob );
		}
	}

	private function cache_file( $file, $blob ) {

		$fp = fopen( $file, 'wb' );
		fwrite( $fp, $blob );
		fclose( $fp );
	}

	function img() {

		global $TEXT;

		if ( $this->img !== '' ) {
			return '<img src="'.$this->img.'" title="'.$TEXT['user_avatar'].'" alt="">';
		} else {
			return cut_long_str( $TEXT['no_avatar'], 18 );
		}
	}

	function delete_link() {

		global $TEXT;

		if ( $this->img !== '' ) {
			return '<a href="?remove_avatar" onClick="return del_avatar();">'.$TEXT['delete_avatar'].'</a>';
		}
	}
}

?>
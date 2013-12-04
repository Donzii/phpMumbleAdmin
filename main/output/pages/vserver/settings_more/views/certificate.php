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

echo '<div class="description" style="width: 500px; margin: auto;">'.EOL;
if ( ! empty( $certificate ) ) {
	echo PMA_output_certificate::get( $certificate );
}
echo '</div>'.EOL.EOL;

// ADD A CERTFICATE
echo '<div style="margin: 5px 10px;"><span class="fill occ">'.$TEXT['add_certificate'].'</span></div>'.EOL;

// Check if we can upload files
if ( ini_get( 'file_uploads' ) && is_writeable( ini_get( 'upload_tmp_dir' ) ) ) {
	// file upload
	echo '<form method="post" action="" enctype="multipart/form-data" onSubmit="return unchanged( this.add_certificate );">'.EOL;
	echo '<input type="hidden" name="cmd" value="murmur_settings">'.EOL;
	// Memo: MAX_FILE_SIZE make php to return an upload error if file is too big.
	echo '<input type="hidden" name="MAX_FILE_SIZE" value="20480">'.EOL; // 20 KB
	echo '<input type="file" name="add_certificate"> <input type="submit" value="'.$TEXT['submit'].'"> ( '.convert_size( 20480, 'byte' ).' max )'.EOL;
	echo '</form>'.EOL;
} else {
	// textarea
	echo '<form method="post" action="" onSubmit="return unchanged( this.add_certificate );">'.EOL;
	echo '<input type="hidden" name="cmd" value="murmur_settings">'.EOL;
	echo '<textarea name="add_certificate" rows="10" cols="4"></textarea>'.EOL;
	echo '<div style="margin: 10px 0px;" class="txtR"><input type="submit" value="'.$TEXT['submit'].'"></div>'.EOL;
	echo '</form>'.EOL;
}

?>

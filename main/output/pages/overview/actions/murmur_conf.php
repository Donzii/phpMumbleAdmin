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

if ( ! $PMA->user->is_min( CLASS_ROOTADMIN ) ) {
	pma_illegal_operation();
}

$defaultConf = $PMA->meta->getDefaultConf();

echo '<div class="toolbar">';
echo '<a href="./" title="'.$TEXT['cancel'].'">'.HTML::img( IMG_CANCEL_22, 'button right' ).'</a>'.EOL;
echo '</div>';

echo '<table class="config">'.EOL;
echo '<tr class="invisible"><th style="width: 200px;"></th><th></th></tr>'.EOL;
echo '<tr class="pad"><th style="width: 200px;" class="title">'.$TEXT['default_settings'].'</th>';
echo '<th class="title">'.sprintf( $TEXT['murmur_vers'], $PMA->meta->txt_version ).'</th></tr>'.EOL;

foreach( $defaultConf as $key => $desc ) {

	// Don't print certificate and private key.
	if ( $key === 'key' OR $key === 'certificate' ) {
		continue;
	}

	echo '<tr class="small"><th>'.$key.'</th><td>'.html_encode( $defaultConf[ $key ] ).'</td></tr>'.EOL;
}

echo '</table>'.EOL;
?>
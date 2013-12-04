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

echo '<div id="page">'.EOL.EOL;

if ( $PMA->user->is_min( CLASS_USER ) ) {
	require 'page_menu.php';
}

if ( isset( $OUTPUT->info_panel ) ) {
	echo $OUTPUT->info_panel->output();
}

if ( isset( $PMA->tabs ) ) {
	require 'tabs.php';
}

echo '<div id="contents">'.EOL;
echo '<div class="'.$OUTPUT->box.'">'.EOL.EOL;
echo $OUTPUT->get_cache();
echo '</div><!-- box - END -->'.EOL;
echo '</div><!-- contents - END -->'.EOL.EOL;
echo '</div><!-- page - END -->'.EOL.EOL;

?>
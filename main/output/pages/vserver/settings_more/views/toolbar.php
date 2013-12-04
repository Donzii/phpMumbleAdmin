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

echo '<div class="toolbar">'.EOL;

echo PMA_output_toolbar::tabs( $tabs, 'more_tab' );

// Reset certificate link
if ( isset( $custom_conf['certificate'] ) && $_SESSION['page_vserver']['more_tab'] !== 'welcometext' ) {
	echo '<a href="?reset_certificate" class="right" onClick="return reset_certificate();">';
	echo HTML::img( IMG_CLEAN_16, 'button', sprintf( $TEXT['reset_param'], 'certificate' ) ).'</a>'.EOL;
}

echo '</div>'.EOL.EOL;

?>

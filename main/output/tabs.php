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

$tabs = $PMA->tabs->get_avalaibles();

if ( count( $tabs ) < 2 ) {
	return;
}

echo '<ul id="tabs">'.EOL;

foreach( $tabs as $tab ) {

	// Add missing txt
	if ( isset( $TEXT['tab_'.$tab ] ) ) {

		$txt = $TEXT['tab_'.$tab ];

	} else {

		if ( PMA_DEBUG > 0 ) {
			$txt = '<span class="unsafe">$TXT</span> '.$tab;
		} else {
			$txt = $tab;
		}
	}

	if ( $tab === $_SESSION['page_'.$PMA->pages->current() ]['tab'] ) {

		$selected = ' class="selected"';

	} else {
		$selected = '';
	}

	echo '<li'.$selected.'><a href="?tab='.$tab.'">'.$txt.'</a></li>'.EOL;
}

echo '</ul><!-- tabs - END -->'.EOL.EOL;

?>
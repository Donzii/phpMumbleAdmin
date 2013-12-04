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

function footer_whos_online( $list ) {

	global $TEXT;

	sort_array_by( $list, 'class' );

	$profiles = PMA_profiles::instance();

	// Count unauthenticated users.
	$unauth = 0;

	$output = '';

	foreach( $list as $array ) {

		if ( $array['class'] === CLASS_UNAUTH ) {

			++$unauth;
			continue;
		}

		// Special for mumble user
		if ( isset( $array['mumble_id'] ) ) {
			$array['classname'] = $array['classname'].' ( profile '.$profiles->get_name( $array['profile_id'] ).', server id #'.$array['sid'].' )';
		}

		$output .=  '<span class="'.$array['classname'].' help" title="'.$array['classname'].'">'.html_encode( $array['login'] ).'</span>'.EOL;
	}

	if ( $unauth > 0 ) {
		$output .=  '<span class="unauth">'.sprintf( $TEXT['total_unauth'], $unauth ).'</span>'.EOL;
	}

	return $output;
}

echo '<div id="whos_online" class="oBox">'.EOL;
echo HTML::img( 'tango/whois_22.png', 'left', $TEXT['whos_online'] ).EOL;
echo footer_whos_online( $list );
echo '</div><!-- whos_online - END -->'.EOL.EOL;

?>
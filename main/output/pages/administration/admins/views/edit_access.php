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

function fills_info( $access ) {

	if ( ! is_array( $access ) OR empty( $access ) ) {
		return;
	}

	global $TEXT;

	$profiles = PMA_profiles::instance();

	$profile_id = PMA_cookie::instance()->get( 'profile_id' );

	$output = '';

	foreach( $access as $iceid => $servers ) {

		if ( $iceid === $profile_id ) {
			$class = 'fill occ';
		} else {
			$class = 'fill';
		}

		$name = html_encode( $profiles->get_name( $iceid ) );

		if ( $servers === '*' ) {
			$output .= '<span class="'.$class.'">'.sprintf( ''.$TEXT['full_access'], $name ).'</span>';
		} else {
			$count = count( explode( ';', $servers ) );
			$output .= '<span class="'.$class.'">'.sprintf( $TEXT['srv_access'], $name, $count ).'</span>';
		}
	}

	return $output;
}

// Output admin registration overview

$id = $registration['id'];
$class = pma_class_name( $registration['class'] );
$login = html_encode( $registration['login'] );
$created = '<span class="help" title="'.PMA_helpers_dates::uptime( PMA_TIME - $registration['created'] ).'">'.PMA_helpers_dates::complet( $registration['created'] ).'</span>';

if ( $registration['last_conn'] > 0 ) {
	$last_conn = '<span class="help" title="'.PMA_helpers_dates::complet( $registration['last_conn'] ).'">'.PMA_helpers_dates::uptime( PMA_TIME - $registration['last_conn'] ).'</span>';
} else {
	$last_conn = '';
}

if ( $registration['email'] !== '' ) {
	$email = '<a href="mailto:'.$registration['email'].'" title="mailto:'.$registration['email'].'">'.$registration['email'].'</a>';
} else {
	$email = '';
}

echo '<div class="toolbar">'.EOL;
echo '<a href="./?tab=admins" title="'.$TEXT['cancel'].'">'.HTML::img( IMG_CANCEL_22, 'button right' ).'</a>'.EOL;
echo '<a href="?edit_registration">'.HTML::img( IMG_EDIT_22, 'button left' ).'</a>'.EOL;
echo '</div>'.EOL.EOL;

echo '<table class="config oBox">'.EOL;

echo '<tr class="small"><th colspan="2" class="txtL">'.$id.'# [<span class="'.$class.'">'.$class.'</span>] '.$login.'</th></tr>'.EOL;

echo '<tr class="small"><th>'.$TEXT['registered_date'].'</th><td>'.$created.'</td></tr>'.EOL;
echo '<tr class="small"><th >'.$TEXT['last_conn'].'</th><td>'.$last_conn.'</td></tr>'.EOL;
echo '<tr class="small"><th>'.$TEXT['email_addr'].'</th><td class="email">'.$email.'</td></tr>'.EOL;
echo '<tr class="small"><th>'.$TEXT['user_name'].'</th><td>'.$registration['name'].'</td></tr>'.EOL;

echo '<tr class="pad"><td class="hide"></td></tr>'.EOL;

echo '<tr><td colspan="2" class="txtL">'.fills_info( $registration['access'] ).'</td>'.EOL;

echo '</table>'.EOL.EOL;

if ( $registration['class'] !== CLASS_ROOTADMIN ) {
	require 'edit_access_expand.php';
}

?>
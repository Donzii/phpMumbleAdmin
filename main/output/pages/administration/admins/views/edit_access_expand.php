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

// Check admin access for a server id.
function chk_admin_access( $sid, $list ) {

	if ( ! is_array( $list ) ) {
		return FALSE;
	}

	return ( in_array( (string) $sid, $list, TRUE ) );
}

/**
* New scroll output
*/
function _scroll( $accessList ) {

	$vservers = PMA_vservers_cache::instance()->get_current();

	if ( ! isset( $vservers['vservers'] ) ) {
		return 'Error: Failed to get the servers list.'.EOL;
	}

	// Memo: id is required for JS
	$output = '<div id="edit_admin_access" class="oBox" style="width: 500px; height: 500px; overflow:auto; margin: auto; background: #dbdbdb;">'.EOL;

	foreach( $vservers['vservers'] as $sid => $array ) {


		$label = 's'.$sid;
		$checked = HTML::chked( chk_admin_access( $sid, $accessList ) );

		$output .= '<div>';
		$output .= '<input type="checkbox" id="'.$label.'" name="'.$sid.'" '.$checked.' onClick="uncheck( \'full_access\' );">';
		$output .= '<label for="'.$label.'">'.$sid.'# '.$array['name'].'</label>';
		$output .= '</div>'.EOL;
	}

	$output .= '</div>'.EOL.EOL;

	return $output;
}

/**
* Old table output
*/
// function _table( $accessList ) {

// 	$vservers = PMA_vservers_cache::instance()->get_current();

// 	if ( ! isset( $vservers['vservers'] ) ) {
// 		return '<td>Error: Failed to get the servers list.</td>'.EOL;
// 	}

// 	$count = count( $vservers['vservers'] );

// 	$last_key = end( $vservers['vservers'] );

// 	// Define how many columns the table must have, based on servers count:
// 	switch( TRUE ) {

// 		case ( $count < 20 ):

// 			$max_cols = 1;
// 			break;

// 		case ( $count < 40 ):

// 			$max_cols = 2;
// 			break;

// 		case ( $count < 60 ):

// 			$max_cols = 3;
// 			break;

// 		case ( $count <= 80 ):

// 			$max_cols = 4;
// 			break;

// 		case ( $count > 80 ):

// 			$max_cols = 5;
// 			break;
// 	}

// 	$i = 1;

// 	$output = '';

//	// Memo: id is required for JS
//	$output = '<table id="edit_admin_access" class="oBox" style="width: auto;">'.EOL;
//	$output .= '<tr>'.EOL;

// 	foreach( $vservers['vservers'] as $sid => $array ) {

// 		$label = 's'.$sid;
// 		$checked = HTML::chked( chk_admin_access( $sid, $accessList ) );

// 		// New column
// 		if ( $i === 1 ) {
// 			$output .= '<th style="width: 150px;">'.EOL;
// 		}

// 		$output .= '<div>';
// 		$output .= '<input type="checkbox" id="'.$label.'" name="'.$sid.'" '.$checked.' onClick="uncheck( \'full_access\' );">';
// 		$output .= '<label for="'.$label.'">'.$sid.'# '.$array['name'].'</label>';
// 		$output .= '</div>'.EOL;

// 		// Close column
// 		if (
// 			(int) ceil( $count / $max_cols ) === $i
// 			OR $sid === $last_key
// 		) {

// 			if ( $count < 10 ) {
// 				$output .= str_repeat( '<div>-</div>', ( 10 - $count ) );
// 			}

// 			$output .= '</th>'.EOL;

// 			$i = 0;
// 		}

// 		++$i;
// 	}

// 	$output .= '</tr>'.EOL;
// 	$output .= '</table>'.EOL;

// 	return $output;
// }

$pid = $PMA->cookie->get( 'profile_id' );

if ( isset( $registration['access'][ $pid ] ) ) {
	$accessList = explode( ';', $registration['access'][ $pid ] );
} else {
	$accessList = array();
}

$full_access = ( isset( $accessList[0] ) && $accessList[0] === '*' );

echo '<form id="js_admin_access" class="oBox mtop" method="post" action="" onSubmit="return form_is_modified( this );">'.EOL;

echo '<input type="hidden" name="cmd" value="config_admins">'.EOL;
echo '<input type="hidden" name="edit_admin_access">'.EOL.EOL;

// buttons
echo '<div class="oBox">'.EOL;
echo '<input type="reset" value="'.$TEXT['reset'].'" style="margin-right: 20px;">'.EOL;
// Show "all", "none" and "invert" buttons only if JS is activated
echo '<script type="text/javascript">admin_edit_access_buttons( "'.$TEXT['all'].'", "'.$TEXT['none'].'", "'.$TEXT['invert'].'" );</script>'.EOL;
echo '<input type="submit" class="right" value="'.$TEXT['apply'].'">'.EOL;
echo '</div>'.EOL.EOL;

// Full access
echo '<span class="fill occ big" style="margin: 10px;">';
echo '<input type="checkbox" '.HTML::chked( $full_access ).' id="fa" name="full_access" onClick="full_access_toggle( this );">';
echo '<label for="fa">'.$TEXT['enable_full_access'].'</label></span>'.EOL.EOL;

// Access vservers list
echo _scroll( $accessList );
//echo _table( $accessList );

echo '</form>'.EOL.EOL;

?>
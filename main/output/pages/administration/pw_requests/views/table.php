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

function pw_request_table_per_lines( $list ) {

	global $TEXT;

	$cookie = PMA_cookie::instance();

	$output = '';

	foreach( $list as $request ) {

		$time = date( $cookie->get( 'time' ), $request['start'] );
		$date = strftime( $cookie->get( 'date' ), $request['start'] );
		$profile_name = PMA_profiles::instance()->get_name( $request['profile_id'] );

		$output .= '<tr>';
		$output .= '<td title="'.sprintf( $TEXT['started_at'], $date, $time ).'">';
		$output .= '<span class="help">'.PMA_helpers_dates::uptime( $request['end'] - PMA_TIME ).'</span></td>';
		$output .= '<td>'.html_encode( $request['login'] ).'</td>';
		$output .= '<td>'.$request['ip'].'</td>';
		$output .= '<td class="icon help" title="'.$profile_name.'">'.$request['profile_id'].'</td>';
		$output .= '<td class="icon">'.$request['sid'].'</td>';
		$output .= '<td class="icon">'.$request['uid'].'</td>';
		$output .= '<td title="'.$request['id'].'">'.$request['id'].'</td>';
		$output .= '</tr>'.EOL;
	}

	return $output;
}

$list = PMA_pw_requests::instance()->get_all();

$TABLE = new PMA_output_table( $list, 'pw_request', 'pw_request_table_per_lines' );
$TABLE->sort_datas( 'end', TRUE );

$TABLE->add_column( 'large', $TABLE->sort->url( 'end', $TEXT['end'] ) );
$TABLE->add_column( '', $TABLE->sort->url( 'login', $TEXT['login'] ) );
$TABLE->add_column( 'large', $TABLE->sort->url( 'ip', $TEXT['ip_addr'] ) );
$TABLE->add_column( 'id', $TABLE->sort->url( 'profile_id', 'iid', 'short' ) );
$TABLE->add_column( 'id', 'sid' );
$TABLE->add_column( 'id', 'uid' );
$TABLE->add_column( 'large', $TEXT['request_id'] );

$TABLE->add_bottom( '( <span class="help" title="'.$TEXT['ice_profile'].'">iid</span> )' );
$TABLE->add_bottom( '( <span class="help" title="'.$TEXT['sid'].'">sid</span> )' );
$TABLE->add_bottom( '( <span class="help" title="'.$TEXT['uid'].'">uid</span> )' );

echo '<div class="toolbar">';
echo HTML::img( 'tango/clock_22.png' ).' '.$TEXT['pw_request_pending'];
echo '</div>'.EOL;

echo $TABLE->output();

?>
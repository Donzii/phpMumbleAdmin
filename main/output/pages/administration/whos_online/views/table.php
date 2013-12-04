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

function whos_online_table_per_lines( $list ) {

	global $TEXT;

	$profiles = PMA_profiles::instance();

	$sessid = session_id();

	$output = '';

	foreach( $list as $array ) {

		$class = '';

		$iid = '<td class="icon"></td>';
		$sid = $array['sid'];
		$uid = $array['uid'];

		$last_activity = PMA_helpers_dates::uptime( PMA_TIME - $array['last_activity'] );

		if ( isset( $array['proxy'] ) ) {
			$img = HTML::img( 'xchat/red_16.png', 'help', $TEXT['proxyed'].' ( first IP: '.$array['proxy'].' )' );
			$array['current_ip'] = $img.' '.$array['current_ip'];
		}

		if ( $array['class'] !== CLASS_UNAUTH ) {

			$class = $array['classname'];

			$array['login'] = html_encode( $array['login'] );

			if ( $array['profile_id'] !== '' ) {
				$iid = '<td class="icon help" title="'.$profiles->get_name( $array['profile_id'] ).'">'.$array['profile_id'].'</td>';
			}
		}

		$output .= '<tr>';
		$output .= '<td class="b '.$array['classname'].'">'.$class.'</td>';
		$output .= '<td>'.$array['login'].'</td>';
		$output .= '<td>'.$array['current_ip'].'</td>';
		$output .= $iid;
		$output .= '<td class="icon">'.$sid.'</td>';
		$output .= '<td class="icon">'.$uid.'</td>';
		$output .= '<td>'.$last_activity.'</td>';
		$output .= '</tr>'.EOL;
	}

	return $output;
}

$list = PMA_whos_online::instance()->get_all();

$TABLE = new PMA_output_table( $list, 'whos_online_table', 'whos_online_table_per_lines' );
$TABLE->sort_datas( 'class', TRUE );

$TABLE->add_column( 'small', $TABLE->sort->url( 'class', $TEXT['class'] ) );
$TABLE->add_column( '', $TABLE->sort->url( 'login', $TEXT['login'] ) );
$TABLE->add_column( 'vlarge', $TABLE->sort->url( 'current_ip', $TEXT['ip_addr'] ) );
$TABLE->add_column( 'id', $TABLE->sort->url( 'profile_id', 'iid', 'short' ) );
$TABLE->add_column( 'id', 'sid' );
$TABLE->add_column( 'id', 'uid' );
$TABLE->add_column( 'large', $TABLE->sort->url( 'last_activity', $TEXT['last_activity'] ) );

$total = count( $list );

$unauth = 0;

foreach( $list as $array ) {
	if ( $array['class'] === CLASS_UNAUTH ) {
		++$unauth;
	}
}

$auth = $total - $unauth;

$TABLE->add_bottom( '( <span class="help" title="'.$TEXT['ice_profile'].'">iid</span> )' );
$TABLE->add_bottom( '( <span class="help" title="'.$TEXT['sid'].'">sid</span> )' );
$TABLE->add_bottom( '( <span class="help" title="'.$TEXT['uid'].'">uid</span> ) - ' );
$TABLE->add_bottom( sprintf( $TEXT['sessions_infos'], $total, '<span class="safe">'.$auth.'</span>', '<span class="unsafe">'.$unauth.'</span>' ) );


echo '<div class="toolbar">';
echo HTML::img( 'tango/whois_22.png' ).' '.$TEXT['whos_online'];
echo '</div>'.EOL;

echo $TABLE->output();

?>
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

function bubble_info( $access ) {

	if ( ! is_array( $access ) OR empty( $access ) ) {
		return;
	}

	global $TEXT;

	$profiles = PMA_profiles::instance();

	$output = '';

	foreach( $access as $iceid => $servers ) {

		$name = html_encode( $profiles->get_name( $iceid ) );

		if ( $servers === '*' ) {
			$output .= sprintf( $TEXT['full_access'], $name ).'<br>';
		} else {
			$count = count( explode( ';', $servers ) );
			$output .= sprintf( $TEXT['srv_access'], $name, $count ).'<br>';
		}
	}

	return HTML::info_bubble( HTML::img( IMG_INFO_16 ), $output, TRUE );
}

function admins_table_per_lines( $list ) {

	global $TEXT;

	$output = '';

	foreach( $list as $array ) {

		if ( ! PMA_user::instance()->is_superior( $array['class'] ) ) {
			continue;
		}

		$classname = pma_class_name( $array['class'] );

		if ( $array['last_conn'] > 0 ) {
			$date = PMA_helpers_dates::complet( $array['last_conn'] );
			$last_conn = '<span class="help" title="'.$date.'">'.PMA_helpers_dates::uptime( PMA_TIME - $array['last_conn'] ).'</span>';
		} else {
			$last_conn = '';
		}

		$js_del = 'return del_admin( this, \''.$array['id'].'\', \''.$array['login'].'\' );';

		$output .= '<tr>';
		$output .= '<td class="b '.$classname.'">'.$classname.'</td>';
		$output .= '<td class="id">'.$array['id'].'</td>';
		$output .= '<td  class="selection b"><a href="?admin='.$array['id'].'">'.html_encode( $array['login'] ).'</a></td>';
		$output .= '<td class="icon" style="overflow: visible;">'.bubble_info( $array['access'] ).'</td>';
		$output .= '<td>'.$last_conn.'</td>';
		$output .= '<td class="icon"><a href="?remove_admin='.$array['id'].'" title="'.$TEXT['del_admin'].'" onClick="'.$js_del.'">';
		$output .= HTML::img( IMG_TRASH_16, 'button' ).'</a></td>';
		$output .= '</tr>'.EOL;
	}

	return $output;
}

$list = $PMA->admins->get_all();

$TABLE = new PMA_output_table( $list, 'admins', 'admins_table_per_lines' );
$TABLE->sort_datas( 'id' );

$TABLE->add_column( 'small', $TABLE->sort->url( 'class', $TEXT['class'] ) );
$TABLE->add_column( 'id', $TABLE->sort->url( 'id', 'id', 'short' ) );
$TABLE->add_column( '', $TABLE->sort->url( 'login', $TEXT['login'] ) );
$TABLE->add_column( 'icon', 'A' );
$TABLE->add_column( 'large', $TABLE->sort->url( 'last_conn', $TEXT['last_conn'] ) );
$TABLE->add_column( 'icon' );

echo '<div class="toolbar">'.EOL;
echo '<a href="?add_admin" title="'.$TEXT['add_admin'].'">'.HTML::img( IMG_ADD_22, 'button' ).'</a>'.EOL;
echo '</div>'.EOL.EOL;

echo $TABLE->output();

?>
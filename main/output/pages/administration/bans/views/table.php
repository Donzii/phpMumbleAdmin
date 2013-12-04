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

function pma_bans_table_per_lines( $list ) {

	global $TEXT;

	$output = '';

	foreach( $list as $key => $array ) {

		$delete = '<a href="">'.HTML::img( IMG_TRASH_16, 'button' ).'</a>';

		$output .= '<tr>';
		$output .= '<td>'.$array['ip'].'</td>';
		$output .= '<td>'.PMA_helpers_dates::complet( $array['start'] ).'</td>';
		$output .= '<td>'.$array['duration'].'</td>';
		$output .= '<td>'.$array['type'].'</td>';
		$output .= '<td>'.$delete.'</td>';
		$output .= '</tr>'.EOL;
	}

	return $output;
}

$list = PMA_bans::instance()->get_all();

$TABLE = new PMA_output_table( $list, 'pma_bans', 'pma_bans_table_per_lines' );
$TABLE->sort_datas( 'ip' );

$TABLE->add_column( 'large', $TABLE->sort->url( 'ip', $TEXT['ip_addr'] ) );
$TABLE->add_column( 'small', $TABLE->sort->url( 'start', 'Start' ) );
$TABLE->add_column( 'small', $TABLE->sort->url( 'duration', $TEXT['end'] ) );
$TABLE->add_column( 'small', $TABLE->sort->url( 'type', 'Type' ) );
$TABLE->add_column( 'icon' );

echo '<div class="toolbar">'.EOL;
echo '</div>'.EOL;

echo $TABLE->output();



?>
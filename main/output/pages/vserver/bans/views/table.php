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

function bans_per_lines( $bans ) {

	global $TEXT;

	$output = '';

	foreach ( $bans as $key => $obj ) {

		$ip = ip_dec_to_str( $obj->address );

		// mask 128 bits mean ip only, no need to show the mask
		if ( $obj->bits === 128 ) {
			$mask = '';
		} else {

			if ( $ip['type'] === 'ipv4' ) {
				$mask = ip_mask_6to4( $obj->bits );
			} else {
				$mask = $obj->bits;
			}

			$mask = ' / <span class="safe">'.$mask.'</span>';
		}

		if ( $obj->name !== '' ) {
			$name = '<span class="user">'.html_encode( $obj->name ).'</span>';
		} else {
			$name = '';
		}

		if ( $obj->reason !== '' ) {
			$reason = '<span class="info">'.html_encode( $obj->reason ).'</span>';
		} else {
			$reason = '';
		}

		if ( $obj->hash !== '' ) {
			$hash = HTML::img( IMG_OK_16, '', $TEXT['cert_included'] );
		} else {
			$hash = '';
		}

		$start = PMA_helpers_dates::complet( $obj->start, 'separator=<br>' );

		if ( $obj->duration > 0 ) {
			$ts = $obj->start + $obj->duration;
			$end = PMA_helpers_dates::complet( $ts, 'separator=<br>' );;
		} else {
			$end = '';
		}

		$output .= '<tr>';
		$output .= '<td class="selection"><a href="?edit_ban_id='.$key.'"><b>'.$ip['ip'].$mask.'</b><br>'.$name.$reason.'</a></td>';
		$output .= '<td class="icon">'.$hash.'</td>';
		$output .= '<td>'.$start.'</td>';
		$output .= '<td>'.$end.'</td>';
		$output .= '<td class="icon"><a href="?delete_ban_id='.$key.'" onClick="return del_ban( this, \''.$key.'\' );">';
		$output .= HTML::img( IMG_TRASH_16, 'button', $TEXT['del_ban'] ).'</a></td>';
		$output .= '</tr>'.EOL;
	}

	return $output;
}

$TABLE = new PMA_output_table( $getBans, 'bans', 'bans_per_lines' );
$TABLE->paging_datas( $PMA->config->get( 'table_bans' ) );

$TABLE->add_column();
$TABLE->add_column( 'icon' );
$TABLE->add_column( 'large', $TEXT['started'] );
$TABLE->add_column( 'large', $TEXT['end'] );
$TABLE->add_column( 'icon' );

// Toolbar
echo '<div class="toolbar">'.EOL;
echo '<a title="'.$TEXT['add_ban'].'" href="?addBan">'.HTML::img( IMG_ADD_22, 'button' ).'</a>'.EOL;
echo '</div>'.EOL.EOL;

echo $TABLE->output();

?>

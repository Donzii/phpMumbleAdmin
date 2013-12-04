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

/**
* Output overview table line per line
*/
function vservers_table_per_lines( $servers ) {

	global $conf;

	$output = '';

	foreach( $servers as $array ) {

		$prx = new PMA_vserver( $array['prx'] );

		$id = $prx->sid();
		$booted = ( $array['status'] === 1 );
		$name = $prx->get_conf( 'registername' );
		$host = $prx->get_conf( 'host' );
		$port = $prx->get_conf( 'port' );
		$max_users = $prx->get_conf( 'users' );

		if ( check_ipv6( $host ) ) {
			$host = '['.$host.']';
		}

		if ( $prx->get_conf( 'PMA_permitConnection' ) === 'TRUE' ) {

			$web_access_img = 'xchat/red_16.png';

		} else {
			$web_access_img = IMG_2_DELETE_16;
		}

		$status = 'off';
		$connurl = '';
		$online_users = '';
		$uptime = '';

		if ( $booted ) {

			$status = 'on';
			$connurl = '<a href="'.$prx->url().'">'.HTML::img( IMG_CONN_16, 'button' ).'</a>';

			if ( $conf->show_online_users ) {

				try {
					$count = count( $prx->getUsers() );

				} catch ( Exception $Ex ) {

					pma_murmur_exception( $Ex );
					$count = 'error';
				}

				$online_users = HTML::online_users( $count, $max_users );
			}

			if ( $conf->show_uptime ) {

				try {
					$uptime = PMA_helpers_dates::started_at( $prx->getUptime() );

				} catch ( Exception $Ex ) {

					pma_murmur_exception( $Ex );
					$uptime = 'error';
				}
			}
		}

		// Show $server_id session.
		if ( isset( $_SESSION['page_vserver']['id'] ) && $_SESSION['page_vserver']['id'] === $id ) {
			$css_sid = 'id selected';
		} else {
			$css_sid = 'id';
		}

		$output .= '<tr>';

		// Satus
		$output .= '<td class="icon"><a href="?cmd=overview&amp;toggle_server_status='.$id.'">';
		$output .= HTML::img( IMG_SPACE_16, 'button '.$status ).'</a></td>';

		// Server id
		$output .= '<td class="'.$css_sid.'">'.$id.'</td>';

		// Server name
		$output .= '<td class="selection b"><a href="?page=vserver&amp;sid='.$id.'">'.html_encode( $name ).'<br>';
		$output .= '<span class="info n">'.$host.' : '.$port.'</span></a></td>';

		// Reset server
		$output .= '<td class="icon">';
		$output .= '<a href="?action=reset_vserver&amp;id='.$id.'" onClick="return reset_vserver( this, \''.$id.'\', \''.$name.'\' );">';
		$output .= HTML::img( 'gei/hot_16.png', 'button' ).'</a></td>';

		// Connection url
		$output .= '<td class="icon">'.$connurl.'</td>';

		// Online user
		if ( $conf->show_online_users ) {
			$output .= '<td class="b">'.$online_users.'</td>';
		}

		// Uptime
		if ( $conf->show_uptime ) {
			$output .= '<td>'.$uptime.'</td>';
		}

		// Web access
		$output .= '<td class="icon"><a href="?cmd=overview&amp;toggle_web_access='.$id.'">';
		$output .= HTML::img( $web_access_img, 'button' ).'</a></td>';

		// Delete vserver
		if ( $conf->user_can_delete ) {
			$output .= '<td class="icon">';
			$output .= '<a href="?action=delete_vserver&amp;id='.$id.'" onClick="return del_vserver( this, \''.$id.'\', \''.$name.'\' );">';
			$output .= HTML::img( IMG_TRASH_16, 'button' ).'</a></td>';
		}

		$output .= '</tr>'.EOL;
	}

	return $output;
}

$JS->add_text( 'add_vserver', $TEXT['add_srv'] );
$JS->add_text( 'gen_su_pw', $TEXT['generate_su_pw'] );
$JS->add_text( 'send_msg_all_vservers', $TEXT['msg_all_srv'] );
$JS->add_text( 'del_vserver', $TEXT['confirm_del_server'] );
$JS->add_text( 'reset_vserver', $TEXT['confirm_reset_srv'] );

$conf = new stdClass();

$conf->show_online_users = (
	$PMA->config->get( 'show_online_users' )
	&& ( $PMA->user->is_min( CLASS_ROOTADMIN ) OR ! $PMA->config->get( 'show_online_users_sa' ) )
);

$conf->show_uptime = (
	method_exists( 'Murmur_Server', 'getUptime' )
	&& $PMA->config->get( 'show_uptime' )
	&& ( $PMA->user->is_min( CLASS_ROOTADMIN ) OR ! $PMA->config->get( 'show_uptime_sa' ) )
);

$conf->user_can_delete = $PMA->user->is_min( CLASS_ADMIN_FULL_ACCESS );

$vservers = array();
$key = 0;
foreach( $lists['vservers'] as $prx ) {

	if ( in_array( $prx, $lists['booted'] ) ) {
		$booted = 1;
	} else {
		$booted = 2;
	}

	$vservers[ $key ]['id'] = $key;
	$vservers[ $key ]['status'] = $booted;
	$vservers[ $key ]['prx'] = $prx;

	++$key;
}

$TABLE = new PMA_output_table( $vservers, 'overview', 'vservers_table_per_lines' );
$TABLE->sort_datas( 'id' );
$TABLE->paging_datas( $PMA->config->get( 'table_overview' ) );

$TABLE->add_column( 'icon', $TABLE->sort->url( 'status', 's', 'short' ) );
$TABLE->add_column( 'id', $TABLE->sort->url( 'id', 'id', 'short' ) );
$TABLE->add_column( '', $TEXT['srv_name'] );
$TABLE->add_column( 'icon' );
$TABLE->add_column( 'icon' );

if ( $conf->show_online_users ) {
	$TABLE->add_column( 'small' );
}

if ( $conf->show_uptime ) {
	$TABLE->add_column( 'large' );
}

$TABLE->add_column( 'icon' );

if ( $conf->user_can_delete ) {
	$TABLE->add_column( 'icon' );
}

echo $TABLE->output();

// Captions
echo '<div style="margin: 10px 0px;">'.EOL;
echo '<span class="caption">'.EOL;
echo '<span style="margin-right: 10px;">'.$TEXT['caption'].'</span>'.EOL;
echo HTML::info_bubble( HTML::img( IMG_SPACE_16, 'button on' ), $TEXT['srv_active'] ).EOL;
echo HTML::info_bubble( HTML::img( IMG_SPACE_16, 'button off' ), $TEXT['srv_inactive'] ).EOL;
echo HTML::info_bubble( HTML::img( 'gei/hot_16.png', 'button' ), $TEXT['reset_srv_info'] ).EOL;
echo HTML::info_bubble( HTML::img( IMG_CONN_16, 'button' ), $TEXT['conn_to_srv'] ).EOL;
echo HTML::info_bubble( HTML::img( 'xchat/red_16.png', 'button' ), $TEXT['webaccess_on'] ).EOL;
echo HTML::info_bubble( HTML::img( IMG_2_DELETE_16, 'button' ), $TEXT['webaccess_off'] ).EOL;
echo HTML::info_bubble( HTML::img( IMG_TRASH_16, 'button' ), $TEXT['del_srv'] ).EOL;
echo '</span>'.EOL;
echo '</div>'.EOL.EOL;

?>
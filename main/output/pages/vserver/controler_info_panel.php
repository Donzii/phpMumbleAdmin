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

// Show the panel if the vserver is offline at anytime.
if ( ! $PMA->cookie->get( 'infoPanel' ) && $isRunning ) {
	return;
}

$OUTPUT->info_panel = new PMA_output_info_panel();

// Start / stop button
if ( $PMA->user->is_min( CLASS_SUPERUSER_RU ) ) {

	if ( $isRunning ) {

		$status = 'on';
		$title = $TEXT['srv_active'];

	} else {
		$status = 'off';
		$title = '';
	}

	if ( $PMA->user->is_min( CLASS_ADMIN ) OR $PMA->config->get( 'SU_start_vserver' ) ) {

		$sid = $getServer->sid();
		$img = HTML::img( IMG_SPACE_16, 'button '.$status );

		$OUTPUT->info_panel->add_button( '<a href="?cmd=overview&amp;toggle_server_status='.$sid.'" title="'.$title.'">'.$img.'</a>' );
	}
}

if ( $isRunning ) {

	// Vserver connection button
	$mumble_url = $getServer->url();

	$OUTPUT->info_panel->add_button( '<a class="right" href="'.$mumble_url.'">'.HTML::img( IMG_CONN_16, 'button', $TEXT['conn_to_srv'] ).'</a>' );

	if ( method_exists( 'Murmur_server', 'getUptime' ) ) {
		try {
			$get = $getServer->getUptime();

			if ( $get > 0 ) {
				$OUTPUT->info_panel->add_fill( sprintf( $TEXT['fill_uptime'], PMA_helpers_dates::started_at( $get ) ) );
			}

		} catch ( Exception $Ex ) {
			pma_murmur_exception( $Ex );
		}
	}

	$OUTPUT->info_panel->add_fill( sprintf( $TEXT['fill_users'], '<b>'.HTML::online_users( $count_getUsers, $getServer->get_conf( 'users' ) ) ).'</b>' );

	if ( isset( $count_getChannels ) ) {
		$OUTPUT->info_panel->add_fill( sprintf( $TEXT['fill_channels'], '<span class="nb">'.$count_getChannels.'</span>' ), 'occasional' );
	}

	if ( isset( $count_getRegisteredUsers ) && $PMA->user->is_min( CLASS_SUPERUSER_RU ) ) {
		$OUTPUT->info_panel->add_fill( sprintf( $TEXT['fill_registrations'], '<span class="nb">'.$count_getRegisteredUsers.'</span>' ), 'occasional' );
	}

	if ( isset( $count_getBans ) ) {
		$OUTPUT->info_panel->add_fill( sprintf( $TEXT['fill_bans'], '<span class="nb">'.$count_getBans.'</span>' ), 'occasional' );
	}

} else {

	if (
		$PMA->user->is_min( CLASS_ADMIN )
		OR ( $PMA->user->is_min( CLASS_SUPERUSER_RU ) && $PMA->config->get( 'SU_start_vserver' ) )
	) {
		$OUTPUT->info_panel->add_fill( $TEXT['srv_inactive'], 'occasional' );
	}
}

if ( $PMA->tabs->current() === 'logs' ) {

	if ( isset( $getLogsLen ) ) {

		if ( $vserver_logs_size !== -1 && $getLogsLen > $vserver_logs_size ) {

			$count_logs = $vserver_logs_size.' / '.$getLogsLen;

		} else {
			$count_logs = $getLogsLen;
		}

	} elseif ( isset( $getLogs ) ) {

		$count_logs = count( $getLogs );

	} else {
		$count_logs = 0;
	}

	$OUTPUT->info_panel->add_fill( sprintf( $TEXT['fill_logs'], '<span class="nb">'.$count_logs.'</span>' ), 'occasional' );
}

?>
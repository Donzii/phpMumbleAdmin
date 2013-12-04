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

// Aucheck for update
if ( $PMA->user->is_min( CLASS_ROOTADMIN ) ) {

	$PMA->updates = new PMA_updates();
	$PMA->updates->autocheck( $PMA->config->get( 'update_check' ) );
}

// Initiate ICE connection
$PMA->meta = PMA_meta::instance( $PMA->user->get_profile() );

if ( ! pma_ice_conn_is_valid() ) {
	return;
}

// Vservers and booted lists
try {
	$lists['vservers'] = $PMA->meta->getAllServers();
	$lists['booted'] = $PMA->meta->getBootedServers();

} catch ( Exception $Ex ) {

	pma_murmur_exception( $Ex );

	$lists['vservers'] = array();
	$lists['booted'] = array();
}

// Current admin $lists['vservers'] & $lists['booted'] traitement
if ( $PMA->user->is( CLASS_ADMIN ) ) {

	$newa = array();
	$newb = array();

	foreach( $lists['vservers'] as $prx ) {

		// Get vserver sid:
		$sid = PMA_vserver::prx_to_sid( $prx );

		if ( $PMA->user->check_admin_sid( $sid ) ) {

			$newa[ $sid ] = $prx;

			if ( in_array( $prx, $lists['booted'] ) ) {

				$newb[] = $prx;
			}
		}
	}

	$lists['vservers'] = $newa;
	$lists['booted'] = $newb;
}

$murmur = sprintf( $TEXT['murmur_vers'], '<b>'.$PMA->meta->str_version.'</b>' );

$uptime = '';

try {

	$get = $PMA->meta->getUptime();

	if ( $get > 0 ) {
		$uptime = ' ( '.PMA_helpers_dates::started_at( $get ).' )';
	}

} catch ( Exception $Ex ) {

	pma_murmur_exception( $Ex );
	$uptime = ' ( uptime error )';
}

$murmur .= $uptime;

require 'controler_info_panel.php';
require 'route.php';

?>

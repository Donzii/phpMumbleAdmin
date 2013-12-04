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

class PMA_output_external_viewer extends PMA_output {

	private $meta;

	private $total = 0;

	function __construct( $meta, $servers ) {

		$this->meta = $meta;

		try {

			$this->get_servers( $servers );

		} catch ( exception $ex ) {

			if ( PMA_EXT_VIEWER_DEBUG ) {
				pdump( $ex );
			}
			die;
		}
	}

	private function get_servers( $servers ) {

		// Get all ( booted )
		if ( $servers === '*' ) {

			$booted = $this->meta->getBootedServers();

			foreach( $booted as $prx ) {
				$this->get_viewer( new PMA_vserver( $prx ) );
			}

		// Get a list of servers
		} else {

			$servers = explode( '-', $servers );

			foreach( $servers as $sid ) {

				if ( ! ctype_digit( $sid ) ) {
					continue;
				}

				$this->get_viewer( $this->meta->getServer( (int) $sid ) );
			}
		}
	}

	private function get_viewer( $prx ) {

		if ( ! is_object( $prx ) ) {
			return;
		}

		if ( ! $prx->isRunning() ) {
			return;
		}

		$tree = $prx->getTree();

		$chans = $prx->getChannels();
		$users = $prx->getUsers();

		$default_channel_id = (int) $prx->get_conf( 'defaultchannel' );
		$server_name = $prx->get_conf( 'registername' );

		$viewer = new PMA_output_viewer( $prx, $tree, $chans, $users );

		$viewer->disable();
		$viewer->set( 'default_channel_id', $default_channel_id );
		$viewer->set( 'vserver_name', $server_name );

		$this->cache .= '<div class="ext_viewer oBox">'.EOL;
		$this->cache .= $viewer->output();
		$this->cache .= '</div>'.EOL.EOL;
	}
}

?>
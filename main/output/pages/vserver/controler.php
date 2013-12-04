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

require 'controler_tabs.php';

if ( $PMA->user->is( CLASS_ADMIN ) ) {

	// Check if current admin have access to current vserver id
	if ( ! $PMA->user->check_admin_sid( $_SESSION['page_vserver']['id'] ) ) {

		$PMA->pages->set( 'overview' );

		msg_debug( __file__ .': Admin do not have access to vserver '.$_SESSION['page_vserver']['id'] );

		unset( $_SESSION['page_vserver'] );

		pma_redirect();
	}
}

// Force SuperUsers and registered users to access to their own vserver.
if ( $PMA->user->is_in( MUMBLE_USERS ) ) {
	$_SESSION['page_vserver']['id'] = $_SESSION['auth']['server_id'];
}

$PMA->meta = PMA_meta::instance( $PMA->user->get_profile() );

if ( ! pma_ice_conn_is_valid() ) {
	return;
}

// get server instance
try {
	$getServer = $PMA->meta->getServer( $_SESSION['page_vserver']['id'] );

} catch ( Exception $Ex ) {
	pma_murmur_exception( $Ex );
}

if ( ! isset( $getServer ) ) {

	if ( $PMA->user->is_min( CLASS_ADMIN ) ) {

		$PMA->pages->set( 'overview' );

		unset( $_SESSION['page_vserver'] );

	} else {

		// Mumble users, logout
		pma_logout();
		msg_box( 'vserver_dont_exists', 'error' );
	}

	pma_redirect();
}

define( 'VSERVER_NAME', $getServer->get_conf( 'registername' ) );

try {
	$isRunning = $getServer->isRunning();

} catch ( Exception $Ex ) {

	$isRunning = FALSE;

	pma_murmur_exception( $Ex );
}

if ( $isRunning ) {

	// Check if a registered user have SuperUser_ru rights.
	if ( $PMA->user->is_in( ALL_REGISTERED_USERS ) ) {

		// Memo: redirect is required on class update.
		if ( $getServer->is_superuser_ru( $PMA->user->mumble_id ) ) {

			if ( $PMA->user->is( CLASS_USER ) ) {
				$PMA->user->set_class( CLASS_SUPERUSER_RU );
				pma_redirect();
			}

		} else {

			if ( $PMA->user->is( CLASS_SUPERUSER_RU ) ) {
				$PMA->user->set_class( CLASS_USER );
				pma_redirect();
			}
		}
	}

	try {
		$getUsers = $getServer->getUsers();
		$count_getUsers = count( $getUsers );

	} catch ( Exception $Ex ) {

		$getUsers = array();
		$count_getUsers = 'error';

		pma_murmur_exception( $Ex );
	}

	switch( $PMA->tabs->current() ) {

		case 'channels':

			try {
				$getChannels = $getServer->getChannels();
				$count_getChannels = count( $getChannels );

			} catch ( Exception $Ex ) {

				pma_murmur_exception( $Ex );

				$getChannels = array();
				$count_getChannels = 'error';
			}

			try {
				$getTree = $getServer->getTree();

			} catch ( Exception $Ex ) {

				pma_murmur_exception( $Ex );
			}
			break;

		case 'registrations':

			try {
				$getRegisteredUsers = $getServer->getRegisteredUsers( '' );
				$count_getRegisteredUsers = count( $getRegisteredUsers );

			} catch ( Exception $Ex ) {

				$getRegisteredUsers = array();
				$count_getRegisteredUsers = 'error';

				pma_murmur_exception( $Ex );
			}
			break;

		case 'bans':

			try {
				$getBans = $getServer->getBans();
				$count_getBans = count( $getBans );

			} catch ( Exception $Ex ) {

				$getBans = array();
				$count_getBans = 'error';

				pma_murmur_exception( $Ex );
			}
			break;
	}
}

if ( $PMA->tabs->current() === 'logs' ) {

	$vserver_logs_size = $PMA->config->get( 'vlogs_size' );

	try {
		$getLogs = $getServer->getLog( 0, $vserver_logs_size );

	} catch ( Exception $Ex ) {

		$array = pma_murmur_exception( $Ex );

		if ( in_istring( $array['text'], 'MemoryLimitException' ) ) {

			$vserver_logs_size = 100;

			msg_box( 'iceMemoryLimitException_logs', 'error' );

			$getLogs = $getServer->getLog( 0, $vserver_logs_size );
		}
	}

	// getLogLen() come with murmur 1.2.3.
	if ( method_exists( 'Murmur_Server', 'getLogLen' ) ) {

		try {
			$getLogsLen = $getServer->getLogLen();

		// MEMO: Murmur_InvalidSecretException class comes with murmur 1.2.3
		} catch ( Murmur_InvalidSecretException $Ex ) {
			// Bug with murmur 1.2.3: getLogLen require icesecretwrite, do nothing.
		}
	}
}

require 'controler_info_panel.php';
require 'route.php';

?>
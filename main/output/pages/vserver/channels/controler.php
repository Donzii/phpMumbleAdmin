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

pma_load_language( 'vserver_channels' );

$JS->add_text( 'send_msg', $TEXT['send_msg'] );

define( 'DEFAULT_CHANNEL_ID', (int) $getServer->get_conf( 'defaultchannel' ) );

echo '<div id="viewerLeft" class="left">'.EOL;

if ( $PMA->user->is_min( CLASS_SUPERUSER_RU ) ) {

	// Change CHANNEL ID
	if ( isset( $_GET['channel'] ) && ctype_digit( $_GET['channel'] ) ) {

		$_GET['channel'] = (int)$_GET['channel'];

		// Remove acl id & group id if we change channel id
		if ( isset( $_SESSION['page_vserver']['cid'] ) && $_SESSION['page_vserver']['cid'] !== $_GET['channel'] ) {
			unset( $_SESSION['page_vserver']['aclID'], $_SESSION['page_vserver']['groupID'] );
		}

		$_SESSION['page_vserver']['cid'] = $_GET['channel'];

		// Remove user session id
		unset( $_SESSION['page_vserver']['uSess'] );
	}

	// Check for valid channel ID
	if ( isset( $_SESSION['page_vserver']['cid'] ) ) {

		// Channel id dont exists
		if ( ! isset( $getChannels[ $_SESSION['page_vserver']['cid'] ] ) ) {

			msg_box( 'InvalidChannelException', 'error' );

			$_SESSION['page_vserver']['cid'] = 0;

			unset( $_SESSION['page_vserver']['aclID'], $_SESSION['page_vserver']['groupID'] );
		}
	}

	// change USER SESSION
	if ( isset( $_GET['userSession'] ) ) {

		list( $id, $name ) = explode( '-', rawUrlDecode( $_GET['userSession'] ), 2 );

		$_SESSION['page_vserver']['uSess']['id'] = (int) $id;
		$_SESSION['page_vserver']['uSess']['name'] = $name;

		// Remove channel id, acl id & group id
		unset( $_SESSION['page_vserver']['cid'], $_SESSION['page_vserver']['aclID'], $_SESSION['page_vserver']['groupID'] );
	}

	// Check for valid user session ID
	if ( isset( $_SESSION['page_vserver']['uSess'] ) ) {

		if ( ! isset( $getUsers[ $_SESSION['page_vserver']['uSess']['id'] ] ) ) {

			// Find if the user has reconnected.
			foreach ( $getUsers as $obj ) {

				if ( $obj->name === $_SESSION['page_vserver']['uSess']['name'] ) {

					$_SESSION['page_vserver']['uSess']['id'] = $obj->session;

					$new_session_found = TRUE;

					msg_debug( 'New user session found' );
					break;
				}
			}

			if ( ! isset( $new_session_found ) ) {

				msg_box( 'InvalidSessionException', 'error' );

				$_SESSION['page_vserver']['cid'] = 0;

				unset( $_SESSION['page_vserver']['uSess'] );
			}
		}
	}

	// DEFAULT : Root channel, this is the only thing we are sure to find in a vserver.
	if ( ! isset( $_SESSION['page_vserver']['cid'] ) && ! isset( $_SESSION['page_vserver']['uSess'] ) ) {
		$_SESSION['page_vserver']['cid'] = 0;
	}

	echo '<div id="viewerBox" class="oBox">'.EOL;

	if ( isset( $_SESSION['page_vserver']['uSess'] ) ) {

		require 'box_user.php';

	} elseif ( isset( $_SESSION['page_vserver']['cid'] ) ) {

		require 'box_channel.php';
	}

	echo '</div><!-- viewerBox - end -->'.EOL.EOL;
}

echo '</div><!-- viewerLeft - END -->'.EOL.EOL;

// Viewer controler
if ( isset( $getTree ) ) {

	$viewer = new PMA_output_viewer( $getServer, $getTree, $getChannels, $getUsers );

	$viewer->set( 'default_channel_id', DEFAULT_CHANNEL_ID );
	$viewer->set( 'vserver_name', VSERVER_NAME );
	$viewer->set( 'show_pw_channels', TRUE );
	$viewer->set( 'show_linked_channels', TRUE );
	$viewer->set( 'show_status_icons', TRUE );

	if ( $PMA->user->is( CLASS_USER ) ) {

		$viewer->disable();

	} else {

		if ( isset( $_SESSION['page_vserver']['cid'] ) ) {

			$viewer->select_channel( $_SESSION['page_vserver']['cid'] );

		} elseif( isset( $_SESSION['page_vserver']['uSess'] ) ) {

			$viewer->select_user( $_SESSION['page_vserver']['uSess']['id'] );
		}

		if ( isset( $_GET['action'] ) ) {

			if ( isset( $_GET['to'] ) && ctype_digit( $_GET['to'] ) ) {
				$viewer->set( 'action_to', (int) $_GET['to'] );
			}

			$viewer->set_action( $_GET['action'] );
		}
	}

	echo '<div id="viewer" class="'.$viewer->get_css().'">'.EOL;
	echo $viewer->output();
	echo '</div><!-- viewer - END -->'.EOL.EOL;
}

echo '<div class="clear" ></div>'.EOL;

require 'views/captions.php';

?>

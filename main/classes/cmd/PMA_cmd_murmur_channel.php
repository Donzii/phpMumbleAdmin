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

class PMA_cmd_murmur_channel extends PMA_cmd {

	private $prx;

	private $chan_id;

	function process() {

		if ( ! $this->PMA->user->is_min( CLASS_SUPERUSER_RU ) ) {
			$this->illegal_operation();
		}

		if ( NULL === $this->prx = $this->PMA->meta->getServer( $_SESSION['page_vserver']['id'] ) ) {
			$this->end();
		}

		$this->chan_id = $_SESSION['page_vserver']['cid'];

		if ( isset( $this->POST['add_sub_channel'] ) ) {
			$this->add_sub_channel( $this->POST['add_sub_channel'] );

		} elseif ( isset( $this->POST['send_msg'] ) ) {
			$this->send_msg( $this->POST['send_msg'] );

		} elseif ( isset( $this->POST['delete_channel'] ) ) {
			$this->delete_channel();

		} elseif ( isset( $this->POST['channel_property'] ) ) {
			$this->edit_channel_property();

		} elseif ( isset( $this->POST['move_users_out_the_channel'] ) ) {
			$this->move_users_out_the_channel( $this->POST['move_users_out_the_channel'] );

		} elseif ( isset( $this->POST['move_users_into_the_channel'] ) ) {
			$this->move_users_into_the_channel();

		} elseif ( isset( $this->GET['move_channel_to'] ) ) {
			$this->move_channel_to( $this->GET['move_channel_to'] );

		} elseif ( isset( $this->GET['link_channel'] ) ) {
			$this->link_channel( $this->GET['link_channel'] );

		} elseif ( isset( $this->GET['unlink_channel'] ) ) {
			$this->unlink_channel( $this->GET['unlink_channel'] );

		} elseif ( isset( $this->GET['unlink_all_channel'] ) ) {
			$this->unlink_all_channel();
		}
	}

	private function add_sub_channel( $name ) {

		$CHAN = $this->prx->getChannelState( $this->chan_id );

		// Don't add a sub channel to a temporary.
		// Mumble doesn't accept that so we don't too ( and it can be problematic anyway ).
		if ( $CHAN->temporary ) {
			$this->error( 'temporary_channel' );
		}

		if ( ! $this->prx->validate_chars( 'channelname', $name ) ) {
			$this->error( 'invalid_channel_name' );
		}

		$new = $this->prx->addChannel( $name, $this->chan_id );

		$_SESSION['page_vserver']['cid'] = $new;
		$_SESSION['page_vserver']['cTab'] = 'property';

		unset( $_SESSION['page_vserver']['aclID'], $_SESSION['page_vserver']['groupID'] );
	}

	private function send_msg( $message ) {

		$this->redirection = 'referer';

		if ( $message === '' ) {
			$this->end();
		}

		if ( ! $this->PMA->user->is_min( CLASS_ROOTADMIN )  ) {
			$message = $this->prx->remove_html_tags( $message );
		}

		$message = url_to_HTML( $message );

		$sub = isset( $this->POST['to_all_sub'] );

		$this->prx->sendMessageChannel( $this->chan_id, $sub, $message );
	}

	private function delete_channel() {

		if ( ! isset( $this->POST['confirmed'] ) ) {
			$this->end();
		}

		$CHAN = $this->prx->getChannelState( $this->chan_id );

		$this->prx->removeChannel( $this->chan_id );

		$_SESSION['page_vserver']['cid'] = $CHAN->parent;

		// Remove defaultChannel if we have deleted the default channel.
		if ( $this->prx->get_conf( 'defaultchannel' ) === (string) $this->chan_id ) {
			$this->prx->setConf( 'defaultchannel' , '' );
		}

		unset( $_SESSION['page_vserver']['cTab'], $_SESSION['page_vserver']['aclID'], $_SESSION['page_vserver']['groupID'] );
	}

	private function edit_channel_property() {

		$state = $this->prx->getChannelState( $this->chan_id );

		// Workaround for murmur 1.2.0 bug ( removed with murmur 1.2.1, 2009-12-31 )
		if ( $this->PMA->meta->int_version === 120 && $this->chan_id === 0 ) {
			// Without this, we can't modify root channel state
			$state->parent = 0;
		}

		// Default channel
		if ( isset( $this->POST['defaultchannel'] ) && ! $state->temporary ) {
			// Memo: setConf() require string for second parameter
			$this->prx->setConf( 'defaultchannel', (string) $this->chan_id );
		}

		// Channel name
		if ( isset( $this->POST['name'] ) && $state->name !== $this->POST['name'] ) {

			if ( $this->prx->validate_chars( 'channelname', $this->POST['name'] ) ) {
				$state->name = $this->POST['name'];
			} else {
				$this->error( 'invalid_channel_name' );
			}
		}

		// Description
		if ( $state->description !== $this->POST['description'] ) {
			$state->description = $this->prx->remove_html_tags( $this->POST['description'] );
		}

		// Position
		if ( is_numeric( $this->POST['position'] ) OR $this->POST['position'] === '' ) {
			$state->position = (int) $this->POST['position'];
		} else {
			$this->error( 'invalid_numerical', 'sprintf=channel position' );
		}

		$this->prx->setChannelState( $state );

		// Channel password
		$this->prx->getACL( $this->chan_id, $aclList, $groupList, $inherit );

		PMA_helpers_ACL::remove_inherited( $aclList );
		PMA_helpers_ACL::remove_inherited_groups( $groupList );

		// Check if a password is set.
		$password_is_set = FALSE;
		$password_acl_id = '';

		foreach( $aclList as $key => $obj ) {

			if ( PMA_helpers_ACL::is_token( $obj ) ) {

				$password_is_set = TRUE;
				$password_acl_id = $key;
				break;
			}
		}

		if ( $this->POST['pw'] !== '' ) {

			// Add a new password
			if ( ! $password_is_set ) {

				// Deny all ACL
				$deny_all = new Murmur_ACL();
				$deny_all->group = 'all';
				$deny_all->userid = -1;
				$deny_all->inherited = FALSE;
				$deny_all->applyHere = TRUE;
				$deny_all->applySubs = TRUE;
				$deny_all->allow = 0;
				$deny_all->deny = 908;

				// Password ACL
				$password = new Murmur_ACL();
				$password->group = '#'.$this->POST['pw'];
				$password->userid = -1;
				$password->inherited = FALSE;
				$password->applyHere = TRUE;
				$password->applySubs = TRUE;
				$password->allow = 908;
				$password->deny = 0;

				$aclList[] = $deny_all;
				$aclList[] = $password;

			// edit password
			} else {
				$aclList[ $password_acl_id ]->group = '#'.$this->POST['pw'];
			}

		// Delete the password if the field is empty and a password was set.
		} elseif ( $password_is_set ) {

			unset( $aclList[ $password_acl_id ] );

			// Search for the "deny all" ACL included with the password creation.
			foreach ( $aclList as $key => $obj ) {

				if (
					$obj->group === 'all'
					&& ! $obj->inherited
					&& $obj->applyHere
					&& $obj->applySubs
					&& $obj->allow === 0
					&& $obj->deny === 908
				) {
					$deny_all_acl_id = $key;
					unset( $aclList[ $key ] );
					break;
				}
			}

			if ( isset( $_SESSION['page_vserver']['aclID'] ) ) {

				// Unset selected acl if it's the password or "deny all".
				if ( $_SESSION['page_vserver']['aclID'] === $password_acl_id
				OR $_SESSION['page_vserver']['aclID'] === $deny_all_acl_id
				) {
					unset( $_SESSION['page_vserver']['aclID'] );
				}
			}
		}

		$this->prx->setACL( $this->chan_id, $aclList, $groupList, $inherit );
	}

	private function move_users_out_the_channel( $move_to_chan_id ) {

		if ( ! ctype_digit( $move_to_chan_id ) ) {
			$this->error( 'invalid_numerical' );
		}

		$move_to_chan_id = (int) $move_to_chan_id;

		$users = $this->prx->getUsers();

		foreach ( $users as $user ) {

			// move only users which are in the selected channel
			if ( $user->channel === $this->chan_id ) {

				// move only user selected by admin
				if ( isset( $this->POST[ $user->session ] ) ) {

					$user->channel = $move_to_chan_id;
					$this->prx->setState( $user );
				}
			}
		}
	}

	private function move_users_into_the_channel() {

		$users = $this->prx->getUsers();

		foreach ( $users as $user ) {

			// move only user out of the selected channel
			if ( $user->channel !== $this->chan_id ) {

				// move only user selected by admin
				if ( isset( $this->POST[ $user->session ] ) ) {

					$user->channel = $this->chan_id;
					$this->prx->setState( $user );
				}
			}
		}
	}

	private function move_channel_to( $id ) {

		if ( ! ctype_digit( $id ) ) {
			$this->error( 'invalid_numerical' );
		}

		$id = (int) $id;

		$CHAN = $this->prx->getChannelState( $this->chan_id );

		if ( $CHAN->parent === $id ) {
			$this->error( 'parent_channel' );
		}

		$CHAN->parent = $id;

		try {
			$this->prx->setChannelState( $CHAN );

		} catch ( Murmur_InvalidChannelException $Ex ) {

			// Most probably move to a children channel.
			$this->error( 'children_channel' );
		}
	}

	private function link_channel( $id ) {

		if ( ! ctype_digit( $id ) ) {
			$this->error( 'invalid_numerical' );
		}

		$id = (int) $id;

		$getChannels = $this->prx->getChannels();

		$CHAN = $this->prx->getChannelState( $this->chan_id );

		if ( $CHAN->id !== $id ) {

			$CHAN->links[] = $id;
			$this->prx->setChannelState( $CHAN );
		}

		if ( count( $getChannels ) - count( $CHAN->links ) > 1 ) {
			$this->redirection = 'referer';
		}
	}

	private function unlink_channel( $id ) {

		if ( ! ctype_digit( $id ) ) {
			$this->error( 'invalid_numerical' );
		}

		$id = (int) $id;

		$CHAN = $this->prx->getChannelState( $this->chan_id );

		foreach ( $CHAN->links as $key => $chan_id ) {

			if ( $chan_id === $id ) {

				unset( $CHAN->links[ $key ] );
				$this->prx->setChannelState( $CHAN );
				break;
			}
		}

		if ( ! empty( $CHAN->links ) ) {
			$this->redirection = 'referer';
		}
	}

	private function unlink_all_channel() {

		$CHAN = $this->prx->getChannelState( $this->chan_id );
		$CHAN->links = array();
		$this->prx->setChannelState( $CHAN );
	}
}

?>

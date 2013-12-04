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

class PMA_cmd_overview extends PMA_cmd {

	private $profile_id;

	private $infos;

	function process() {

		if ( ! $this->PMA->user->is_min( CLASS_SUPERUSER_RU ) ) {
			$this->illegal_operation();
		}

		$profile = $this->PMA->user->get_profile();

		// Common log infos
		$this->infos = 'server id: ';
		if ( $this->PMA->profiles->total() > 1 ) {
			$this->infos = 'profile: '.$profile['id'].'# '.$profile['name'].' - '.$this->infos;
		}

		$this->profile_id = $profile['id'];

		if ( isset( $this->POST['add_vserver'] ) ) {
			$this->add_vserver();

		} elseif ( isset( $this->GET['toggle_server_status'] ) ) {
			$this->toggle_server_status( $this->GET['toggle_server_status'] );

		} elseif ( isset( $this->POST['confirm_stop_sid'] ) ) {
			$this->confirm_stop_sid( $this->POST['confirm_stop_sid'] );

		} elseif ( isset( $this->GET['toggle_web_access'] ) ) {
			$this->toggle_web_access( $this->GET['toggle_web_access'] );

		} elseif ( isset( $this->POST['delete_vserver_id'] ) ) {
			$this->delete_vserver_id( $this->POST['delete_vserver_id'] );

		} elseif ( isset( $this->POST['send_msg_vservers'] ) ) {
			$this->send_msg_vservers( $this->POST['send_msg_vservers'] );

		} elseif ( isset( $this->GET['refreshServerList'] ) ) {
			$this->refresh_vserver_list();

		} elseif ( isset( $this->POST['reset_vserver_id'] ) ) {
			$this->reset_vserver_id( $this->POST['reset_vserver_id'] );

		} elseif ( isset( $this->POST['mass_settings'] ) ) {
			$this->mass_settings();
		}
	}

	private function refresh_vserver_cache() {
		PMA_vservers_cache::instance()->refresh( $this->profile_id );
	}

	private function add_vserver() {

		if ( ! $this->PMA->user->is_min( CLASS_ADMIN_FULL_ACCESS ) ) {
			$this->illegal_operation();
		}

		$prx = $this->PMA->meta->newServer();
		$prx->setConf( 'boot', 'FALSE' );
		$this->success( 'vserver_created_success' );

		$this->log_action( 'Virtual server created ( '.$this->infos . $prx->id().' )' );

		if ( isset( $this->POST['new_su_pw'] ) ) {
			$prx->setSuperuserPassword( $pw = gen_random_chars( 16 ) );
			msg_box( 'new_su_pw', 'error', 'nobutton, sprintf='.$pw );
		}

		$this->refresh_vserver_cache();
	}

	private function toggle_server_sanity( &$sid ) {

		// Check if SuperUser have authorization to start / stop his vserver.
		if ( ! $this->PMA->config->get( 'SU_start_vserver' ) && $this->PMA->user->is_in( ALL_SUPERUSERS ) ) {
			$this->illegal_operation();
		}

		if ( ! ctype_digit( $sid ) ) {
			$this->error( 'invalid_numerical' );
		}

		// Set $sid
		if ( $this->PMA->user->is_min( CLASS_ADMIN ) ) {

			$sid = (int) $sid;

		} else {
			$sid = $_SESSION['auth']['server_id'];
		}

		// Check current admin rights for the virtual server
		if ( $this->PMA->user->is( CLASS_ADMIN ) ) {

			if ( ! $this->PMA->user->check_admin_sid( $sid ) ) {
				$this->illegal_operation();
			}
		}

		if ( NULL === $prx = $this->PMA->meta->getServer( $sid ) ) {
			$this->end();
		}

		return $prx;
	}

	/**
	* Start / stop the vserver
	*/
	private function toggle_server_status( $sid ) {

		$prx = $this->toggle_server_sanity( $sid );

		if ( $prx->isRunning() ) {

			$getUsers = $prx->getUsers();

			// Check if the virtual server is empty or display a warning msg.
			if ( empty( $getUsers ) ) {

				$prx->stop();
				$this->log_action( 'Server stopped ( '.$this->infos . $sid.' )' );
				$prx->setConf( 'boot', 'FALSE' );

			} else {

				// Server is not empty, redirect to the confirmation message.
				$this->redirection = './?confirm_stop_sid='.$sid;
				$this->end();
			}

		} else {
			$prx->start();
			$this->log_action( 'Server started ( '.$this->infos . $sid.' )' );
			$prx->setConf( 'boot', '' );
		}
	}

	private function confirm_stop_sid( $sid ) {

		$prx = $this->toggle_server_sanity( $sid );

		if ( ! isset( $this->POST['confirmed'] ) ) {
			$this->illegal_operation();
		}

		if ( ! $prx->isRunning() ) {
			$this->error( 'serverBootedException' );
		}

		$message = $this->POST['msg'];

		if ( $message !== '' ) {

			if ( ! $this->PMA->user->is_min( CLASS_ADMIN_FULL_ACCESS ) ) {
				$message = $prx->remove_html_tags( $message );
			}

			$prx->sendMessageChannel( 0, TRUE, url_to_HTML( $message ) );
		}

		if ( isset( $this->POST['kickUsers'] ) ) {
			$prx->kick_all_users();
		}

		$prx->stop();
		$this->log_action( 'Server stopped ( '.$this->infos . $sid.' )' );
		$prx->setConf( 'boot', 'FALSE' );
	}

	private function toggle_web_access( $sid ) {

		if ( ! $this->PMA->user->is_min( CLASS_ADMIN ) ) {
			$this->illegal_operation();
		}

		if ( ! ctype_digit( $sid ) ) {
			$this->illegal_operation();
		}

		$sid = (int) $sid;

		// Check current admin rights for the virtual server
		if ( $this->PMA->user->is( CLASS_ADMIN ) ) {

			if ( ! $this->PMA->user->check_admin_sid( $sid ) ) {
				$this->illegal_operation();
			}
		}

		if ( NULL === $prx = $this->PMA->meta->getServer( $sid ) ) {
			$this->end();
		}

		if ( $prx->get_conf( 'PMA_permitConnection' ) !== 'TRUE' ) {
			$prx->setConf( 'PMA_permitConnection', 'TRUE' );
			$this->log_action( 'Web access enabled ( '.$this->infos . $sid.' )' );
		} else {
			// Delete the parameter
			$prx->setConf( 'PMA_permitConnection', '' );
			$this->log_action( 'Web access disabled ( '.$this->infos . $sid.' )' );
		}

		$this->refresh_vserver_cache();
	}

	private function delete_vserver_id( $sid ) {

		if ( ! $this->PMA->user->is_min( CLASS_ADMIN_FULL_ACCESS ) ) {
			$this->illegal_operation();
		}

		if ( ! ctype_digit( $sid ) ) {
			$this->illegal_operation();
		}

		$sid = (int) $sid;

		if ( ! isset( $this->POST['confirmed'] ) ) {
			$this->end();
		}

		if ( NULL === $prx = $this->PMA->meta->getServer( $sid ) ) {
			$this->end();
		}

		// You can't delete a running virtual server, so stop it.
		if ( $prx->isRunning() ) {
			$prx->kick_all_users();
			$prx->stop();
		}

		$prx->delete();

		$this->log_action( 'Virtual server deleted ( '.$this->infos . $sid.' )' );
		$this->success( 'vserver_deleted_success' );

		PMA_admins::instance()->del_sid_access( $this->profile_id, $sid );

		$this->refresh_vserver_cache();

		// Unset $_SESSION['page_vserver'] if we deleted the server in session.
		if ( isset( $_SESSION['page_vserver']['id'] ) && $_SESSION['page_vserver']['id'] === $sid ) {
			unset( $_SESSION['page_vserver'] );
		}
	}

	private function send_msg_vservers( $message ) {

		if ( ! $this->PMA->user->is_min( CLASS_ADMIN ) ) {
			$this->illegal_operation();
		}

		if ( $message === '' ) {
			$this->end();
		}

		$booted_servers = $this->PMA->meta->getBootedServers();

		foreach ( $booted_servers as $prx ) {

			$prx = new PMA_vserver( $prx );

			if ( $this->PMA->user->is( CLASS_ADMIN ) ) {
				if ( ! $this->PMA->user->check_admin_sid( $prx->sid() ) ) {
					continue;
				}
			}

			if ( $this->PMA->user->is_in( ALL_ADMINS ) ) {
				$message = $prx->remove_html_tags( $message, FALSE );
			}

			$prx->sendMessageChannel( 0, TRUE, url_to_HTML( $message ) );
		}
	}

	private function refresh_vserver_list() {

		if ( ! $this->PMA->user->is_min( CLASS_ROOTADMIN ) ) {
			$this->illegal_operation();
		}

		$this->redirection = 'referer';
		$this->refresh_vserver_cache();
	}

	private function reset_vserver_id( $sid ) {

		if ( ! $this->PMA->user->is_min( CLASS_ADMIN ) ) {
			$this->illegal_operation();
		}

		// Action cancelled
		if ( ! isset( $this->POST['confirmed'] ) ) {
			$this->end();
		}

		if ( ! ctype_digit( $sid ) ) {
			$this->illegal_operation();
		}

		$sid = (int) $sid;

		// Check current admin rights for the virtual server
		if ( $this->PMA->user->is( CLASS_ADMIN ) ) {
			if ( ! $this->PMA->user->check_admin_sid( $sid ) ) {
				$this->illegal_operation();
			}
		}

		if ( NULL === $prx = $this->PMA->meta->getServer( $sid ) ) {
			$this->end();
		}

		if ( ! $prx->isRunning() ) {
			$prx->start();
		}

		$prx->kick_all_users();

		// DELETE ALL CHANNELS
		$channels = $prx->getChannels();
		foreach ( $channels as $chan ) {
			if ( $chan->id !== 0 && $chan->parent === 0 ) {
				$prx->removeChannel( $chan->id );
			}
		}

		// RESET ROOT CHANNEL PROPERTIES
		$root = $prx->getChannelState( 0 );
		$root->name = 'Root';
		$root->links = array();
		$root->description = '';
		$root->position = 0;
		// Workaround for the 1.2.0 murmur bug with Root channel state
		if ( $this->PMA->meta->int_version === 120 ) {
			$root->parent = 0;
		}
		$prx->setChannelState( $root );

		// RESET ROOT CHANNEL ACL
		$aclList = array();

		$aclList[1] = new Murmur_ACL();
		$aclList[1]->group = 'admin';
		$aclList[1]->userid = -1;
		$aclList[1]->applyHere = TRUE;
		$aclList[1]->applySubs = TRUE;
		$aclList[1]->inherited = FALSE;
		$aclList[1]->allow = 1;
		$aclList[1]->deny = 0;

		$aclList[2] = new Murmur_ACL();
		$aclList[2]->group = 'auth';
		$aclList[2]->userid = -1;
		$aclList[2]->applyHere = TRUE;
		$aclList[2]->applySubs = TRUE;
		$aclList[2]->inherited = FALSE;
		$aclList[2]->allow = 1024;
		$aclList[2]->deny = 0;

		$aclList[3] = new Murmur_ACL();
		$aclList[3]->group = 'all';
		$aclList[3]->userid = -1;
		$aclList[3]->applyHere = TRUE;
		$aclList[3]->applySubs = FALSE;
		$aclList[3]->inherited = FALSE;
		$aclList[3]->allow = 524288;
		$aclList[3]->deny = 0;

		// RESET ROOT CHANNEL GROUPES
		$groupList = array();

		$groupList[1] = new Murmur_Group();
		$groupList[1]->name = 'admin';
		$groupList[1]->inherited = FALSE;
		$groupList[1]->inherit = TRUE;
		$groupList[1]->inheritable = TRUE;
		$groupList[1]->add = array();
		$groupList[1]->members = array();
		$groupList[1]->remove = array();

		$prx->setACL( 0, $aclList, $groupList, FALSE );

		// RESET VIRTUAL SERVER PARAMETERS
		$getAllConf = $prx->getAllConf();
		foreach( $getAllConf as $key => $value ) {
			$prx->setConf( $key, '' );
		}

		// DELETE ALL REGISTERED ACCOUNTS
		$getRegisteredUsers = $prx->getRegisteredUsers( '' );
		foreach( $getRegisteredUsers as $uid => $name ) {
			if ( $uid !== 0 ) {
				$prx->unregisterUser( $uid );
			}
		}

		// Reset SuperUser registration
		$reset_su[0] = 'SuperUser';
		$reset_su[1] = '';
		$reset_su[2] = '';
		$prx->updateRegistration( 0, $reset_su );

		// New SuperUser password
		if ( isset( $this->POST['new_su_pw'] ) ) {
			// New superadmin password
			$prx->setSuperuserPassword( $pw = gen_random_chars( 16 ) );
			msg_box( 'new_su_pw', 'error', 'nobutton, sprintf='.$pw );
		}

		// DELETE ALL BANS
		$prx->setBans( array() );

		// END
		$prx->stop();
		$prx->setConf( 'boot', 'FALSE' );

		if ( isset( $_SESSION['page_vserver']['id'] ) && $_SESSION['page_vserver']['id'] === $sid ) {
			unset( $_SESSION['page_vserver'] );
		}

		$this->log_action( 'Virtual server reseted ( '.$this->infos . $sid.' )' );
		$this->success( 'vserver_reset_success' );
	}

	private function mass_settings() {

		if ( ! $this->PMA->user->is_min( CLASS_ROOTADMIN ) ) {
			$this->illegal_operation();
		}

		$this->redirection = 'referer';

		if ( $this->POST['confirm'] !== $this->POST['confirm_word'] ) {
			$this->error( 'invalid_confirm_word' );
		}

		// Get $vserver_settings
		require 'main/include/vars.vserver_settings.php';

		// Check for a valid parameter
		if ( ! isset( $vserver_settings[ $this->POST['key'] ] ) ) {
			$this->error( 'invalid_setting_parameter' );
		}

		$vservers = $this->PMA->meta->getAllServers();

		foreach( $vservers as $prx ) {

			//$this->PMA->meta->get_secret_ctx( $prx );

			$prx = new PMA_vserver( $prx );

			$prx->setConf( $this->POST['key'], $this->POST['value'] );
		}

		$this->success( 'parameters_updated_success' );
	}
}

?>

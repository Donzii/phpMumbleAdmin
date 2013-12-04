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

class PMA_cmd_pw_requests extends PMA_cmd {

	private $prx;

	private $profile;
	private $sid;
	private $login;
	private $uid;

	private $infos;

	private $temporary_started = FALSE;

	function process() {

		// Autoban attempt
		if ( $this->PMA->config->get( 'autoban_attempts' ) > 0 ) {
			PMA_autoban::instance()->attempts();
		}

		$this->sanity();
		$this->setup();

		// Start the virtual server if it's stopped.
		// WARN: Here, do not return to cmd/cmd.php before we stop the vserver at the end of this script.
		if ( ! $this->isRunning ) {
			$this->prx->start();
			$this->temporary_started = TRUE;
		}

		$this->uid = $this->get_user_id();

		$this->PMA->pw_requests = PMA_pw_requests::instance();
		$this->PMA->pw_requests->delete_identical( $this->profile['id'], $this->profile['host'], $this->profile['port'], $this->sid, $this->uid );

		$this->email = $this->get_user_email();

		$this->send_email();
	}

	protected function end() {

		// Stop the virtual server if it was temporary started.
		if ( $this->temporary_started ) {
			$this->prx->stop();
		}

		parent::end();
	}

	private function explicit_error( $key ) {

		if ( $this->PMA->config->get( 'pw_gen_explicit_msg' ) ) {

			$this->error( $key );

		} else {
			$this->error( 'gen_pw_error' );
		}
	}

	private function sanity() {

		if (
			! $this->PMA->config->get( 'pw_gen_active' )
			OR ! COOKIE_ACCEPTED
			OR ! pma_ice_conn_is_valid()
		) {
			$this->error( 'illegal_operation', 'nobutton' );
		}
	}

	private function setup() {

		$this->profile = $this->PMA->user->get_profile();
		$this->sid = (int) $this->POST['server_id'];

		$this->login = $this->POST['login'];

		// Common logs infos
		if ( $this->PMA->profiles->total() > 1 ) {
			$this->infos = ' ( profile: '.$this->profile['id'].'# '.$this->profile['name'].' - server id: '.$this->sid.' -  login: '.$this->login.' )';
		} else {
			$this->infos = ' ( server id: '.$this->sid.' -  login: '.$this->login.' )';
		}

		if ( NULL === $this->prx = $this->PMA->meta->getServer( $this->sid ) ) {

			$this->log( 'pwGen.error', 'Invalid server id '.$this->infos );
			$this->explicit_error( 'gen_pw_invalid_server_id' );
		}

		// Check if web access is enable
		if ( $this->prx->get_conf( 'PMA_permitConnection' ) !== 'TRUE' ) {
			write_log( 'pwGen.warn', 'Web access is disabled '.$this->infos );
			$this->error( 'web_access_disabled' );
		}

		$this->isRunning = $this->prx->isRunning();
	}

	private function get_user_id() {

		// Memo: getRegisteredUsers() return all occurence of a search.
		// example: for "ipnoz", it's return ipnozer, ipnozer2 etc...
		$search = $this->prx->getRegisteredUsers( $this->login );

		$user = strtolower( $this->login );

		foreach ( $search as $uid => $login ) {

			if ( strtolower( $login ) === $user ) {
				// User login exists, keep user ID.
				$mumble_id = $uid;
				break;
			}
		}

		// user not found
		if ( ! isset( $mumble_id ) ) {

			$this->log( 'pwGen.error', 'User not found '.$this->infos );
			$this->explicit_error( 'gen_pw_invalid_username' );

		// SuperUser
		} elseif ( $mumble_id === 0 ) {

			$this->log( 'pwGen.error', 'SuperUser is denied  '.$this->infos );
			$this->explicit_error( 'gen_pw_su_denied' );

		// User found and valid
		} elseif ( $mumble_id > 0 ) {

			return $mumble_id;

		// Unknown error
		} else {

			$this->debug( __class__ .'->'. __function__ .'() Unknown', 1, TRUE );
			$this->log( 'pwGen.error', 'Unknown error  '.$this->infos );
			$this->error( 'gen_pw_error' );
		}
	}

	private function get_user_email() {

		// Fetch user email
		$registration = $this->prx->getRegistration( $this->uid );

		if ( isset( $registration[1] ) ) {
			$email = $registration[1];
		} else {
			$email = '';
		}

		if ( $email === '' ) {
			$this->log( 'pwGen.warn', 'empty email '.$this->infos );
			$this->explicit_error( 'gen_pw_empty_email' );
		}

		return $email;
	}

	private function send_email() {

		$pending = $this->PMA->config->get( 'pw_gen_pending' );

		// Get smtp functions.
		require 'main/functions/pma_mail.php';

		global $TEXT;
		pma_load_language( 'common' );
		pma_load_language( 'auth' );

		// Construct the new pw request
		$new_request['id'] = $this->PMA->pw_requests->get_unique_id();
		$new_request['start'] = PMA_TIME;
		$new_request['end'] = PMA_TIME + $pending * 3600;
		$new_request['ip'] = PMA_USER_IP;
		$new_request['profile_id'] = $this->profile['id'];
		$new_request['profile_host'] = $this->profile['host'];
		$new_request['profile_port'] = $this->profile['port'];
		$new_request['sid'] = $this->sid;
		$new_request['uid'] = $this->uid;
		$new_request['login'] = $this->login;

		// Cronstruct the mail
		$URL = PMA_HTTP_HOST.PMA_HTTP_PATH.'?confirm_pw_request='.$new_request['id'];

		$from = get_sender_email( 'pw_gen_sender_email' );

		$to[] = array( 'type' => 'to', 'email' => $this->email, 'name' => $this->login );

		$subject = sprintf( $TEXT['pw_mail_title'], $_SERVER['HTTP_HOST'] );

		$message = '';
		// ice Profile
		if ( $this->PMA->profiles->total( 'publics' ) > 1 ) {
			$message .= sprintf( $TEXT['ice_profile_name'], $this->profile['name'] ).'<br>'.MEOL;
		}
		// server id / name
		$message .= sprintf( $TEXT['server_name'], $this->prx->get_conf( 'registername' ) ).'<br>'.MEOL;
		// Login
		$message .= $TEXT['login'].' : '.$this->login.'<br><br>'.MEOL.MEOL;
		// Error
		$message .= $TEXT['pw_mail_body_1'].'<br><br>'.MEOL.MEOL;
		// Confirm text
		$message .= $TEXT['pw_mail_body_2'].'<br><br>'.MEOL.MEOL;
		// url
		$message .= '<a href="'.$URL.'">'.$URL.'</a><br><br>'.MEOL.MEOL;
		// end - pending delay
		$message .= sprintf( $TEXT['pw_mail_body_3'], $pending );


		// SEND THE MAIL
		$pma_mail = pma_mail( $from, $to, $subject, '', $message );

		if ( $pma_mail ) {

			$this->success( 'gen_pw_mail_sent' );
			$this->log( 'pwGen.info', 'Mail sent '.$this->infos );

			$this->PMA->pw_requests->add( $new_request );

		} else {
			$this->error( 'gen_pw_error' );
		}
	}
}

?>

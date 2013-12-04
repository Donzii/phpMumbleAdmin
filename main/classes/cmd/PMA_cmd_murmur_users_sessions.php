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

class PMA_cmd_murmur_users_sessions extends PMA_cmd {

	private $prx;

	// User session id
	private $id;

	function process() {

		if ( ! $this->PMA->user->is_min( CLASS_SUPERUSER_RU ) ) {
			$this->illegal_operation();
		}

		if ( NULL === $this->prx = $this->PMA->meta->getServer( $_SESSION['page_vserver']['id'] ) ) {
			$this->end();
		}

		$this->id = $_SESSION['page_vserver']['uSess']['id'];

		if ( isset( $this->POST['kick'] ) ) {
			$this->kick( $this->POST['kick'] );

		} elseif ( isset( $this->GET['move_user_to'] ) ) {
			$this->move_user_to( $this->GET['move_user_to'] );

		} elseif ( isset( $this->POST['change_user_session_name'] ) ) {
			$this->change_session_name( $this->POST['change_user_session_name'] );

		} elseif ( isset( $this->GET['muteUser'] ) ) {
			$this->toggle_mute();

		} elseif ( isset( $this->GET['deafUser'] ) ) {
			$this->toggle_deaf();

		} elseif ( isset( $this->GET['togglePrioritySpeaker'] ) ) {
			$this->toggle_priority_speaker();

		} elseif ( isset( $this->POST['send_msg'] ) ) {
			$this->send_msg( $this->POST['send_msg'] );

		} elseif ( isset( $this->GET['register_session'] ) ) {
			$this->register_session();

		} elseif ( isset( $this->POST['change_user_comment'] ) ) {
			$this->change_comment( $this->POST['change_user_comment'] );
		}
	}

	private function kick( $message ) {
		$this->prx->kickUser( $this->id, $message );
		unset( $_SESSION['page_vserver']['uSess'] );
	}

	private function move_user_to( $id ) {

		if ( ! ctype_digit( $id ) ) {
			$this->error( 'invalid_numerical' );
		}

		$id = (int) $id;

		$USER = $this->prx->getState( $this->id );

		if ( $USER->channel !== $id ) {
			$USER->channel = $id;
			$this->prx->setState( $USER );
		}
	}

	private function change_session_name( $name ) {

		// Change session name come with murmur 1.2.4
		if ( $this->PMA->meta->int_version < 124 ) {
			$this->error( 'murmur_124_required' );
		}

		$USER = $this->prx->getState( $this->id );

		if ( $USER->name !== $name ) {
			$USER->name = $name;
			$this->prx->setState( $USER );
		}
	}

	private function toggle_mute() {

		$USER = $this->prx->getState( $this->id );

		if ( $USER->mute ) {
			$USER->mute = FALSE;
			$USER->deaf = FALSE;
		} else {
			$USER->mute = TRUE;
		}

		$this->prx->setState( $USER );
	}

	private function toggle_deaf() {

		$USER = $this->prx->getState( $this->id );

		$USER->deaf = ! $USER->deaf;

		$this->prx->setState( $USER );
	}

	private function toggle_priority_speaker() {

		$USER = $this->prx->getState( $this->id );

		// Priority speaker come with murmur 1.2.3
		if ( ! isset( $USER->prioritySpeaker ) OR $this->PMA->meta->int_version < 123 ) {
			$this->error( 'murmur_123_required' );
		}

		$USER->prioritySpeaker = ! $USER->prioritySpeaker;

		$this->prx->setState( $USER );
	}

	private function send_msg( $message ) {

		$this->redirection = 'referer';

		if ( $message === '' ) {
			$this->error( 'empty_message' );
		}

		if ( ! $this->PMA->user->is_min( CLASS_ROOTADMIN )  ) {
			$message = $this->prx->remove_html_tags( $message );
		}

		$this->prx->sendMessage( $this->id, url_to_HTML( $message ) );
	}

	private function register_session() {

		// getCertificateList method come with murmur 1.2.1
		if ( ! method_exists( 'Murmur_Server', 'getCertificateList' ) OR $this->PMA->meta->int_version < 121 ) {
			$this->error( 'murmur_121_required' );
		}

		$USER = $this->prx->getState( $this->id );

		$getCertificateList = $this->prx->getCertificateList( $this->id );

		$sha1 = sha1( array_dec_to_chars( $getCertificateList[0] ) );

		$newuser = array( 0 => $USER->name, 3 => $sha1 );

		$this->prx->registerUser( $newuser );

		$this->success( 'registration_created_success' );
	}

	private function change_comment( $comment ) {

		$USER = $this->prx->getState( $this->id );

		if ( $comment !== $USER->comment ) {

			// Memo: remove eol to avoid a bug when we modify user comment via js textarea.
			$USER->comment = replace_eol( $this->prx->remove_html_tags( $comment ) );
			$this->prx->setState( $USER );
		}
	}
}

?>
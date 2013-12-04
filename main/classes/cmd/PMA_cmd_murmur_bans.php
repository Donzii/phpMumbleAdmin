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

class PMA_cmd_murmur_bans extends PMA_cmd {

	private $prx;

	private $bans_list;

	private $address; // Ban IP in decimal
	private $mask;

	function process() {

		if ( ! $this->PMA->user->is_min( CLASS_SUPERUSER_RU ) ) {
			$this->illegal_operation();
		}

		if ( NULL === $this->prx = $this->PMA->meta->getServer( $_SESSION['page_vserver']['id'] ) ) {
			$this->end();
		}

		$this->bans_list = $this->prx->getBans();

		if ( isset( $this->POST['addBan'] ) ) {
			$this->add_ban();

		} elseif ( isset( $this->POST['edit_ban_id'] ) ) {
			$this->edit_ban( $this->POST['edit_ban_id'] );

		} elseif ( isset( $this->POST['delete_ban_id'] ) ) {
			$this->delete_ban( $this->POST['delete_ban_id'] );

		} elseif ( isset( $this->GET['remove_ban_hash'] ) ) {
			$this->remove_hash( $this->GET['remove_ban_hash'] );
		}
	}

	private function setBans() {
		$this->prx->setBans( $this->bans_list );
	}

	/**
	* IP and bitmask sanity
	*/
	private function ip_mask_sanity() {

		$ip = $this->POST['ip'];
		$this->mask = $this->POST['mask'];

		// IP
		if ( check_ipv4( $ip ) ) {

			$type = 'ipv4';
			$range = range( 1, 32 );

		} elseif ( check_ipv6( $ip ) ) {

			$type = 'ipv6';
			$range = range( 1, 128 );

		} else {
			$this->error( 'invalid_IP_address' );
		}

		// Add last range mask on empty field
		if ( $this->mask === '' ) {
			$this->mask = end( $range );
		}

		$this->mask = (int) $this->mask;

		if ( ! in_array( $this->mask, $range, TRUE ) ) {
			$this->error( 'invalid_bitmask' );
		}

		if ( $type === 'ipv4' ) {

			$this->address = ipv4_str_to_dec( $ip );
			$this->mask = ip_mask_4to6( $this->mask );

		} else {
			$this->address = ipv6_str_to_dec( $ip );
		}
	}

	private function add_ban() {

		$this->ip_mask_sanity();

		// Setup duration
		if (
			ctype_digit( $this->POST['hour'] )
			&& ctype_digit( $this->POST['day'] )
			&& ctype_digit( $this->POST['month'] )
			&& ctype_digit( $this->POST['year'] )
			&& ! isset( $this->POST['permanent'] )
		) {
			$hours = (int) $this->POST['hour'];
			$days = (int) $this->POST['day'];
			$months = (int) $this->POST['month'];
			$years = (int) $this->POST['year'];

			$duration = mktime( $hours, date( 'i', PMA_TIME ), date( 's', PMA_TIME ), $months, $days, $years ) - PMA_TIME;

		} else {
			$duration = 0;
		}

		$add = new Murmur_Ban();
		$add->address = $this->address;
		$add->bits = $this->mask;
		$add->name = $this->POST['name'];
		$add->hash = $this->POST['hash'];
		$add->reason = $this->POST['reason'];
		$add->start = PMA_TIME;
		$add->duration = $duration;

		$this->bans_list[] = $add;

		$this->setBans();

		if ( isset( $this->POST['kickhim'] ) ) {
			$this->prx->kickUser( $_SESSION['page_vserver']['uSess']['id'], $this->POST['reason'] );
			unset( $_SESSION['page_vserver']['uSess'] );
		}
	}

	private function edit_ban( $id ) {

		$this->ip_mask_sanity();

		// Invalid ban id
		if ( ! isset( $this->bans_list[ $id ] ) ) {
			$this->error( 'invalid_ban_id' );
		}

		$this->redirection = 'referer';

		// Workaround : upgrading murmur 1.2.2 to 1.2.3 modify all bans start to "-1"
		if ( $this->bans_list[ $id ]->start === -1 ) {
			$this->bans_list[ $id ]->start = PMA_TIME;
		}

		// Setup duration
		if (
			ctype_digit( $this->POST['hour'] )
			&& ctype_digit( $this->POST['day'] )
			&& ctype_digit( $this->POST['month'] )
			&& ctype_digit( $this->POST['year'] )
			&& ! isset( $this->POST['permanent'] )
		) {
			$start = $this->bans_list[ $id ]->start;

			$hours = (int) $this->POST['hour'];
			$days = (int) $this->POST['day'];
			$months = (int) $this->POST['month'];
			$years = (int) $this->POST['year'];

			$duration = mktime( $hours, date( 'i', $start ), date( 's', $start ), $months, $days, $years ) - $start;
		} else {
			$duration = 0;
		}

		// Memo: don't edit hash and start.
		$this->bans_list[ $id ]->address = $this->address;
		$this->bans_list[ $id ]->bits = $this->mask;
		$this->bans_list[ $id ]->name = $this->POST['name'];
		$this->bans_list[ $id ]->reason = $this->POST['reason'];
		// $this->bans_list[ $id ]->hash = 'DEBUG';
		$this->bans_list[ $id ]->duration = $duration;

		$this->setBans();
	}

	private function delete_ban( $id ) {

		if ( ! isset( $this->POST['confirmed'] ) ) {
			$this->end();
		}

		if ( ! isset( $this->bans_list[ $id ] ) ) {
			$this->error( 'invalid_ban_id' );
		}

		unset ( $this->bans_list[ $id ] );
		$this->setBans();
	}

	private function remove_hash( $id ) {

		if ( ! isset( $this->bans_list[ $id ] ) ) {
			$this->error( 'invalid_ban_id' );
		}

		$this->redirection = 'referer';

		$this->bans_list[ $id ]->hash = '';
		$this->setBans();
	}
}

?>

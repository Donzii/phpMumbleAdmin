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

class PMA_cmd_murmur_acl extends PMA_cmd {

	private $prx;
	private $chan_id;

	private $acl_list;
	private $group_list;
	private $acl_inherit;

	// ACL id
	private $id;

	private $total;
	private $last_acl_key;

	function process() {

		if ( ! $this->PMA->user->is_min( CLASS_SUPERUSER_RU ) ) {
			$this->illegal_operation();
		}

		if ( NULL === $this->prx = $this->PMA->meta->getServer( $_SESSION['page_vserver']['id'] ) ) {
			$this->end();
		}

		$this->chan_id = $_SESSION['page_vserver']['cid'];
		$this->prx->getACL( $this->chan_id, $this->acl_list, $this->group_list, $this->acl_inherit );

		$this->total = count( $this->acl_list );
		$this->last_acl_key = $this->total -1;

		PMA_helpers_ACL::remove_inherited( $this->acl_list );
		PMA_helpers_ACL::remove_inherited_groups( $this->group_list );

		// Fix a rare bug...
		reset( $this->acl_list );

		if ( isset( $this->GET['toggle_inherit_acl'] ) ) {
			$this->toggle_inherit_acl();

		} elseif ( isset( $this->GET['add_acl'] ) ) {
			$this->add_acl();

		} elseif ( isset( $this->POST['edit_acl'] ) ) {
			$this->edit_acl();

		} elseif ( isset( $this->GET['up_acl'] ) ) {
			$this->up_acl();

		} elseif ( isset( $this->GET['down_acl'] ) ) {
			$this->down_acl();

		} elseif ( isset( $this->GET['delete_acl'] ) ) {
			$this->delete_acl();
		}
	}

	private function setACL() {
		$this->prx->setACL( $this->chan_id, $this->acl_list, $this->group_list, $this->acl_inherit );
	}

	/**
	* Sanity for "edit / move up / move down / delete" which require a valid selected ACL.
	*/
	private function sanity() {

		if ( ! isset( $_SESSION['page_vserver']['aclID'] ) ) {
			$this->error( 'invalid_acl_id' );
		}

		$this->id = $_SESSION['page_vserver']['aclID'];

		if ( $this->id === -1 OR ! isset( $this->acl_list[ $this->id ] ) ) {
			$this->error( 'invalid_acl_id' );
		}

		$ACL = $this->acl_list[ $this->id ];

		// Deny SuperUser_ru to edit the ACL which give him SuperUser_ru right.
		if (
			$this->chan_id === 0
			&& $this->PMA->user->is( CLASS_SUPERUSER_RU )
			&& $ACL->userid === $this->PMA->user->mumble_id
			&& PMA_helpers_ACL::is_superuser_ru( $ACL )
		) {
			$this->illegal_operation();
		}

		return $ACL;
	}

	private function toggle_inherit_acl() {

		$this->acl_inherit = ! $this->acl_inherit;

		if ( ! $this->acl_inherit ) {

			// Check if we have selected an inherited ACL
			if ( isset( $_SESSION['page_vserver']['aclID'] ) && ! isset( $this->acl_list[ $_SESSION['page_vserver']['aclID'] ] ) ) {

				if ( $_SESSION['page_vserver']['aclID'] !== -1 ) {
					unset( $_SESSION['page_vserver']['aclID'] );
				}
			}
		}

		$this->setACL();
	}

	private function add_acl() {

		$new = new Murmur_ACL();
		$new->group = 'all';
		$new->userid = -1;
		$new->applyHere = TRUE;
		$new->applySubs = TRUE;
		$new->inherited = FALSE;
		$new->allow = 0;
		$new->deny = 0;

		$this->acl_list[] = $new;

		$this->setACL();

		// Select the new acl
		$_SESSION['page_vserver']['aclID'] = $this->total;
	}

	private function edit_acl() {

		$ACL = $this->sanity();

		// Change group
		if ( $this->POST['group'] !== '' && $this->POST['user'] === '' ) {
			$ACL->group = $this->POST['group'];
			$ACL->userid = -1;
		}

		// Change user
		if ( ctype_digit( $this->POST['user'] ) ) {
			$ACL->userid =  (int) $this->POST['user'];
			$ACL->group = NULL;
		}

		$ACL->applyHere = isset( $this->POST['applyHere'] );
		$ACL->applySubs = isset( $this->POST['applySubs'] );

		// Remove ACLs with both allow & deny key.
		if ( isset( $this->POST['ALLOW'] ) && isset( $this->POST['DENY'] ) ) {

			foreach ( $this->POST['ALLOW'] as $key => $value ) {

				if ( isset( $this->POST['DENY'][ $key ] ) ) {
					unset( $this->POST['ALLOW'][ $key ], $this->POST['DENY'][ $key ] );
				}
			}
		}

		if ( isset( $this->POST['ALLOW'] ) ) {
			$ACL->allow = bitmask_count( $this->POST['ALLOW'] );
		} else {
			$ACL->allow = 0;
		}

		if ( isset( $this->POST['DENY'] ) ) {
			$ACL->deny = bitmask_count( $this->POST['DENY'] );
		} else {
			$ACL->deny = 0;
		}

		$this->acl_list[ $this->id ] = $ACL;

		$this->setACL();
	}

	private function up_acl() {

		$ACL = $this->sanity();

		// Move up only if it's not the first ACL
		if ( $this->id === key( $this->acl_list ) ) {
			$this->end();
		}

		// Inverse position
		$up = $this->id -1;
		$down = $this->id;

		$tmp[ $up ] = $this->acl_list[ $up ];
		$tmp[ $down ] = $this->acl_list[ $down ];

		$this->acl_list[ $up ] = $tmp[ $down ];
		$this->acl_list[ $down ] = $tmp[ $up ];

		$this->setACL();

		$_SESSION['page_vserver']['aclID'] = $up;
	}

	private function down_acl() {

		$ACL = $this->sanity();

		// Move down only if it's not the last ACL
		if ( $this->id === $this->last_acl_key ) {
			$this->end();
		}

		// Inverse position
		$up = $this->id;
		$down = $this->id +1;

		$tmp[ $up ] = $this->acl_list[ $up ];
		$tmp[ $down ] = $this->acl_list[ $down ];

		$this->acl_list[ $up ] = $tmp[ $down ];
		$this->acl_list[ $down ] = $tmp[ $up ];

		$this->setACL();

		$_SESSION['page_vserver']['aclID'] = $down;
	}

	private function delete_acl() {

		$ACL = $this->sanity();

		unset( $this->acl_list[ $_SESSION['page_vserver']['aclID'] ] );

		$this->setACL();

		// Stay on the last ACL if we deleted the last one.
		if ( $_SESSION['page_vserver']['aclID'] === $this->last_acl_key ) {
			$_SESSION['page_vserver']['aclID'] = $this->last_acl_key -1;
		}
	}
}



?>

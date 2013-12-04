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

class PMA_cmd_murmur_groups extends PMA_cmd {

	private $prx;
	private $chan_id;

	private $acl_list;
	private $group_list;
	private $acl_inherit;

	// Group id
	private $gid;

	function process() {

		if ( ! $this->PMA->user->is_min( CLASS_SUPERUSER_RU ) ) {
			$this->illegal_operation();
		}

		if ( NULL === $this->prx = $this->PMA->meta->getServer( $_SESSION['page_vserver']['id'] ) ) {
			$this->end();
		}

		$this->chan_id = $_SESSION['page_vserver']['cid'];

		$this->prx->getACL( $this->chan_id, $this->acl_list, $this->group_list, $this->acl_inherit );

		PMA_helpers_ACL::remove_inherited( $this->acl_list );

		if ( isset( $this->POST['add_group'] ) ) {
			$this->add_group( $this->POST['add_group'] );

		} elseif ( isset( $this->GET['deleteGroup'] ) ) {
			$this->delete_group();

		} elseif ( isset( $this->GET['toggle_group_inherit'] ) ) {
			$this->toggle_group_inherit();

		} elseif ( isset( $this->GET['toggle_group_inheritable'] ) ) {
			$this->toggle_group_inheritable();

		} elseif ( isset( $this->POST['add_user'] ) ) {
			$this->add_user( $this->POST['add_user'] );

		} elseif ( isset( $this->GET['removeMember'] ) ) {
			$this->remove_member( $this->GET['removeMember'] );

		} elseif ( isset( $this->GET['excludeMember'] ) ) {
			$this->exclude_member( $this->GET['excludeMember'] );

		} elseif ( isset( $this->GET['removeExcluded'] ) ) {
			$this->remove_excluded( $this->GET['removeExcluded'] );
		}
	}

	private function setACL() {
		$this->prx->setACL( $this->chan_id, $this->acl_list, $this->group_list, $this->acl_inherit );
	}

	/**
	* Sanity for a valid group id
	* Memo: add_group do not require a valid group id.
	*/
	private function sanity() {

		if ( ! isset( $_SESSION['page_vserver']['groupID'] ) ) {
			$this->error( 'invalid_group_id' );
		}

		$this->gid = $_SESSION['page_vserver']['groupID'];

		if ( ! isset( $this->group_list[ $this->gid ] ) ) {
			$this->error( 'invalid_group_id' );
		}

		PMA_helpers_ACL::remove_inherited_groups( $this->group_list, $this->gid );
	}

	private function add_group( $name ) {

		if ( $name === '' ) {
			$this->error( 'empty_name' );
		}

		// mumble add group name in lower case, so do it.
		$name = strToLower( $name );

		$add = new Murmur_Group();
		$add->name = $name;
		$add->inherited = FALSE;
		$add->inherit = TRUE;
		$add->inheritable = TRUE;
		$add->add = array();
		$add->members = array();
		$add->remove = array();

		$this->group_list[] = $add;

		// Memo: unset inherited group after we added the new group to avoid a bug.
		PMA_helpers_ACL::remove_inherited_groups( $this->group_list );
		$this->setACL();

		// Murmur will reindex keys of groups after setACL()
		// So get tje group list a second time to find the new group and select it.
		$this->prx->getACL( $this->chan_id, $this->acl_list, $this->group_list, $this->acl_inherit );

		foreach ( $this->group_list as $key => $group ) {

			if ( $group->name === $name ) {

				$_SESSION['page_vserver']['groupID'] = $key;
				break;
			}
		}
	}

	private function delete_group() {

		$this->sanity();

		$keepname = $this->group_list[ $this->gid ]->name;

		unset( $this->group_list[ $this->gid ], $_SESSION['page_vserver']['groupID'] );
		$this->setACL();

		// If we reset an inherited group, re-select it.
		$this->prx->getACL( $this->chan_id, $this->acl_list, $this->group_list, $this->acl_inherit );

		foreach( $this->group_list as $key => $obj ) {

			if ( $obj->name === $keepname ) {
				$_SESSION['page_vserver']['groupID'] = $key;
				break;
			}
		}
	}

	private function toggle_group_inherit() {

		$this->sanity();

		$this->group_list[ $this->gid ]->inherit = ! $this->group_list[ $this->gid ]->inherit;
		$this->setACL();
	}

	private function toggle_group_inheritable() {

		$this->sanity();

		$this->group_list[ $this->gid ]->inheritable = ! $this->group_list[ $this->gid ]->inheritable;
		$this->setACL();
	}

	private function add_user( $id ) {

		if ( ! ctype_digit( $id ) ) {
			$this->error( 'invalid_numerical' );
		}

		$this->sanity();

		$this->group_list[ $this->gid ]->add[] = (int) $id;
		$this->setACL();
	}

	private function remove_member( $id ) {

		if ( ! ctype_digit( $id ) ) {
			$this->error( 'invalid_numerical' );
		}

		$id = (int) $id;

		$this->sanity();

		foreach ( $this->group_list[ $this->gid ]->add as $key => $uid ) {

			if ( $uid === $id ) {
				unset( $this->group_list[ $this->gid ]->add[ $key ] );
				// Memo: continue loop to end
			}
		}

		$this->setACL();
	}

	private function exclude_member( $id ) {

		if ( ! ctype_digit( $id ) ) {
			$this->error( 'invalid_numerical' );
		}

		$id = (int) $id;

		$this->sanity();

		// Dont exclude "non-inherited" members.
		if ( in_array( $id, $this->group_list[ $this->gid ]->add, TRUE ) ) {
			$this->error( 'non_inherited_member' );
		}

		// Check for a valid inherited uid
		foreach ( $this->group_list[ $this->gid ]->members as $key => $uid ) {

			if ( $uid === $id ) {
				$this->group_list[ $this->gid ]->remove[] = $id;
				$this->setACL();
				break;
			}
		}
	}

	private function remove_excluded( $id ) {

		if ( ! ctype_digit( $id ) ) {
			$this->error( 'invalid_numerical' );
		}

		$id = (int) $id;

		$this->sanity();

		foreach ( $this->group_list[ $this->gid ]->remove as $key => $uid ) {

			if ( $uid === $id ) {
				unset( $this->group_list[ $this->gid ]->remove[ $key ] );
				// Memo: continue loop to end
			}
		}

		$this->setACL();
	}
}

?>

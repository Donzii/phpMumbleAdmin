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

class PMA_helpers_ACL {

	/**
	* Check if a Murmur ACL rule match for SuperUser_ru rights
	*
	* @return Bool
	*/
	static function is_superuser_ru( $acl ) {

		if ( $acl->userid > 0 ) {

			$allow = bitmask_decompose( $acl->allow );

			if (
				in_array( Murmur_PermissionWrite, $allow, TRUE )
				&& $acl->applyHere
				&& $acl->applySubs
			) {
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	* Check if a Murmur ACL rule is a token
	*
	* @return Bool
	*/
	static function is_token( $acl ) {
		return ( $acl->userid === -1 && substr( $acl->group, 0, 1 ) === '#' );
	}

	/**
	* Check if a Murmur ACL rule is a "deny all" from a token
	*
	* @return Bool
	*/
	static function is_deny_all_token( $acl ) {

		return (
			$acl->group === 'all'
			&& $acl->applyHere
			&& $acl->applySubs
			&& $acl->deny === 908
		);
	}

	/**
	* Remove inherited ACLs
	* This function permit to not add inherited ACLs as new ACL with Murmur_server::setACL() method.
	*/
	static function remove_inherited( &$aclList ) {

		foreach ( $aclList as $key => $obj ) {

			if ( $obj->inherited ) {
				unset( $aclList[ $key ] );
			}
		}
	}

	/**
	* Remove inherited groups
	* This permit to avoid to remove the inherited flag with setACL().
	*
	* @param $keep_key - do not  remove the current group for modification.
	*/
	static function remove_inherited_groups( &$groupList, $keep_key = NULL ) {

		foreach ( $groupList as $key => $obj ) {

			if ( $keep_key !== NULL && $keep_key == $key ) {
				continue;
			}

			if ( $obj->inherited ) {
				unset( $groupList[ $key ] );
			}
		}
	}
}

?>
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

class PMA_vserver extends PMA_abs_ice_prx {

	private $prx;

	// Server id
	private $sid;

	// Custom conf
	private $custom;

	function __construct( $prx ) {

		$this->prx = $prx;

		$this->sid = self::prx_to_sid( $this->prx );

		$this->stats_enabled = PMA_config::instance()->get( 'debug_stats' );

		PMA_meta::instance()->get_secret_ctx( $this->prx );

		msg_debug( '<span class="b fushia">New</span> vserver ( id: '.$this->sid.' )', 3 );
	}

	/**
	* Return the vserver id from ice proxy object.
	* Useful to avoid too much ICE queries with $prx->id(); and save web server ressources.
	*
	* @return int - or NULL on invalid $prx
	*/
	static function prx_to_sid( $prx ) {

		// $prx return "s/1 -t:tcp -h 127.0.0.1 -p 6502"

		list( $sid ) = explode( ' ', $prx );

		if ( substr( $sid, 0, 2 ) === 's/' ) {
			return (int) substr( $sid, 2 );
		}
	}

	function sid() {
		return $this->sid;
	}

	/**
	* Kick all users of the vserver.
	*
	* @return string
	*/
	function kick_all_users( $msg = '' ) {

		$msg = url_to_HTML( $msg );

		foreach( $this->getUsers() as $user ) {
			$this->kickUser( $user->session, $msg );
		}
	}

	/**
	* Construct the vserver connection url.
	*
	* @return string
	*/
	function url() {

		$meta = PMA_meta::instance();

		if ( NULL === $profile = PMA_user::instance()->get_profile() ) {
			return;
		}

		$cookie = PMA_cookie::instance();

		// Server IP.
		if ( $profile['http-addr'] !== '' ) {
			$host = $profile['http-addr'];
		} else {
			$host = $this->get_conf( 'host' );
		}

		// a http IPv6 addr have to be like [::1]
		if ( check_ipv6( $host ) ) {
			$host = '['.$host.']';
		}

		$port = $this->get_conf( 'port' );

		// login name
		if ( $cookie->get( 'vserver_login' ) !== '' ) {

			$login = $cookie->get( 'vserver_login' );

		} elseif ( $_SESSION['auth']['login'] !== '' ) {

			$login = $_SESSION['auth']['login'];

		} else {
			$login = 'Guest_'.gen_random_chars( 5 );
		}

		// Murmur version
		if ( PMA_config::instance()->get( 'murmur_version_url' ) ) {
			$version = $meta->str_version;
		} else {
			$version = '1.2.0';
		}

		// Server password
		$password = $this->get_conf( 'password' );

		// login:pass
		if ( $password !== '' ) {
			$login .= ':'.$password;
		}

		return 'mumble://'.$login.'@'.$host.':'.$port.'/?version='.$version;
	}

	function validate_chars( $key, $str ) {

		// Get patern for channelName or userName.
		$patern = $this->get_conf( $key );

		// "\w" with preg_match do not work as intended ( any localized character )
		// Workaround: replace \w by \pL\pN ( L = letter, N = Number ).
		// See http://www.pcre.org/pcre.txt
		// See http://www.php.net/manual/en/book.pcre.php
		$patern = str_replace( array( '\\\w', '\\w', '\w' ), '\pL\pN', $patern );

		if ( preg_match( '/^'.$patern.'$/u', $str ) === 1 ) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	* Check if the vserver accept HTML tag or remove them.
	*
	* @param string $str - the string value to check
	* @param bool $alert - If the vserver dont allow HTML tags, alert  the current user if TRUE.
	*
	* @return string - modified ( or not ) $str
	*/
	function remove_html_tags( $str, $alert = TRUE ) {

		$allow = $this->get_conf( 'allowhtml' );

		if ( $allow !== 'false' ) {
			return $str;
		}

		$strip_tags = strip_tags( $str );

		if ( $strip_tags !== $str && $alert === TRUE ) {
			msg_box( 'vserver_dont_allow_HTML', 'error' );
		}

		return $strip_tags;
	}

	/**
	*
	* Check if a registered user have SuperUser_ru rights
	*
	* @return Bool
	*/
	function is_superuser_ru( $uid ) {

		$config = PMA_config::instance();

		if ( $uid > 0 && $config->get( 'SU_ru_active' ) && $config->get( 'SU_auth' ) ) {

			// Get Root channel ACL list
			$this->getACL( 0, $aclList, $groupList, $inherit );

			foreach ( $aclList as $obj ) {

				// Registered user have an ACL owned by it's uid.
				if ( $obj->userid === $uid ) {

					// Memo: continue on false, maybe user have more than one ACL.
					if ( PMA_helpers_ACL::is_superuser_ru( $obj ) ) {
						return TRUE;
					}
				}
			}
		}

		return FALSE;
	}

	/**
	* Return the specific conf for a vserver ( custom or default ).
	*
	* @return string ( or NULL )
	*/
	function get_conf( $key ) {

		$defaultConf = PMA_meta::instance()->getDefaultConf();

		if ( ! is_array( $this->custom ) ) {
			$this->custom = $this->getAllConf();
		}

		if ( isset( $this->custom[ $key ] ) ) {

			return $this->custom[ $key ];

		} else {

			// Murmur default port particularity
			if ( $key === 'port' ) {
				return $defaultConf['port'] + $this->sid - 1;
			}

			// Memo: Murmur do not return some default parameters.
			if ( isset( $defaultConf[ $key ] ) ) {
				return $defaultConf[ $key ];
			}
		}
	}

	/**
	* **********************************************
	* Murmur_Server methods with queries_stats.
	* **********************************************
	*/
	function addChannel( $name, $parent ) {

		$this->stats_start( __function__ );
		$id = $this->prx->addChannel( $name, $parent );
		$this->stats_stop();

		return $id;
	}

	function delete() {

		$this->stats_start( __function__ );
		$this->prx->delete();
		$this->stats_stop();
	}

	function getACL( $id, &$aclList, &$aclGroup, &$inherit ) {

		$this->stats_start( __function__ );
		$this->prx->getACL( $id, $aclList, $aclGroup, $inherit );
		$this->stats_stop();
	}

	function getAllConf() {

		$this->stats_start( __function__ );
		$array = $this->prx->getAllConf();
		$this->stats_stop();

		return $array;
	}

	function getBans() {

		$this->stats_start( __function__ );
		$array = $this->prx->getBans();
		$this->stats_stop();

		return $array;
	}

	function getCertificateList( $uid ) {

		$this->stats_start( __function__ );
		$array = $this->prx->getCertificateList( $uid );
		$this->stats_stop();

		return $array;
	}

	function getChannelState( $id ) {

		$this->stats_start( __function__ );
		$obj = $this->prx->getChannelState( $id );
		$this->stats_stop();

		return $obj;
	}

	function getChannels() {

		$this->stats_start( __function__ );
		$array = $this->prx->getChannels();
		$this->stats_stop();

		return $array;
	}

	function getConf( $key ) {

		$this->stats_start( __function__ );
		$str = $this->prx->getConf( $key );
		$this->stats_stop();

		return $str;
	}

	function getLog( $first, $last ) {

		$this->stats_start( __function__ );
		$array = $this->prx->getLog( $first, $last );
		$this->stats_stop();

		return $array;
	}

	function getLogLen() {

		$this->stats_start( __function__ );
		$len = $this->prx->getLogLen();
		$this->stats_stop();

		return $len;
	}

	function getRegisteredUsers( $filter ) {

		$this->stats_start( __function__ );
		$array = $this->prx->getRegisteredUsers( $filter );
		$this->stats_stop();

		return $array;
	}

	function getRegistration( $id ) {

		$this->stats_start( __function__ );
		$array = $this->prx->getRegistration( $id );
		$this->stats_stop();

		return $array;
	}

	function getState( $uid ) {

		$this->stats_start( __function__ );
		$obj = $this->prx->getState( $uid );
		$this->stats_stop();

		return $obj;
	}

	function getTexture( $uid ) {

		$this->stats_start( __function__ );
		$array = $this->prx->getTexture( $uid );
		$this->stats_stop();

		return $array;
	}

	function getTree() {

		$this->stats_start( __function__ );
		$obj = $this->prx->getTree();
		$this->stats_stop();

		return $obj;
	}

	function getUptime() {

		$this->stats_start( __function__ );
		$int = $this->prx->getUptime();
		$this->stats_stop();

		return $int;
	}

	function getUsers() {

		$this->stats_start( __function__ );
		$array = $this->prx->getUsers();
		$this->stats_stop();

		return $array;
	}

	function hasPermission( $session, $channelid, $perm ) {

		$this->stats_start( __function__ );
		$bool = $this->prx->hasPermission( $session, $channelid, $perm );
		$this->stats_stop();

		return $bool;
	}

	function id() {

		$this->stats_start( __function__ );
		$id = $this->prx->id();
		$this->stats_stop();

		return $id;
	}

	function isRunning() {

		$this->stats_start( __function__ );
		$bool = $this->prx->isRunning();
		$this->stats_stop();

		return $bool;
	}

	function kickUser( $uid, $reason ) {

		$this->stats_start( __function__ );
		$this->prx->kickUser( $uid, $reason );
		$this->stats_stop();
	}

	function registerUser( $array ) {

		$this->stats_start( __function__ );
		$uid = $this->prx->registerUser( $array );
		$this->stats_stop();

		return $uid;
	}

	function removeChannel( $id ) {

		$this->stats_start( __function__ );
		$this->prx->removeChannel( $id );
		$this->stats_stop();
	}

	function sendMessage( $uid, $text ) {

		$this->stats_start( __function__ );
		$this->prx->sendMessage( $uid, $text );
		$this->stats_stop();
	}

	function sendMessageChannel( $uid, $sub, $text ) {

		$this->stats_start( __function__ );
		$this->prx->sendMessageChannel( $uid, $sub, $text );
		$this->stats_stop();
	}

	function setACL( $id, $aclList, $aclGroup, $inherit ) {

		$this->stats_start( __function__ );
		$this->prx->setACL( $id, $aclList, $aclGroup, $inherit );
		$this->stats_stop();
	}

	function setBans( $array ) {

		$this->stats_start( __function__ );
		$this->prx->setBans( $array );
		$this->stats_stop();
	}

	function setChannelState( $chan ) {

		$this->stats_start( __function__ );
		$this->prx->setChannelState( $chan );
		$this->stats_stop();
	}

	function setConf( $key, $value ) {

		$this->stats_start( __function__ );
		$this->prx->setConf( $key, $value );
		$this->stats_stop();
	}

	function setState( $state ) {

		$this->stats_start( __function__ );
		$this->prx->setState( $state );
		$this->stats_stop();
	}

	function setSuperuserPassword( $str ) {

		$this->stats_start( __function__ );
		$this->prx->setSuperuserPassword( $str );
		$this->stats_stop();
	}

	function setTexture( $uid, $texture ) {

		$this->stats_start( __function__ );
		$this->prx->setTexture( $uid, $texture );
		$this->stats_stop();
	}

	function start() {

		$this->stats_start( __function__ );
		$this->prx->start();
		$this->stats_stop();
	}

	function stop() {

		$this->stats_start( __function__ );
		$this->prx->stop();
		$this->stats_stop();
	}

	function unregisterUser( $uid ) {

		$this->stats_start( __function__ );
		$this->prx->unregisterUser( $uid );
		$this->stats_stop();
	}

	function updateRegistration( $uid, $array ) {

		$this->stats_start( __function__ );
		$this->prx->updateRegistration( $uid, $array );
		$this->stats_stop();
	}

	function verifyPassword( $name, $pw ) {

		$this->stats_start( __function__ );
		$result = $this->prx->verifyPassword( $name, $pw );
		$this->stats_stop();

		return $result;
	}

/**
*
* Murmur_Server methods I didnt declared here
*
* addCallback
* addContextCallback
* addUserToGroup
* effectivePermissions
* getUserIds
* getUserNames
* redirectWhisperGroup
* removeCallback
* removeContextCallback
* removeUserFromGroup
* setAuthenticator
*
*/

}

?>
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

/**
* Miscellaneous functions relative to PMA
*/

/**
* Autoload PMA classes
*/
function __autoload( $class ) {

	if ( $class === 'HTML' ) {
		require 'main/classes/output/'.$class.'.php';
		return;
	}

	if ( substr( $class, 0, 3 ) !== 'PMA' ) {
		return;
	}

	if ( substr( $class, 0, 7 ) === 'PMA_cmd' ) {

		require 'main/classes/cmd/'.$class.'.php';

	} elseif ( substr( $class, 0, 11 ) === 'PMA_helpers' ) {

		require 'main/classes/helpers/'.$class.'.php';

	} elseif ( substr( $class, 0, 10 ) === 'PMA_output' ) {

		require 'main/classes/output/'.$class.'.php';

	} elseif ( substr( $class, 0, 13 ) === 'PMA_controler' ) {

		require 'main/classes/controler/'.$class.'.php';

	} else {

		require 'main/classes/'.$class.'.php';
	}

	msg_debug( '<b style="color: blue">'.$class.'</b> autoloaded', 3 );
}

/**
* Load requested PMA file language
*/
function pma_load_language( $file ) {

	global $TEXT;

	$lang = PMA_cookie::instance()->get( 'lang' );

	// Load english file, this language should be up to date ( even if my english is bad. Fixed translation are welcome :o ).
	require_once PMA_DIR_LANGUAGES.'en_EN/'.$file.'.loc.php';

	// Load translated file if it's not english.
	if ( $lang !== 'en_EN' ) {
		@include_once PMA_DIR_LANGUAGES.$lang.'/'.$file.'.loc.php';
	}
}

function pma_illegal_operation() {

	// It can appends when an user has been auto-logout.
	// Do not send an ugly "illegal operation" message.
	if ( PMA_user::instance()->is( CLASS_UNAUTH ) ) {

		msg_box( 'please_authenticate', 'error' );

	} else {
		msg_box( 'illegal_operation', 'error', 'nobutton' );
	}

	pma_redirect();
}

/**
* Redirect current user to main.
*/
function pma_redirect( $redirection = NULL ) {

	if ( $redirection === NULL ) {
		$redirection = './';
	}

	// Update modified datas in database before redirection.
	PMA_db::instance()->save_all_datas();

	msg_debug( '<span class="b"> >> redirection << </span>', 2 );

	// Save all messages in session before redirection.
	PMA_session::instance()->cache_messages();

	header( 'location:'.$redirection );

	pma_fatal_error( __function__. '(): Redirection didn\'t worked.' );
}

/**
* Logout current user.
*/
function pma_logout() {
	$_SESSION = array();
}

/**
* Parse a string to return all options in an array
* Separate options with a comma ","
* Options can be just a key, or with a value if equal "=" symbol is found.
*
* example: $str = 'an_option , another_option=with_value'
*
* @return array
*/
function pma_parse_options( $str ) {

	$options = array();

	if ( $str === NULL OR $str === '' ) {
		return $options;
	}

	$opts = explode( ',', $str );

	foreach( $opts as $opt ) {

		$opt = trim( $opt );

		if ( $opt === '' ) {
			continue;
		}

		$value = '';

		if ( in_istring( $opt, '=' ) ) {

			list( $opt, $value ) = explode( '=', $opt, 2 );

			$opt = trim( $opt );

			$value = trim( $value );
		}

		$options[ $opt ] = $value;
	}

	return $options;
}

/**
* Ice_Exception have multiples sources of messages ( almost empty btw ).
* This function try to find the message string.
* @return array - class & text of an exception.
*/
function pma_get_exception( $Ex ) {

	$exception['class'] = get_class( $Ex );
	$exception['text'] = '';

	// Ice_unkown exception
	if ( isset( $Ex->unknown ) ) {
		$exception['text'] = $Ex->unknown;

	// Ice_MarshalException exception
	} elseif ( isset( $Ex->reason ) ) {
		$exception['text'] = $Ex->reason;

	} else {
		$exception['text'] = $Ex->getMessage();
	}

	// Ice_exceptions can return an EOL and break PMA logs
	$exception['text'] = replace_eol( $exception['text'] );

	return $exception;
}

/**
* Common murmur exceptions messages
* This function must be called only with murmur methods.
*/
function pma_murmur_exception( $Ex ) {

	$array = pma_get_exception( $Ex );

	$message =  'Exception => '.$array['class'] .' : '.$array['text'];

	msg_debug( $message, 1, TRUE );

	// Assume that all others exceptions are an invalid slice definitions file.
	// Disable the current Ice profile.
	if ( ! in_istring( $array['class'], 'Murmur_' ) ) {

			$pid = PMA_cookie::instance()->get( 'profile_id' );

			PMA_profiles::instance()->set_as_invalid_slice_file( $pid, $message );
			pma_redirect();
	}

	switch( $array['class'] ) {

		case 'Murmur_InvalidChannelException':
			msg_box( 'InvalidChannelException', 'error' );
			unset( $_SESSION['page_vserver']['cid'], $_SESSION['page_vserver']['cTab'], $_SESSION['page_vserver']['aclID'], $_SESSION['page_vserver']['groupID'] );
			break;

		case 'Murmur_InvalidSecretException':
			// This one will be catched only on write action as PMA check "read secret" on Ice initialization
			// Most probably an Invalid icesecretwrite password
			// Memo: icesecretwrite come with murmur 1.2.3
			msg_box( 'invalid_secret_write', 'error' );
			break;

		case 'Murmur_InvalidServerException':
			msg_box( 'vserver_dont_exists', 'error' );
			break;

		case 'Murmur_InvalidSessionException':
			msg_box( 'InvalidSessionException', 'error' );
			unset( $_SESSION['page_vserver']['uSess'] );
			break;

		case 'Murmur_InvalidUserException':

			$trace = $Ex->getTraceAsString();

			// Error on user registration or updateregistration. Most probably user name allready in database.
			if ( in_istring( $trace, 'registeruser' ) OR in_istring( $trace, 'updateregistration' ) ) {

				if ( PMA_CMD_MODE === 'murmur_users_sessions' ) {

					msg_box( 'user_already_registered', 'error' );

				} else {
					msg_box( 'username_exists', 'error' );
				}

			} else {
				// Probably registration do not exists
				msg_box( 'InvalidUserException', 'error' );
				unset( $_SESSION['page_vserver']['registration_id'] );
			}
			break;

		case 'Murmur_NestingLimitException';
			msg_box( 'NestingLimitException', 'error' );
			break;

		case 'Murmur_ServerBootedException':
			msg_box( 'ServerBootedException', 'error' );
			break;

		case 'Murmur_ServerFailureException':
			msg_box( 'ServerFailureException', 'error' );
			break;

		case 'Murmur_MurmurException';
			msg_box( 'unknown_murmur_exception', 'error' );
			break;

		default:
			/**
			* Unused exceptions:
			*
			* 'Murmur_InvalidCallbackException':
			* 'Murmur_InvalidTextureException':
			*/
			msg_box( $array['class'], 'error' );
			break;
	}
}

/**
* Get Ice connection state, anytime, everywhere, in any circumstances...
* No need to include meta if it's not required...
*
* @return bool
*/
function pma_ice_conn_is_valid() {
	return defined( 'PMA_ICE_CONN_IS_VALID' );
}

function pma_fatal_error( $message ) {

	// Flush output cache if enabled.
	@ob_end_clean();

	// Update modified datas in database before die.
	PMA_db::instance()->save_all_datas();

	die( '<span style="color: red;"><b>'.PMA_NAME.' fatal error</b></span> : '.$message );
}

/**
* Convert class constant by class name string
*
* @param $class integer
*
* @return string class name
*/
function pma_class_name( $class ) {

	switch( $class ) {

		case CLASS_SUPERADMIN:
			return 'SuperAdmin';

		case CLASS_ROOTADMIN:
			return 'RootAdmin';

		case CLASS_HEADADMIN:
			return 'HeadAdmin';

		case CLASS_ADMIN_FULL_ACCESS:
		case CLASS_ADMIN:
			return 'Admin';

		case CLASS_SUPERUSER:
			return 'SuperUser';

		case CLASS_SUPERUSER_RU:
			return 'SuperUser_ru';

		case CLASS_USER:
			return 'User';

		case CLASS_UNAUTH:
			return 'unauth';
	}
}

?>
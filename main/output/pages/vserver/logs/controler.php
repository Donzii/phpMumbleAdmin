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

pma_load_language( 'logs' );

$LOGS = new PMA_output_logs( $PMA->cookie->get( 'date' ) );

// Get $vlogs['filters']
require 'main/include/vars.logs_filters.php';

$vlogs['total_filtered'] = 0;
$vlogs['total_search_found'] = 0;

/**
* VLOGS REPLACE & HIGHLIGHT
*/
if ( $vlogs['allow_highlight'] ) {

	// filter = string patern.
	// regExp = Regular expression patern.
	// replace = String to replace.
	$vlogs['replace'][] =  array( 'filter' => 'Connection closed: The remote host closed the connection [1]', 'replace' => 'has left the server' );
	$vlogs['replace'][] =  array( 'regExp' => '/^Stopped$/', 'replace' => 'Server stopped' );

	// filter = string only patern
	// css = CSS class
	// dontBreak = TRUE : If a rule match, the script will move to the next log. This permit to continue and apply multiple filters on the text
	// allText = TRUE : The CSS englobe all the text. dontBreak become useless.
	// Memo: dontBreak rule have to be on top
	$vlogs['highlights'][] =  array( 'filter' => 'Ignoring connection:', 'css' => 'Linfo', 'dontBreak' => TRUE );
	$vlogs['highlights'][] =  array( 'filter' => 'Authenticated', 'css' => 'Lauth' );
	$vlogs['highlights'][] =  array( 'filter' => 'New connection:', 'css' => 'Lauth' );
	$vlogs['highlights'][] =  array( 'filter' => 'has left the server', 'css' => 'Lclosed_conn' );
	$vlogs['highlights'][] =  array( 'filter' => 'Connection closed', 'css' => 'Linfo' );
	$vlogs['highlights'][] =  array( 'filter' => 'Moved to channel', 'css' => 'Linfo' );
	$vlogs['highlights'][] =  array( 'filter' => 'Moved user', 'css' => 'Ladmin' );
	$vlogs['highlights'][] =  array( 'filter' => 'not allowed to', 'css' => 'Lwarn' );
	$vlogs['highlights'][] =  array( 'filter' => 'SSL Error:', 'css' => 'Lerror' );
	// Memo: This rule must be before "Moved channel" to avoid a bug
	$vlogs['highlights'][] =  array( 'filter' => 'Removed channel', 'css' => 'Ladmin' );
	$vlogs['highlights'][] =  array( 'filter' => 'Moved channel', 'css' => 'Ladmin' );
	$vlogs['highlights'][] =  array( 'filter' => 'Changed speak-state', 'css' => 'Ladmin' );
	$vlogs['highlights'][] =  array( 'filter' => 'Added channel', 'css' => 'Ladmin' );
	$vlogs['highlights'][] =  array( 'filter' => 'Renamed channel', 'css' => 'Ladmin' );
	$vlogs['highlights'][] =  array( 'filter' => 'Updated ACL', 'css' => 'Ladmin' );
	$vlogs['highlights'][] =  array( 'filter' => 'Updated banlist', 'css' => 'Ladmin' );
	$vlogs['highlights'][] =  array( 'filter' => 'Server is full', 'css' => 'Lwarn' );
	$vlogs['highlights'][] =  array( 'filter' => 'Rejected connection: Invalid username', 'css' => 'Lwarn' );
	$vlogs['highlights'][] =  array( 'filter' => 'Disconnecting ghost', 'css' => 'Linfo' );
	$vlogs['highlights'][] =  array( 'filter' => 'Rejected connection: Username already in use', 'css' => 'Linfo' );
	$vlogs['highlights'][] =  array( 'filter' => 'Certificate hash is banned.', 'css' => 'Linfo' );
	$vlogs['highlights'][] =  array( 'filter' => 'Rejected connection: Wrong password for user', 'css' => 'Linfo' );
	$vlogs['highlights'][] =  array( 'filter' => '(Server ban)', 'css' => 'Lwarn' );
	$vlogs['highlights'][] =  array( 'filter' => '(Global ban)', 'css' => 'Lwarn' );
	$vlogs['highlights'][] =  array( 'filter' => 'Rejected connection: Invalid server password', 'css' => 'Lwarn' );
	$vlogs['highlights'][] =  array( 'filter' => 'Timeout', 'css' => 'Lwarn' );
	$vlogs['highlights'][] =  array( 'filter' => 'Generating new server certificate.', 'css' => 'Lerror' );
	$vlogs['highlights'][] =  array( 'filter' => 'The address is not available', 'css' => 'Lerror' );
	$vlogs['highlights'][] =  array( 'filter' => 'The bound address is already in use', 'css' => 'Lerror' );
	// Memo: This rule must be before "Announcing server via bonjour" to avoid a bug
	$vlogs['highlights'][] =  array( 'filter' => 'Stopped announcing server via bonjour', 'css' => 'Lwarn' );
	$vlogs['highlights'][] =  array( 'filter' => 'Announcing server via bonjour', 'css' => 'Lwarn' );
	$vlogs['highlights'][] =  array( 'filter' => 'Server listening on', 'css' => 'Linfo' );
	$vlogs['highlights'][] =  array( 'filter' => 'Binding to address', 'css' => 'Lwarn' );
	$vlogs['highlights'][] =  array( 'filter' => 'Server stopped', 'css' => 'Lerror' );
	$vlogs['highlights'][] =  array( 'filter' => 'Unregistered user', 'css' => 'Ladmin' );
	$vlogs['highlights'][] =  array( 'filter' => 'Renamed user', 'css' => 'Ladmin' );
	$vlogs['highlights'][] =  array( 'filter' => 'Kicked', 'css' => 'Ladmin', 'allText' => TRUE );
	$vlogs['highlights'][] =  array( 'filter' => 'Kickbanned', 'css' => 'Ladmin', 'allText' => TRUE );
}

// Construct filter expand menu & remove inactive filters
foreach( $vlogs['filters'] as $bitmask => $patern ) {

	$vlogs['filters_menu'][ $bitmask ] = array( 'txt' => $patern, 'count' => 0 );

	if ( in_array( $bitmask, $vlogs['filters_actived'], TRUE ) ) {

		$vlogs['filters_menu'][ $bitmask ]['active'] = TRUE;

	} else {

		$vlogs['filters_menu'][ $bitmask ]['active'] = FALSE;

		unset( $vlogs['filters'][ $bitmask ] );
	}
}

/**
* Workaround for the timestamp bug with "getLog" method:
*
* getLog() return a modified timestamp if the OS time is not UTC + 00.
*
* ie: OS time is UTC + 01
* getLog return timestamp - 3600 secondes.
*
* UTC + 02 return timestamp - 7200 secondes
* UTC - 03 return timestamp + 10800 secondes
* etc...
*
* This workaround have been tested for linux, I dont know if it's works on BSD.
* "date +%:::z" return the time difference ( +01, +02, -03 ).
*
* On windows, it's definitly doesnt work.
*
* If php safe_mode is actived, you have to set in php.ini:
* safe_mode_exec_dir = /bin ( where the date command is located )
*
*/
$ts_workaround = 0;
if ( PMA_OS === 'linux' ) {

	$linux_tz_diff = exec( 'date +%:::z' );

	if ( is_numeric( $linux_tz_diff ) ) {
		$ts_workaround = $linux_tz_diff * 3600;
	}
}

if ( empty( $getLogs ) ) {

	$output = 'No log found';

} else {

	$output = '';

	foreach ( $getLogs as $log ) {

		// Special traitement for the "Moved" log: try to find if it's self action or on another user.
		if ( in_istring( $log->txt, 'Moved' ) && ! in_istring( $log->txt, 'Moved channel' ) && ! in_istring( $log->txt, 'Removed' ) ) {

			// example : "<35:ipnoz(16)> Moved ipnoz:16(35) to channelName[2:0]"

			// $actor = "<35:ipnoz(16)>"
			// $target = "ipnoz:16(35) to channelName[2:0]"
			list( $actor, $target ) = explode( ' Moved', $log->txt );

			// Find session id -> "<35:"
			preg_match( '/^<[0-9]+:/', $actor, $session );
			$session['id'] = substr( $session[0], 1, -1 );

			// Find user id -> "(16)>" or "(-1)>"
			preg_match( '/\([0-9]+\)>$|\(-1\)>$/', $actor, $uid );
			$uid['id'] = substr( $uid[0], 1, -2 );

			// Find login - remove session and uid string
			$login = str_replace( array( $session[0], $uid[0] ), '', $actor );

			// Now reconstruct target string like murmur do in the logs ( ie: "ipnoz:35(16) to" )
			$reconstructed = $login.':'.$session['id'].'('.$uid['id'].') to';

			// Check if the recontructed target is different.
			if ( in_string( $target, $reconstructed ) ) {

				// Match, user moved self.
				$log->txt = $actor.' Moved to channel'.str_replace( $reconstructed, '', $target );

			} else {

				// Different, user have moved another user
				$log->txt = $actor.' Moved user '.$target;
			}
		}

		// REPLACE ( before logs search )
		if ( isset( $vlogs['replace'] ) ) {

			foreach ( $vlogs['replace'] as $rule ) {

				$txt = $log->txt;

				if ( isset( $rule['regExp'] ) ) {
					$log->txt = preg_replace( $rule['regExp'], $rule['replace'], $txt );

				} elseif ( isset( $rule['filter'] ) ) {
					$log->txt = str_replace( $rule['filter'], $rule['replace'], $txt );
				}

				if ( $log->txt !== $txt ) {
					break;
				}
			}
		}

		//  SEARCH
		if ( isset( $_SESSION['search']['logs'] ) ) {

			if ( ! in_istring( $log->txt, $_SESSION['search']['logs'] ) ) {
				continue;

			} else {
				++$vlogs['total_search_found'];
			}
		}

		//  FILTERS
		foreach ( $vlogs['filters'] as $bitmask => $filter ) {

			if ( in_istring( $log->txt, $filter ) ) {

				++$vlogs['total_filtered'];
				++$vlogs['filters_menu'][ $bitmask ]['count'];

				// Filtered log, continue to next log.
				continue 2;
			}
		}

		// HIGHTLIGHTS
		if ( isset( $vlogs['highlights'] ) ) {

			foreach ( $vlogs['highlights'] as $rule ) {

				if ( in_istring( $log->txt, $rule['filter'] ) ) {

					if ( isset( $rule['allText'] ) ) {
						$log->txt = '<span class="'.$rule['css'].'">'.$log->txt.'</span>';
					} else {
						$log->txt = str_replace( $rule['filter'], '<span class="'.$rule['css'].'">'.$rule['filter'].'</span>', $log->txt );
					}

					if ( isset( $rule['dontBreak'] ) && ! isset( $rule['allText'] ) ) {
						continue;
					} else {
						break;
					}
				}
			}
		}

		// Apply time workaround
		$log->timestamp += $ts_workaround;

		$output .= $LOGS->day( $log->timestamp );
		$output .= '<div><span class="Ltime">'.date( $PMA->cookie->get( 'time' ), $log->timestamp ).'</span>'.$log->txt.'</div>'.EOL;
	}
}

require 'views/logs.php';

?>

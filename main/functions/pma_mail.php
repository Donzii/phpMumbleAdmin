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
* Mail end of line
*/
define( 'MEOL', "\r\n" );

/**
* @function pma_mail
*
* Replace the mail() function.
* Support of pipelining and multiple recipients.
* Catch smtp errors.
*
* @param $to - array
*
* example:
* $to = array( 'type' => 'to', 'email' => 'name@addr.tld' , name => 'Mr name' )
* @see email_headers() ( for key "type" )
*
* @return bool
*/
function pma_mail( $from, $to, $subject, $headers, $message ) {

	global $PMA;

	$host = $PMA->config->get( 'smtp_host' );
	$port = $PMA->config->get( 'smtp_port' );

	// By default, pipelining is turned off.
	$pipelining = FALSE;

	// By default, no valid recipient found
	$valid_recipient_found = FALSE;

	// Connection
	msg_debug( '' );
	msg_debug( __function__ .'(): Connecting to '.$host.' : '.$port.'...' );
	msg_debug( '' );

	$socket = @fsockopen( $host, $port, $errno, $errstr );

	// Check for a valid connection
	if ( ! is_resource( $socket ) ) {
		return smtp_error( 'Invalid ressource '.$errno.' : '.$errstr );
	}

	$resp = smtp_response( $socket );

	// Check for a valid connection
	if ( ! is_int( $resp['code'] ) ) {

		fclose( $socket );
		return smtp_error( 'Invalid smtp server' );

	}
	if ( $resp['code'] !== 220 ) {

		smtp_quit( $socket );
		return smtp_error( $resp['str'] );
	}

	// EHLO / HELO
	smtp_cmd( $socket, 'EHLO '.$_SERVER['HTTP_HOST'] );

	$resp = smtp_response( $socket );

	if ( $resp['code'] === 250 ) {

		$ESMTP = TRUE;

	} else {

		$ESMTP = FALSE;

		// So try HELO
		smtp_cmd( $socket, 'HELO '.$_SERVER['HTTP_HOST'] );

		$resp = smtp_response( $socket );

		if ( $resp['code'] !== 250 ) {

			smtp_quit( $socket );
			return smtp_error( $resp['str'] );
		}
	}

	// Check if the ESMTP server accept pipelining.
	if ( $ESMTP ) {

		// get ESMTP response(s)
		$responses = explode( MEOL, $resp['str'] );

		foreach( $responses as $response ) {

			if ( substr( $response, 4 ) === 'PIPELINING' ) {

				$pipelining = TRUE;
				break;
			}
		}
	}

	if ( $pipelining ) {

		msg_debug( 'Enabling smtp PIPELINING' );

		$pipe[] = 'MAIL FROM:<'.$from.'>';
		foreach( $to as $array ) {
			$pipe[] = 'RCPT TO:<'.$array['email'].'>';
		}
		$pipe[] = 'DATA';

		$last_response = count( $pipe );

		// Send pipelining cmds
		smtp_cmd( $socket, $pipe );

		for ( $i = 1; $i <= $last_response; ++$i ) {

			$resp = smtp_response( $socket, TRUE );

			// MAIL FROM response
			if ( $i === 1 && $resp['code'] !== 250 ) {
				smtp_quit( $socket );
				return smtp_error( $resp['str'] );
			}

			// RCPT TO response(s) ( check for at least one valid recipient ).
			if ( $i > 1 && $i < $last_response ) {

				if ( $resp['code'] === 250 OR $resp['code'] === 251 ) {
					$valid_recipient_found = TRUE;
				}

				continue;
			}

			// DATA response
			if ( $i === $last_response ) {

				// RFC 2920 - The server didn't found valid recipient but accepted the DATA command
				if ( ! $valid_recipient_found && $resp['code'] === 354 ) {

					// close the smtp session imediatly
					smtp_cmd( $socket, '.' );
					smtp_quit( $socket );
					return smtp_error( 'No valid recipients found' );

				// Some server like postfix return a 554 error code which is RFC complient too
				} elseif ( $resp['code'] !== 354 ) {

					smtp_quit( $socket );
					return smtp_error( $resp['str'] );
				}
			}
		}

	// No pipelinging, send cmd one by one.
	} else {

		// MAIL FROM
		smtp_cmd( $socket, 'MAIL FROM:<'.$from.'>' );
		$resp = smtp_response( $socket );

		if ( $resp['code'] !== 250 ) {
			smtp_quit( $socket );
			return smtp_error( $resp['str'] );
		}

		// RCPT TO
		foreach( $to as $array ) {

			smtp_cmd( $socket, 'RCPT TO:<'.$array['email'].'>' );
			$resp = smtp_response( $socket );

			if ( $resp['code'] === 250 OR $resp['code'] === 251 ) {
				$valid_recipient_found = TRUE;
			}
		}

		if ( ! $valid_recipient_found ) {

			smtp_quit( $socket );
			return smtp_error( 'No valid recipients found' );
		}

		// DATA
		smtp_cmd( $socket, 'DATA' );
		$resp = smtp_response( $socket );

		if ( $resp['code'] !== 354 ) {
			smtp_quit( $socket );
			return smtp_error( $resp['str'] );
		}
	}

	// BODY
	$body[] = 'Subject: '.$subject;
	$body[] = 'From: '.$from;
	$body[] = email_headers( $to );
	$body[] = 'X-Mailer: '.PMA_NAME;
	$body[] = 'MIME-Version: 1.0';
	$body[] = 'Content-type: text/html; charset=utf-8';
	$body[] = $headers;
	$body[] = $message;
	$body[] = '.';

	smtp_cmd( $socket, $body );
	$resp = smtp_response( $socket );

	// Msg sent with success
	if ( $resp['code'] === 250 ) {
		smtp_quit( $socket );
		return TRUE;

	} else {
		smtp_quit( $socket );
		return smtp_error( $resp['str'] );
	}
}

function smtp_error( $msg ) {

	$msg = utf8_encode( trim( $msg ) );

	msg_debug( html_encode( $msg ), 1, TRUE );
	write_log( 'smtp.error', $msg );

	return FALSE;
}

function smtp_quit( $socket ) {

	if ( is_resource( $socket ) ) {

		smtp_cmd( $socket, 'QUIT' );
		// Get the response for debugging.
		smtp_response( $socket );
		@fclose( $socket );
	}
}

// Send one or multiple cmds in a row
// @param cmds => string or array
function smtp_cmd( $socket, $cmds ) {

	global $PMA;

	if ( ! is_resource( $socket ) ) {
		return;
	}

	if ( ! is_array( $cmds ) ) {
		$cmds = array( $cmds );
	}

	$send = join( MEOL, $cmds );

	// Send command
	@fwrite( $socket, $send.MEOL );

	if ( PMA_DEBUG > 0 ) {
		foreach( $cmds as $cmd ) {
			msg_debug( 'C: '.html_encode( $cmd ) );
		}
	}
}

function smtp_response( $socket, $byline = FALSE ) {

	$size = 2048;

	$array['str'] = '';
	$array['code'] = -1;

	if ( ! is_resource( $socket ) ) {
		$array['str'] = 'Connection closed during process';
		return $array;
	}

	if ( $byline === TRUE ) {
		$response = fgets( $socket, $size );
	} else {
		$response = fread( $socket, $size );
	}

	$response = trim( $response );

	// This means a timeout or the remote server closed the connection.
	// It can be also the result of a bug from PMA where it's wait for an answer which will never come( untill the timeout is reached ).
	// Memo: do not use smtp_quit(), it will increase the timeout by 2.
	if ( $response === '' ) {
		@fclose( $socket );
	}

	msg_debug( 'S: '. html_encode( $response ) );

	$code = substr( $response, 0, 3 );

	if ( ctype_digit( $code ) ) {

		$array['code'] = (int) $code;

	} else {
		$array['code'] = -1;
	}

	$array['str'] = $response;

	return $array;
}

// Return to and cc headers in this format ( only if one or more email of the type exists ).
// to: <email>, <email>, <email>
// cc: <email>, <email>, <email>
// bcc ( and other ): => discare
function email_headers( $array ) {

	// Return "name <email>" enveloppe
	function enveloppe( $email, $name ) {
		return $name.' <'.$email.'>';
	}

	$tmp = array();
	$return = '';

	foreach( $array as $arr ) {

		$arr['type'] = strToLower( $arr['type'] );

		if ( $arr['type'] === 'to' OR $arr['type'] === 'cc' ) {

			if ( isset( $arr['name'] ) ) {
				$tmp[ $arr['type'] ][] = enveloppe( $arr['email'], $arr['name'] );
			} else {
				$tmp[ $arr['type'] ][] = $arr['email'];
			}
		}
	}

	if ( isset( $tmp['to'] ) ) {
		$return .= 'to: '.join( ', ', $tmp['to'] );
	}

	if ( isset( $tmp['cc'] ) ) {

		if ( isset( $tmp['to'] ) ) {
			$return .= MEOL;
		}
		$return .= 'cc: '.join( ', ', $tmp['cc'] );
	}

	return $return;
}

function get_sender_email( $key ) {

	switch( $key ) {
		case 'pw_gen_sender_email':
			$email = PMA_config::instance()->get( $key );
			break;

		default:
			$email = '';
	}

	if ( $email === '' ) {
		$email = PMA_config::instance()->get( 'smtp_default_sender_email' );
	}

	if ( $email === '' ) {
		$email = 'noreply@'.$_SERVER['HTTP_HOST'];
	}

	return $email;
}

?>
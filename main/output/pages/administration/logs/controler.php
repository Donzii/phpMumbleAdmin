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

if ( ! check_file( PMA_FILE_LOGS ) ) {

	echo 'PMA logs are disabled:<br>'.PMA_FILE_LOGS.' is not writeable.';
	return;
}

pma_load_language( 'logs' );

$LOGS = new PMA_output_logs( $PMA->cookie->get( 'date' ) );

$tabs = array(
	'all' => $TEXT['all'],
	'PMA' => 'PMA',
	'auth' => 'auth',
	'pwGen' => 'pwGen',
	'autoBan' => 'autoBan',
	'smtp' => 'smtp',
	'action' => 'action'
);

$toolbar_tabs = PMA_output_toolbar::tabs( $tabs, 'pma_logs_filter' );

if ( $PMA->config->get( 'pmaLogs_keep' ) > 0 ) {

	$clean_logs = TRUE;

	$day_keep =  $PMA->config->get( 'pmaLogs_keep' )* 24 * 3600;

} else {
	$clean_logs = FALSE;
}

$pmaLogs = @file( PMA_FILE_LOGS );

$pmaLogs = array_reverse( $pmaLogs );

$filter = $_SESSION['page_administration']['pma_logs_filter'];

if ( $filter === 'all' ) {
	$total_of_logs = count( $pmaLogs );
} else {
	$total_of_logs = 0;
}

$output = '';

foreach( $pmaLogs as $key => $line ) {

	// MEMO:
	// [0]timestamp ::: [1]localtime ::: [2]logLvl ::: [3]ip ::: [4]txt ::: [5]EOL
	$line = explode( ':::', $line );

	$timestamp = $line[0];
	$level = $line[2];
	$ip = $line[3];
	$txt = html_encode( $line[4], FALSE );

	// Sanity
	if ( count( $line ) !== 6 ) {

		unset( $pmaLogs[ $key ] );

		$update_log_file = TRUE;
		continue;
	}

	if ( $clean_logs ) {

		// Too old logs
		if ( PMA_TIME > ( $day_keep + $timestamp ) ) {

			unset( $pmaLogs[ $key ] );

			$update_log_file = TRUE;
			continue;
		}
	}

	// Filters
	if ( $filter !== 'all' ) {

		if ( ! in_istring( $level, '['.$filter.'.'  ) ) {
			continue;
		}

		// Count filtered logs
		++$total_of_logs;
	}

	// HighLights
	if ( $PMA->cookie->get( 'highlight_pmaLogs' ) ) {

		// Auth success
		if ( in_string( $level, '[auth.info]' ) ) {

			$level = '<span class="Lauth">'.$level.'</span>';
			$txt = str_replace( 'Successful login', '<span class="Lauth">Successful login</span>', $txt );

		// Auth error
		} elseif ( in_string( $level, '[auth.error]' ) ) {

			$level = '<span class="Lerror">'.$level.'</span>';

			$txt = str_replace( 'Login error', '<span class="Lerror">Login error</span>', $txt );
			$txt = str_replace( 'Password error', '<span class="Lerror">Password error</span>', $txt );

		// Actions
		} elseif ( in_string( $level, '[action.info]' ) ) {

			$level = '<span class="Ladmin">'.$level.'</span>';

			$txt = str_replace( 'Virtual server deleted', '<span class="Lwarn">Virtual server deleted</span>', $txt );
			$txt = str_replace( 'Server stopped', '<span class="Lwarn">Server stopped</span>', $txt );
			$txt = str_replace( 'Virtual server reseted', '<span class="Linfo">Virtual server reseted</span>', $txt );
			$txt = str_replace( 'Virtual server created', '<span class="Ladmin">Virtual server created</span>', $txt );
			$txt = str_replace( 'Server started', '<span class="Linfo">Server started</span>', $txt );

			$txt = str_replace( 'profile deleted', '<span class="Lwarn">profile deleted</span>', $txt );
			$txt = str_replace( 'profile created', '<span class="Linfo">profile created</span>', $txt );
			$txt = str_replace( 'profile updated', '<span class="Ladmin">profile updated</span>', $txt );

			$txt = str_replace( 'Admin account deleted', '<span class="Lwarn">Admin account deleted</span>', $txt );
			$txt = str_replace( 'Admin account created', '<span class="Linfo">Admin account created</span>', $txt );
			$txt = str_replace( 'Admin access updated', '<span class="Ladmin">Admin access updated</span>', $txt );

		// Info
		} elseif ( in_string( $level, '.info]' ) ) {

			$level = '<span class="Linfo">'.$level.'</span>';
			$txt = '<span class="Linfo">'.$txt.'</span>';

		// Warn
		} elseif ( in_string( $level, '.warn]' ) ) {

			$level = '<span class="Lwarn">'.$level.'</span>';

		// Error
		} elseif ( in_string( $level, '.error]' ) ) {

			$level = '<span class="Lerror">'.$level.'</span>';
		}
	}

	$output .= $LOGS->day( $timestamp );
	$output .=  '<div><span class="Ltime">'.date( $PMA->cookie->get( 'time' ), $timestamp ).'</span>'.$level.' - '.$ip.' : '.$txt.'</div>'.EOL;
}

// Update log file
if ( isset( $update_log_file ) ) {

	$pmaLogs = array_reverse( $pmaLogs );

	file_put_contents( PMA_FILE_LOGS, $pmaLogs );
}

require 'views/logs.php';

?>
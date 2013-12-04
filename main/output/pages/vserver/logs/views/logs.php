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

// ToolBar
echo '<div class="toolbar">'.EOL;

$action_menu = new PMA_output_expand_menu( $TEXT['filters'] );

// Highlight link
if ( $PMA->user->is_min( CLASS_ROOTADMIN ) OR $PMA->config->get( 'vlogs_admins_highlights' ) ) {
	$action_menu->add_link( '?cmd=murmur_logs&amp;toggle_highlight', $TEXT['highlight_logs'], HTML::bool_state( $vlogs['allow_highlight'] ) );
	$action_menu->add_separation();
}

// filters
foreach ( $vlogs['filters_menu'] as $bitmask => $rule ) {

	if ( $rule['active'] === TRUE ) {
		$count = '( <span class="count">'.$rule['count'].'</span> )';
	} else {
		$count = '';
	}

	$txt = $rule['txt'].' '.$count;
	$img = HTML::bool_state( $rule['active'] );

	$action_menu->add_link( '?cmd=murmur_logs&amp;toggle_log_filter='.$bitmask, $txt, $img );
}

$action_menu->add_separation();
$action_menu->add( $TEXT['log_filtered'].' : <span class="count">'.$vlogs['total_filtered'].'</span>' );

echo $action_menu->output();

echo PMA_output_toolbar::search( 'logs', $vlogs['total_search_found'] );

echo '</div><!-- ToolBar - END -->'.EOL.EOL;

// Logs div
echo '<div id="logs" class="oBox">'.EOL.EOL;
echo $output;
echo '</div><!-- logs - END -->'.EOL.EOL;

?>

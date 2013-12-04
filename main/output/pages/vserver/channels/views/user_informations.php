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

if ( $uSess->userid === -1 ) {
	$userid = $TEXT['unregistered'];
} else {
	$userid = $uSess->userid;
}

if ( $uSess->tcponly ) {
	$tcp_only = $TEXT['yes'];
} else {
	$tcp_only = $TEXT['no'];
}

echo '<table class="config">'.EOL;

echo '<tr class="invisible"><th style="width: 160px;"></th><th></th></tr>'.EOL;

if ( isset( $uSess->cert_blob ) ) {
	echo '<tr><th colspan="2" class="txtL"><a href="?uCert">'.$TEXT['show_cert'].'</a></th></tr>'.EOL;
} else {
	echo '<tr><th colspan="2"></th></tr>'.EOL;
}

echo '<tr><th>'.$TEXT['ip_addr'].'</th></tr><tr><td colspan="2">'.$uSess->ip.'</td></tr>'.EOL;

echo '<tr><th>'.$TEXT['registration_id'].'</th><td>'.$userid.'</td></tr>'.EOL;

echo '<tr><th>'.$TEXT['session_id'].'</th><td>'.$uSess->session.'</td></tr>'.EOL;

echo '<tr><th>'.$TEXT['online'].'</th><td>'.PMA_helpers_dates::uptime( $uSess->onlinesecs ).'</td></tr>'.EOL;

echo '<tr><th>'.$TEXT['idle'].'</th><td>'.PMA_helpers_dates::uptime( $uSess->idlesecs ).'</td></tr>'.EOL;

echo '<tr><th>'.$TEXT['tcp_mode'].'</th><td>'.$tcp_only.'</td></tr>'.EOL;

echo '<tr><th>'.$TEXT['bandwidth'];
echo HTML::info_bubble( HTML::img( IMG_INFO_16 ), $TEXT['bandwidth_info'] ).'</th>';
// I know, 1 byte = 8 ( or 9 ) bits, but only *10 return what the mumble client do.
echo '<td>'.convert_size( $uSess->bytespersec * 10 ).'</td></tr>'.EOL;

// udpPing and tcpPing comes with murmur 1.2.4
if ( isset( $uSess->udpPing ) ) {
	echo '<tr><th>'.$TEXT['udp_ping'];
	echo HTML::info_bubble( HTML::img( IMG_INFO_16 ), $TEXT['ping_info'] ).'</th>';
	echo '<td>'.round( $uSess->udpPing, 2 ).'</td></tr>'.EOL;
}

if ( isset( $uSess->udpPing ) ) {
	echo '<tr><th>'.$TEXT['tcp_ping'];
	echo HTML::info_bubble( HTML::img( IMG_INFO_16 ), $TEXT['ping_info'] ).'</th>';
	echo '<td>'.round( $uSess->tcpPing, 2 ).'</td></tr>'.EOL;
}

// Mumble release
echo '<tr><th>'.$TEXT['mumble_client'].'</th><td>'.$uSess->release.'</td></tr>'.EOL;

echo '<tr><th>'.$TEXT['os'].'</th><td>'.$uSess->os .' '. $uSess->osversion.'</td></tr>'.EOL;

if ( isset( $uSess->cert_sha1 ) ) {
	echo '<tr><th>'.$TEXT['cert_hash'].'</th></tr>'.EOL;
	echo '<tr><td colspan="2">'.$uSess->cert_sha1.'</td></tr>'.EOL;
}

echo '</table>'.EOL;

?>

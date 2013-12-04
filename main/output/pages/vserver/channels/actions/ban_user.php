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

if ( ! isset( $uSess->cert_blob ) ) {
	return;
}

$bantxt = sprintf( $TEXT['ban_user'], $uSess->name );

$actionBox = new PMA_output_actionBox();

$actionBox->set_conf( 'css', '' );

$actionBox->form();
$actionBox->cancel();
$actionBox->input( 'hidden', 'cmd', 'murmur_bans' );
$actionBox->input( 'hidden', 'addBan', '' );
$actionBox->input( 'hidden', 'name', $uSess->name );
$actionBox->input( 'hidden', 'ip', $uSess->ip );
$actionBox->input( 'hidden', 'mask', '' );
$actionBox->input( 'hidden', 'hash', $uSess->cert_sha1 );
$actionBox->input( 'hidden', 'kickhim', '' );

$actionBox->output .= '<h1>'.$bantxt.'</h1>'.EOL;
$actionBox->output .= '<div class="b txtL"><label for="reason">'.$TEXT['reason'].'</label></div>'.EOL;
$actionBox->output .= '<div class="txtL"><input type="text" id="reason" name="reason"></div>'.EOL;

$actionBox->output .= PMA_output_bans::duration();

$actionBox->input( 'submit', '', $bantxt );
$actionBox->close();

echo $actionBox->output;

?>

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

// Transform DER file to PEM
$encode64 = base64_encode( $uSess->cert_blob );

// PEM file must be 65 characters per line.
$lines = str_split( $encode64, 65 );
$body = join( EOL, $lines );

$certificate = '-----BEGIN CERTIFICATE-----'.EOL;
$certificate .= $body.EOL;
$certificate .= '-----END CERTIFICATE-----'.EOL;

echo '<div class="txtR"><a href="./">'.$TEXT['back'].'</a></div>'.EOL;
echo '<div class="description" style="margin: 5px 0px;">'.EOL;
echo PMA_output_certificate::get( $certificate );
echo '</div>'.EOL;

?>

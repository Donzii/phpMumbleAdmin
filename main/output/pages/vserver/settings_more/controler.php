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

pma_load_language( 'vserver_settings' );

$JS->add_text( 'confirm_del_certificate', $TEXT['confirm_reset_cert'] );

echo '<div style="width: 700px; margin: auto;" class="oBox">'.EOL.EOL;

if ( isset( $_GET['reset_certificate'] ) ) {

	require 'actions/reset_certificate.php';

	echo '</div>'.EOL.EOL;
	return;

}

try {
	$custom_conf = $getServer->getAllConf();

} catch ( Exception $Ex ) {

	$custom_conf = array();
	pma_murmur_exception( $Ex );
}

$welcometext = $getServer->get_conf( 'welcometext' );
$certificate = $getServer->get_conf( 'certificate' );
$certkey = $getServer->get_conf( 'key' );

$tabs = array( 'welcometext' => $TEXT['welcometext'], 'certificate' => $TEXT['certificate'] );

if ( $PMA->user->is( CLASS_SUPERADMIN ) ) {
	$tabs['pem'] = 'PEM';
}

require 'views/toolbar.php';

switch( $view = $_SESSION['page_vserver']['more_tab'] ) {

	case 'welcometext':
	case 'certificate':
	case 'pem':

		require 'views/'.$view.'.php';
}

echo '</div>'.EOL.EOL;

?>

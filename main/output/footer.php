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

if ( $PMA->user->is_min( CLASS_ROOTADMIN ) ) {

	$list = PMA_whos_online::instance()->get_all();
	require 'footer_whos_online.php';
}

$version = ( $PMA->user->is_min( CLASS_ROOTADMIN ) ) ? ' '.PMA_VERS_STR.PMA_VERS_DESC : '';

echo '<div id="footer">'.EOL;
echo '<div>'.strftime( '%A ' ).PMA_helpers_dates::complet( PMA_TIME ).'</div>';
echo '<div>Powered by <a href="http://sourceforge.net/projects/phpmumbleadmin/">'.PMA_NAME.'</a>'.$version.'</div>'.EOL;
echo '</div><!-- footer - END -->'.EOL.EOL;

?>
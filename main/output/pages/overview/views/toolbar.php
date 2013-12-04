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

echo '<div class="toolbar">';

if ( $PMA->user->is_min( CLASS_ROOTADMIN ) ) {

	// Murmur conf
	echo '<a href="?action=murmur_conf" title="'.$TEXT['default_settings'].'">'.HTML::img( IMG_INFO_22, 'button' ).'</a>'.EOL;

	// mass settings
	echo '<a href="?action=mass_settings" title="'.$TEXT['mass_settings'].'">'.HTML::img( 'tango/settings_22.png', 'button' ).'</a>'.EOL;

	echo HTML::img( IMG_SPACE_16 ).EOL;
}

if ( $PMA->user->is_min( CLASS_ADMIN_FULL_ACCESS ) ) {

	// Add a virtual server
	echo '<a href="?action=add_vserver" title="'.$TEXT['add_srv'].'" onClick="return add_vserver();">';
	echo HTML::img( IMG_ADD_22, 'button' ).'</a>'.EOL;
}

if ( $PMA->user->is_min( CLASS_ADMIN ) ) {

	// Send message
	echo '<a href="?action=send_msg_vservers" title="'.$TEXT['msg_all_srv'].'" onClick="return send_msg_all_vservers();">';
	echo HTML::img( IMG_MSG_22, 'button' ).'</a>'.EOL;
}

echo '</div>'.EOL.EOL;

?>
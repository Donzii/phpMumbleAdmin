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

$actionBox = new PMA_output_actionBox();

$actionBox->set_conf( 'css', 'alert' );
$actionBox->set_conf( 'onSubmit', 'return form_is_modified( this );' );

$actionBox->form();
$actionBox->input( 'hidden', 'cmd', 'murmur_channel' );
$actionBox->input( 'hidden', 'move_users_into_the_channel', '' );
$actionBox->cancel();

// 	// Test scroll
// 	for ( $i=100000; $i < 100150; ++$i ) {
// 		$getUsers[$i] = new Murmur_User();
// 		$getUsers[$i]->name = 'name-'.$i;
// 		$getUsers[$i]->session = $i;
// 	}
// 	$count_getUsers = count($getUsers);

foreach( $getUsers as $obj ) {

	// Show only users out of the channel
	if ( $obj->channel !== $CHANNEL->id ) {
		$actionBox->custom_datas .= '<div class="name">';
		$actionBox->custom_datas .= '<input type="checkbox" id="id'.$obj->session.'" name="'.$obj->session.'"> ';
		$actionBox->custom_datas .= '<label for="id'.$obj->session.'">'.$obj->name.'</label>';
		$actionBox->custom_datas .= '</div>'.EOL;
	}
}

$actionBox->scroll( $TEXT['move_user_in_chan'], $count_getUsers );

$actionBox->close();

echo $actionBox->output;

?>

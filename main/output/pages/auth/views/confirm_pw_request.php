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

$PW_REQUESTS = PMA_pw_requests::instance();

echo '<div class="actionBox small">'.EOL;

if ( ! isset( $PW_REQUESTS->found ) ) {

	echo $TEXT['request_dont_exists'];

} else {

	$request = $PW_REQUESTS->found;

	// Remove
	$PW_REQUESTS->delete( $request['id'] );

	// Profile host / port must be the same.
	$profile = PMA_profiles::instance()->get( $PMA->user->profile_id );

	if ( $request['profile_host'] !== $profile['host'] OR $request['profile_port'] !== $profile['port'] ) {
		$request = NULL;
		echo 'Error during password request process, please retry...';
	}

	if ( NULL !== $getServer = $PMA->meta->getServer( $request['sid'] ) ) {

		// Start the virtual server if it's stopped
		$isRunning = $getServer->isRunning();

		if ( ! $isRunning ) {
			$getServer->start();
		}

		// Generate a new random password
		$newPassword = gen_random_chars( 16 );

		try {
			// get user registration
			$user = $getServer->getRegistration( $request['uid'] );
			// set the new generated password
			$user[4] = $newPassword;
			// update registration
			$getServer->updateRegistration( $request['uid'], $user );

			// New password
			echo $TEXT['new_generated_pw'].'<br><div style="font-size: 24px; margin: 5px;"><b>'.$newPassword.'</b></div>';

		} catch( ice_exexption $Ex ) {
			pma_murmur_exception( $Ex );
		}

		// Stop the virtual server if it was stopped.
		if ( ! $isRunning ) {
			$getServer->stop();
		}
	}
}

// Back to auth link
echo '<div style="text-align: right; margin-top: 25px;"><a href="./">'.$TEXT['back_auth_page'].'</a></div>'.EOL;
echo '</div>'.EOL.EOL;

?>

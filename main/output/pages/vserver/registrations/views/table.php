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

function get_all_registrations( $prx, $getRegisteredUsers, $getUsers ) {

	$user = PMA_user::instance();

	$search_found = 0;

	$list = array();

	foreach( $getRegisteredUsers as $uid => $username ) {

		// SuperUser_ru dont have access to SuperUser registration
		if ( $uid === 0 && $user->is( CLASS_SUPERUSER_RU ) ) {
			continue;
		}

		// Search
		if ( isset( $_SESSION['search']['registrations'] ) ) {

			if ( in_istring( $username, $_SESSION['search']['registrations'] ) ) {
				++$search_found;
			} else {
				continue;
			}
		}

		try {
			$getRegistration = $prx->getRegistration( $uid );
		} catch ( Exception $Ex ) {
			pma_murmur_exception( $Ex );
		}

		$status = registered_is_online( $uid, $getUsers );

		$list[ $uid ]['status'] = $status['status'];

		$list[ $uid ]['url'] = $status['url'];

		$list[ $uid ]['id'] = $uid;

		$list[ $uid ]['login'] = $username;

		if ( isset( $getRegistration[1] ) && $getRegistration[1] !== '' ) {
			$list[ $uid ]['email'] = $getRegistration[1];
		} else {
			$list[ $uid ]['email'] = EMPTY_SORT_WORKAROUND;
		}

		if ( isset( $getRegistration[2] ) && $getRegistration[2] !== '' ) {
			$list[ $uid ]['comment'] = 1;
		} else {
			$list[ $uid ]['comment'] = 2;
		}

		if ( isset( $getRegistration[3] ) && $getRegistration[3] !== '' ) {
			$list[ $uid ]['hash'] = 1;
		} else {
			$list[ $uid ]['hash'] = 2;
		}

		if ( isset( $getRegistration[5] ) ) {

			if ( $getRegistration[5] !== '' ) {
				$list[ $uid ]['last_activity'] = PMA_helpers_dates::datetime_to_timestamp( $getRegistration[5] );
			} else {
				$list[ $uid ]['last_activity'] = EMPTY_SORT_WORKAROUND;
			}
		}
	}

	$registrations['list'] = $list;
	$registrations['search_found'] = $search_found;

	return $registrations;
}

function users_table_per_lines( $list ) {

	$columns = 7;

	if ( PMA_meta::instance()->int_version >= 123 ) {
		++$columns;
	}

	$output = '';

	foreach( $list as $user ) {

		// Status
		if ( $user['status'] === 1 ) {
			$status = '<a href="?tab=channels&amp;userSession='.$user['url'].'">';
			$status .= HTML::img( IMG_SPACE_16, 'button on' ).'</a>';
		} else {
			$status = HTML::img( IMG_SPACE_16, 'button off' );
		}

		// Login
		$login = html_encode( $user['login'] );

		if ( $user['id'] === 0 && strtolower( $user['login'] ) !== 'superuser' ) {
			$login .= ' <i>( SuperUser )</i>';
		}

		// Email
		if ( $user['email'] !== EMPTY_SORT_WORKAROUND ) {
			$email = '<span class="email"><a href="mailto:'.$user['email'].'" title="mailto:'.$user['email'].'">'.$user['email'].'</a></span>';
		} else {
			$email = '';
		}

		// UserLastActive come with murmur 1.2.3
		if ( isset( $user['last_activity'] ) ) {

			if ( is_int( $user['last_activity'] ) ) {
				$title = PMA_helpers_dates::complet( $user['last_activity'] );
				$last_activity = '<span class="help" title="'.$title.'">'.PMA_helpers_dates::uptime( PMA_TIME - $user['last_activity'] ).'</span>';
			} else {
				$last_activity = '';
			}
		}

		if ( $user['comment'] === 1 ) {
			$comment = HTML::img( 'mumble/comment.png' );
		} else {
			$comment = '';
		}

		if ( $user['hash'] === 1 ) {
			$hash = HTML::img( IMG_OK_16 );
		} else {
			$hash = '';
		}

		if ( $user['id'] > 0 ) {
			$js = 'onClick="return del_account_id( this, \''.$user['id'].'\', \''.$login.'\' );"';
			$delete = '<a href="?delete_account_id='.$user['id'].'" '.$js.'>'.HTML::img( IMG_TRASH_16, 'button' ).'</a>';
		} else {
			$delete = '';
		}

		$output .= '<tr>';
		$output .= '<td class="icon">'.$status.'</td>';
		$output .= '<td class="id">'.$user['id'].'</td>';
		$output .= '<td class="b selection"><a href="?registration_id='.$user['id'].'">'.$login.'</a></td>';
		$output .= '<td><div class="name">'.$email.'</div></td>';
		if ( isset( $last_activity ) ) {
			$output .= '<td>'.$last_activity.'</td>';
		}
		$output .= '<td class="icon">'.$comment.'</td>';
		$output .= '<td class="icon">'.$hash.'</td>';
		$output .= '<td class="icon">'.$delete.'</td>';
		$output .= '</tr>'.EOL;
	}

	return $output;
}

// PHP sort empty or null string at top. I prefert it bottom.
define( 'EMPTY_SORT_WORKAROUND', str_repeat( 'z', 160 ) );

$registrations = get_all_registrations( $getServer, $getRegisteredUsers, $getUsers );

$TABLE = new PMA_output_table( $registrations['list'], 'users', 'users_table_per_lines' );
$TABLE->sort_datas( 'id' );
$TABLE->paging_datas( $PMA->config->get( 'table_users' ) );

$TABLE->add_column( 'icon', $TABLE->sort->url( 'status', 'S', 'short' ) );
$TABLE->add_column( 'id', $TABLE->sort->url( 'id', 'id', 'short' ) );
$TABLE->add_column( '', $TABLE->sort->url( 'login', $TEXT['login'] ) );
$TABLE->add_column( 'vlarge', $TABLE->sort->url( 'email', $TEXT['email_addr'] ) );
//UserLastActive come with murmur 1.2.3
if ( $PMA->meta->int_version >= 123 ) {
	$TABLE->add_column( 'large', $TABLE->sort->url( 'last_activity', $TEXT['last_activity'] ) );
}
$TABLE->add_column( 'icon', $TABLE->sort->url( 'comment', 'C', 'short' ) );
$TABLE->add_column( 'icon', $TABLE->sort->url( 'hash', 'H', 'short' ) );
$TABLE->add_column( 'icon' );

// ToolBar
echo '<div class="toolbar">'.EOL;
// Add new account
echo '<a title="'.$TEXT['add_acc'].'" href="?add_new_account" onClick="return add_account();">';
echo HTML::img( IMG_ADD_22, 'button' ).'</a>'.EOL;
echo PMA_output_toolbar::search( 'registrations', $registrations['search_found'] );
echo '</div>'.EOL.EOL;

echo $TABLE->output();

// CAPTION
echo '<div style="margin: 10px 0px;">';
echo '<span class="caption">'.EOL;
echo '<span style="margin-right: 10px;">'.$TEXT['caption'].'</span>';
echo HTML::info_bubble( HTML::img( IMG_SPACE_16, 'button on' ), $TEXT['user_is_online'] );
echo HTML::info_bubble( HTML::img( IMG_SPACE_16, 'button off' ), $TEXT['offline'] );
echo HTML::info_bubble( HTML::img( 'mumble/comment.png' ), $TEXT['have_a_comm'] );
echo HTML::info_bubble( HTML::img( IMG_OK_16), $TEXT['have_a_cert'] );
echo HTML::info_bubble( HTML::img( IMG_TRASH_16, 'button' ), $TEXT['delete_acc'] );
echo '</span></div>'.EOL.EOL;


?>

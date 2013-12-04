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

function vserver_name() {

	global $TEXT;

	if ( PMA_cookie::instance()->get( 'infoPanel' ) ) {
		$img = IMG_2_DELETE_16;
	} else {
		$img = IMG_2_ADD_16;
	}

	if ( SUHOSIN_COOKIE_ENCRYPT ) {
		$js = '';
	} else {
		$js = 'onClick="return toggle_infopanel();"';
	}

	$output = '<div class="vserver name">'.EOL;
	$output .= '<a href="?cmd=config&amp;toggle_infopanel" '.$js.'>';
	$output .= HTML::img( $img, 'button', $TEXT['toggle_panel'], 'js_infopanel' ).'</a>'.EOL;
	$output .= $_SESSION['page_vserver']['id'].'<span class="sharp">#</span>'.html_encode( VSERVER_NAME ).EOL;

	$output .= '</div><!-- vserverName - END -->'.EOL;

	return $output;
}

function dropdown_list( $page ) {

	global $TEXT;

	$user = PMA_user::instance();
	$vservers_cache = PMA_vservers_cache::instance()->get_current();
	$show_cache_uptime = PMA_config::instance()->get( 'ddl_show_cache_uptime' );

	$cache_uptime = '';

	if ( ! isset( $vservers_cache['vservers'] ) ) {
		return;
	}

	$output = '<form class="dropdown_list" method="GET" action="">';
	$output .= '<input type="hidden" name="page" value="vserver">';

	// Refresh vservers cache list button
	if ( $user->is_min( CLASS_ROOTADMIN ) ) {

		// Last refresh uptime
		$cache_uptime = ' ( '.PMA_helpers_dates::uptime( PMA_TIME - $vservers_cache['cache_time'] ).' )';

		$output .= '<a href="?cmd=overview&amp;refreshServerList">';
		$output .= HTML::img( 'tango/refresh_16.png', 'button', $TEXT['refresh_srv_cache'].$cache_uptime ).'</a>';
	}

	$txt = $TEXT['select_server'];

	if ( $show_cache_uptime ) {
		$txt .= ' '.$cache_uptime;
	}

	$output .= '<select name="sid" onChange="submit();"><option>'.$txt.'</option>';

	// DROPDOWN LIST
	foreach( $vservers_cache['vservers'] as $array ) {

		// Remove virtual server that's current admin don't have access
		if ( $user->is( CLASS_ADMIN ) ) {

			if ( ! $user->check_admin_sid( $array['id'] ) ) {
				continue;
			}
		}

		if ( ! isset( $prev_next_menu['first'] ) ) {
			$prev_next_menu['first'] = $array['id'];
		}

		if ( isset( $current_vserver_reached ) ) {

			$prev_next_menu['next'] = $array['id'];
			unset( $current_vserver_reached );
		}

		// Disallow to select the virtual server where we are.
		if ( $page === 'vserver' && $array['id'] === $_SESSION['page_vserver']['id'] ) {

			$output .= '<option class="selected disabled" disabled="disabled">'.$array['id'].'# '.$array['name'].'</option>';

			if ( isset( $prev ) ) {
				$prev_next_menu['prev'] = $prev;
			}

			$current_vserver_reached = TRUE;

		} elseif ( isset( $_SESSION['page_vserver']['id'] ) && $array['id'] === $_SESSION['page_vserver']['id'] ) {

			$output .= '<option class="selected" value="'.$array['id'].'">'.$array['id'].'# '.$array['name'].'</option>';

		} else {
			$output .= '<option value="'.$array['id'].'">'.$array['id'].'# '.$array['name'].'</option>';
		}

		$prev = $array['id'];
		$prev_next_menu['last'] = $array['id'];
	}

	$output .= '</select><noscript> <input type="submit" value="'.$TEXT['ok'].'"></noscript>'.EOL;

	// PREV / NEXT buttons
	if ( $page === 'vserver' && count( $vservers_cache['vservers'] ) > 1 ) {

		// First
		if ( $_SESSION['page_vserver']['id'] != $prev_next_menu['first'] ) {
			$output .= '<a href="?page=vserver&amp;sid='.$prev_next_menu['first'].'">'.HTML::img( 'tango/page_first_16.png', 'button' ).'</a>'.EOL;
		} else {
			$output .= HTML::img( IMG_SPACE_16, 'button' ).EOL;
		}

		// previous
		if ( isset( $prev_next_menu['prev'] ) ) {
			$output .= '<a href="?page=vserver&amp;sid='.$prev_next_menu['prev'].'">'.HTML::img( 'tango/page_prev_16.png', 'button' ).'</a>'.EOL;
		} else {
			$output .= HTML::img( IMG_SPACE_16, 'button' ).EOL;
		}

		// next
		if ( isset( $prev_next_menu['next'] ) ) {
			$output .= '<a href="?page=vserver&amp;sid='.$prev_next_menu['next'].'">'.HTML::img( 'tango/page_next_16.png', 'button' ).'</a>'.EOL;
		} else {
			$output .= HTML::img( IMG_SPACE_16, 'button' ).EOL;
		}

		// Last
		if ( $_SESSION['page_vserver']['id'] != $prev_next_menu['last'] ) {
			$output .= '<a href="?page=vserver&amp;sid='.$prev_next_menu['last'].'">'.HTML::img( 'tango/page_last_16.png', 'button' ).'</a>'.EOL;
		} else {
			$output .= HTML::img( IMG_SPACE_16, 'button' ).EOL;
		}

	} else {
		$output .= HTML::img( IMG_SPACE_16, 'button' ).EOL;
		$output .= HTML::img( IMG_SPACE_16, 'button' ).EOL;
		$output .= HTML::img( IMG_SPACE_16, 'button' ).EOL;
		$output .= HTML::img( IMG_SPACE_16, 'button' ).EOL;
	}

	$output .= '</form><!-- server_dropdown_list - END -->'.EOL;

	return $output;
}

echo '<div class="page_top">'.EOL;

if ( $PMA->user->is_min( CLASS_ADMIN ) ) {
	echo dropdown_list( $PMA->pages->current() );
}

if ( defined( 'VSERVER_NAME' ) ) {
	echo vserver_name();
}

echo '</div><!-- page_top - END -->'.EOL.EOL;

?>
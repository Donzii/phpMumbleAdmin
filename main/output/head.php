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

function languages_flags( $list ) {

	$current = PMA_cookie::instance()->get( 'lang' );

	$output = '';

	foreach( $list as $language ) {

		$flag = PMA_DIR_LANGUAGES.$language['dir'].'/flag.png';

		if ( ! is_readable( $flag ) ) {
			$flag = 'images/aha-soft/pirate.png';
		}

		if ( $language['dir'] === $current ) {
			$selected = 'class="selected"';
		} else {
			$selected = '';
		}

		$output .= '<li><a href="?cmd=config&amp;setLang='.$language['dir'].'">';
		$output .= '<img '.$selected.' src="'.$flag.'" alt="" title="'.$language['name'].'"></a></li>'.EOL;
	}

	return $output;
}

function pages_menu( $pages, $current ) {

	global $TEXT;

	$user = PMA_user::instance();

	$output = '';

	foreach( $pages as $page ) {

		// Dont show server page for them.
		if ( $page === 'vserver' && $user->is_min( CLASS_ADMIN ) ) {
			continue;
		}

		if ( isset( $TEXT['page_'.$page] ) ) {
			$text = $TEXT['page_'.$page];
		} else {
			$text = $page;
		}

		if ( $current === $page ) {
			$css = 'class="selected"';
		} else {
			$css = '';
		}

		$output .= '<li><a href="?page='.$page.'" '.$css.'>'.$text.'</a></li>'.EOL;
	}

	return $output;
}

$languages_flags = '';

if ( COOKIE_ACCEPTED ) {

	if ( $PMA->user->is( CLASS_UNAUTH ) OR $PMA->config->get( 'debug_select_flag' ) ) {

		$languages = PMA_helpers_options::get_languages();

		if ( count( $languages > 1 ) ) {

			$languages_flags = '<ul class="languages">'.EOL;
			$languages_flags .= languages_flags( $languages );
			$languages_flags .= '</ul>'.EOL;
		}
	}
}

if ( ! $PMA->user->is( CLASS_UNAUTH ) ) {

	$logout = sprintf( $TEXT['autologout_at'], date( $PMA->cookie->get( 'time' ), PMA_TIME + ( $PMA->config->get( 'auto_logout' ) * 60 ) ) );

	if ( $PMA->user->is( CLASS_SUPERADMIN ) && rand( 1, 10 ) === 5 ) {

		// Never forget, you are the King in this place :D .
		$userclass = 'The King of the Hill';

	} else {
		$userclass = pma_class_name( $PMA->user->class );
	}

	$bottom = '<a href="?cmd=logout" title="'.$logout.'">'.$TEXT['logout'].'</a> ';
	$bottom .= html_encode( $PMA->user->login );
	$bottom .= '( <span class="userclass">'.$userclass.'</span> )'.EOL;
	$bottom .= '<ul class="right pages">'.EOL;
	$bottom .= pages_menu( $PMA->pages->get_avalaibles(), $PMA->pages->current() );
	$bottom .= '</ul>'.EOL;

} else {
	$bottom = '';
}

$title = html_encode( $PMA->config->get( 'siteTitle' ) );
$comment = html_encode( $PMA->config->get( 'siteComment' ) );

?>
<div id="head">

<?php echo $languages_flags; ?>

<div class="title name"><?php echo $title; ?></div>

<div class="comment name"><?php echo $comment; ?></div>

<div class="bottom">
<?php echo $bottom; ?>
</div><!-- bottom - END -->

</div><!-- head - END -->

<?php

require 'head_profiles.php';

if (
	isset( $PMA->messages['box'] )
	OR isset( $PMA->messages['alert'] )
	OR isset( $PMA->messages['ice_error'] )
) {
	require 'head_messages.php';
}

?>
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

if ( ! $PMA->config->get( 'external_viewer_enable' ) OR ! isset( $_GET['server'] ) ) {
	die();
}

$profile = PMA_profiles::instance()->get( $pid );

// Profile has to be public
if ( $profile['public'] !== TRUE ) {
	die;
}

require 'main/include/def.images.php';

define( 'PMA_EXT_VIEWER_DEBUG', ( PMA_DEBUG > 0 && FALSE ) );

$meta = PMA_meta::instance( $profile );

if ( ! pma_ice_conn_is_valid() ) {
	die;
}

function get_pixels( $int ) {

	if ( $int > 0 ) {
		return $int.'px';
	}

	return 'none';
}

$width = get_pixels( $PMA->config->get( 'external_viewer_width' ) );
$height = get_pixels( $PMA->config->get( 'external_viewer_height' ) );
$vertical = $PMA->config->get( 'external_viewer_vertical' );
$scroll = $PMA->config->get( 'external_viewer_scroll' );

echo '<style type="text/css">'.EOL;
echo file_get_contents( 'css/skel.common.css' );
echo "
.ext_viewer {
	cursor: default;
	line-height: 16px;
	width: $width;
	height: $height;".EOL;
if ( $vertical ) {
	echo '	float: left;'.EOL;
}
if ( $scroll ) {
	echo '	overflow: auto;'.EOL;
} else {
	echo '	overflow: hidden;'.EOL;
}
echo '}'.EOL.EOL;

echo '
.ext_viewer div {
	height: 16px;
	white-space: nowrap;
}
.ext_viewer div .name {
	overflow: visible;
}
'.EOL;
echo '</style>'.EOL.EOL;

$viewer = new PMA_output_external_viewer( $meta, $_GET['server'] );

echo $viewer->get_cache();

if ( PMA_EXT_VIEWER_DEBUG ) {

	$debug = new PMA_output_debug();

	$debug->get_stats();

	if ( isset( $PMA->messages['debug'] ) ) {
		$debug->get_debug_messages( $PMA->messages['debug'] );
	}

	echo '<div style="clear: both"></div>'.EOL.EOL;
	echo $debug->get_cache();
}


?>
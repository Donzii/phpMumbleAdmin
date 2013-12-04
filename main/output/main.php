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

require 'main/include/def.images.php';

PMA_whos_online::instance()->update_current_user();

$PMA->pages = new PMA_controler_pages( $PMA->user, $_SESSION['page'] );

pma_load_language( 'common' );
pma_load_language( $PMA->pages->current() );

$OUTPUT = new PMA_output();
$JS = new PMA_output_js();

// Default css class for box class
$OUTPUT->box = 'box';

msg_debug( '<span class="maroon b">Starting contents cache</span>', 2 );
// caching current page contents
ob_start();

require 'pages/'.$PMA->pages->current().'/controler.php';

$OUTPUT->cache( ob_get_clean() );

msg_debug( '<span class="maroon b">End of contents cache</span>', 2 );

///////////////////////////////////////
// OUTPUT
//////////////////////////////////////

require 'headers.php';

require 'head.php';
require 'body.php';
require 'footer.php';

$PMA->db->save_all_datas();

if ( PMA_DEBUG > 0 ) {
	require 'debug.php';
}

// END - close HTML page.
echo '</body>'.EOL;
echo '</html>';

?>
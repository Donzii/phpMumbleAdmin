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

echo '<div style="margin: 10px 0px;">'.EOL;
echo '<span class="caption">'.EOL;

echo '<span style="margin-right: 10px;">'.$TEXT['caption'].'</span>'.EOL;

echo HTML::info_bubble( HTML::img( 'other/home_16.png' ), $TEXT['img_defaultchannel'] );
echo HTML::info_bubble( HTML::img( 'pma/tree_link_with.png' ), $TEXT['img_linked'] );
echo HTML::info_bubble( HTML::img( 'pma/tree_link_direct.png' ), $TEXT['img_link_direct'] );
echo HTML::info_bubble( HTML::img( 'pma/tree_link_indirect.png' ), $TEXT['img_link_undirect'] );
echo HTML::info_bubble( HTML::img( 'gei/padlock_16.png' ), $TEXT['img_channel'] );
echo HTML::info_bubble( HTML::img( 'tango/clock_16.png' ), $TEXT['img_temp'] );
echo HTML::info_bubble( HTML::img( 'mumble/comment.png' ), $TEXT['img_comment'] );
echo HTML::info_bubble( HTML::img( 'mumble/user_auth.png' ), $TEXT['img_auth'] );
echo HTML::info_bubble( HTML::img( 'xchat/red_16.png' ), $TEXT['img_recording'] );
echo HTML::info_bubble( HTML::img( 'tango/microphone_16.png' ), $TEXT['img_priorityspeaker'] );
echo HTML::info_bubble( HTML::img( 'mumble/user_suppressed.png' ), $TEXT['img_supressed'] );
echo HTML::info_bubble( HTML::img( 'mumble/user_muted.png' ), $TEXT['img_muted'] );
echo HTML::info_bubble( HTML::img( 'mumble/user_deafened.png' ), $TEXT['img_deafened '] );
echo HTML::info_bubble( HTML::img( 'mumble/user_selfmute.png' ), $TEXT['img_mute'] );
echo HTML::info_bubble( HTML::img( 'mumble/user_selfdeaf.png' ), $TEXT['img_deaf'] );

echo '</span></div>'.EOL.EOL;

?>

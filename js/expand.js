
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

// This var will check if toggle_expand have been cliked,
// otherwise, user can close expand on an outside click.
var toggle_expand_done = false;

function expand( e, el ) {

	el.style.cursor = 'pointer';

	el.addEventListener( 'click', function() { toggle_expand( ul[0] ); }, false );

	ul = el.getElementsByTagName( 'ul' );
	ul[0].style.display = 'none';
	ul[0].style.cursor = 'default';

	document.addEventListener( 'click', function() { close_expand( ul[0] ); }, false );
	document.addEventListener( 'mouseup', function() { toggle_expand_done = false; }, false );

	return false;
}

function toggle_expand( doc ) {

	if ( doc.style.display === 'none' ) {
		doc.style.display = 'block';
	} else {
		doc.style.display = 'none';
	}

	toggle_expand_done = true;
}

// Close expand on outside click
function close_expand( doc ) {

	if ( ! toggle_expand_done ) {
		doc.style.display = 'none';
	}
}

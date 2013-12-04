
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

window.onload = overview_onload;

function overview_onload() {
	if ( document.getElementById( 'send_msg_vservers' ) ) {
		document.getElementById( 'send_msg_vservers' ).focus();
	}
}

function validate_mass_setting( el, confirm_word ) {

	if ( el.key.options[0].selected === true ) {
		el.key.focus();
		return false;
	}

	if ( el.confirm.value !== confirm_word ) {
		el.confirm.focus();
		return false;
	}
}

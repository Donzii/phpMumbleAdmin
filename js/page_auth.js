
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

window.onload = function() { document.getElementById( 'login' ).focus() };

// Validate auth field
function validate_auth( doc ) {
	if ( doc.login.value === '' ) {
		doc.login.select();
		return false;
	}
	if ( doc.password.value === '' ) {
		doc.password.select();
		return false;
	}
}

// Validate generate password field
function validate_gen_passw( doc ) {
	if ( doc.login.value === '' ) {
		doc.login.select();
		return false;
	}
	if ( doc.server_id.type == 'text' && doc.server_id.value === '' ) {
		doc.server_id.select();
		return false;
	}
	if ( doc.server_id.type == 'select-one' && doc.server_id.options[0].selected === true ) {
		doc.server_id.focus();
		return false;
	}
}
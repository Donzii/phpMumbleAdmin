
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

window.onload = administration_onload;

function administration_onload() {

	if ( document.getElementById( 'login' ) ) {

		// Focus login only if we dont edit admins registration
		if ( typeof( document.getElementsByName( 'edit_registration' )[0] ) === 'undefined' ) {
			focus_end( document.getElementById( 'login' ) );
		}
	}
}

function validate_add_admin( doc ) {

	if ( doc.login.value === '' ) {
		doc.login.select();
		return false;
	}

	if ( validate_pw( doc ) === false ) {
		return false;
	}
}

function validate_modify_admin( doc ) {

	if ( ! form_is_modified( doc ) ) {
		return false;
	}

	if ( doc.login.value === '' ) {
		doc.login.select();
		return false;
	}

	if ( doc.new_pw.value !== '' ) {
		return validate_pw( doc );
	}
}

function admin_edit_access_buttons( all, none, invert ) {

	input = '<input type="button" onClick="uncheck( \'full_access\' );'

	document.write( input+' check_all_chkbox( \'edit_admin_access\' );" value="'+all+'"> ' );

	document.write( input+' uncheck_all_chkbox( \'edit_admin_access\' );" value="'+none+'"> ' );

	document.write( input+' invert_all_chkbox( \'edit_admin_access\' );" value="'+invert+'">' );
}

function full_access_toggle( togglebox ) {

	if ( togglebox.checked === true ) {

		uncheck_all_chkbox( 'edit_admin_access' );

	} else {

		if ( document.getElementById( 'fa' ).defaultChecked === false ) {

			document.getElementById( 'js_admin_access' ).reset();
		}
	}
}

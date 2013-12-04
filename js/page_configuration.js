
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

window.onload = configuration_onload;

function configuration_onload() {

	if ( document.getElementById( 'current' ) ) {
		document.getElementById( 'current' ).focus();
	}

	if ( document.getElementById( 'add_profile' ) ) {
		document.getElementById( 'add_profile' ).focus();
	}
}


function validate_SuperAdmin( doc ) {

	if ( doc.current.value === '' ) {
		doc.current.select();
		return false;
	}

	if ( doc.login.value === '' ) {
		doc.login.select();
		return false;
	}

	if ( doc.login.value === doc.login.defaultValue
	&& doc.new_pw.value === doc.new_pw.defaultValue
	&& doc.confirm_new_pw.value === doc.confirm_new_pw.defaultValue ) {

		doc.style.outline = '4px solid #5d0000';
		window['restorevar'] = doc;
		setTimeout( 'restore()', 50 );
		return false;
	}

	if ( doc.new_pw.value !== '' ) {
		return validate_pw( doc );
	}
}

function validate_ice_profile( el ) {

	// Empty name is invalid
	if ( el.name.value === '' ) {
		el.name.select();
		return false;
	}

	// Empty host is invalid
	if ( el.host.value === '' ) {
		el.host.select();
		return false;
	}


	if ( ! form_is_modified( el ) ) {
		return false;
	}

	if ( el.host.value === '' ) {
		el.ihost.select();
		return false;
	}

	if ( ! check_port( el.port.value ) ) {
		pma_alert( TEXT.invalid_port, el.port );
		return false;
	}

	// Timeout
	if ( ! check_digital( el.timeout.value ) ) {
		pma_alert( TEXT.invalid_timeout, el.timeout );
		return false;
	}

	// 0 is an invalid value for ice timeout
	if ( el.timeout.value === '0' ) {
		pma_alert( TEXT.invalid_timeout, el.timeout );
		return false;
	}
}

function validate_add_informations_locales( doc ) {

	if ( doc.key.options[0].selected === true ) {
		doc.key.focus();
		return false;
	}

	if ( doc.val.value === '' ) {
		doc.val.select();
		return false;
	}
}

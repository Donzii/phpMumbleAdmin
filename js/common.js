
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

function check_ip( ip ) {

	if ( check_ipv4( ip ) ) {
		return true;

	} else if ( check_ipv6( ip ) ) {
		return true;
	}

	return false;
}

function check_ipv4( ip ) {

	regexp_ipv4 = new RegExp( /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/ );

	return regexp_ipv4.test( ip );
}

function check_ipv6( ip ) {

	regexp_ipv6 = new RegExp( /^((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))$/ );

	return regexp_ipv6.test( ip );
}

function check_port( port ) {

	if ( ! check_digital( port )  ) {
		return false;
	}

	if ( port <= 65535 ) {
		return true;
	}

	return false;
}

// Return true if >= 0, max 255 characters
function check_digital( str ) {

	regexp_digital =  new RegExp( /^[0-9]{1,255}$/ );

	return regexp_digital.test( str );
}

// Focus the end of an element ( input or textarea ).
function focus_end( el ) {

	el.focus();
	el.value = '';
	el.value = el.defaultValue;
}

// Read a cookie
function read_cookie( name ) {

	start = document.cookie.indexOf( name + '=' );

	if ( start >= 0 ) {

		start += name.length + 1;
		end = document.cookie.indexOf( ';', start );
		if ( end < 0 ) {
			end = document.cookie.length;
		}
		return unescape( document.cookie.substring( start, end ) );
	}
}

// Don't submit unchanged value, and dont allow empty value if not allowed
//
// Return false on error
function unchanged( doc, empty_not_allowed ) {

	// Select field
	if ( doc.tagName === 'SELECT' ) {

		if ( doc.options[0].selected === true ) {
			doc.focus();
			return false;
		}

	// Input, textarea
	} else if ( doc.value === doc.defaultValue ) {

		doc.select();
		return false;

	// Deny empty value if modified but not allowed
	} else if ( typeof( empty_not_allowed ) !== 'undefined' && empty_not_allowed === 'true' ) {

		if ( doc.value === '' ) {
			doc.select();
			return false;
		}
	}
}

// Dont submit unmodified form.
// Return bool
function form_is_modified( doc ) {

	array = doc.elements;

	for ( i = 0; i < array.length; i++ ) {

		switch ( array[i].type ) {

			case 'text':
			case 'password':
			case 'textarea':

				if ( array[i].value !== array[i].defaultValue ) {
					return true;
				}
				break;

			case 'checkbox':
			case 'radio':

				if ( array[i].checked !== array[i].defaultChecked ) {
					return true;
				}
				break;

			case 'select-one':
			case 'select-multiple':

				hasDefault = false;

				opt = array[i].options;

				for ( y = 0; y < opt.length; y++ ) {

					// DefaultSelect have been set
					if ( opt[y].defaultSelected ) {

						hasDefault = true;

						if ( ! opt[y].selected ) {
							return true;
						}
					}
				}

				// No defaultSelect found
				if ( hasDefault === false ) {

					if ( opt[0].selected === false ) {
						return true;
					}
				}

				break;
		}
	}

	doc.style.outline = '4px solid #5d0000';
	window['restorevar'] = doc;
	setTimeout( 'restore()', 50 );

	return false;
}

function restore() {
	restorevar.style.outline = '';
}

// Validate a password form:
//
// doc.current
// doc.new_pw
// doc.confirm_new_pw
//
// Return false on error
function validate_pw( doc ) {

	// If current password is required
	if ( typeof( doc.current ) !== 'undefined' ) {

		if ( doc.current.value === '' ) {
			doc.current.select();
			return false;
		}
	}

	if ( doc.new_pw.value === '' ) {
		doc.new_pw.select();
		return false;
	}

	if ( doc.confirm_new_pw.value == '' ) {
		doc.confirm_new_pw.select();
		return false;
	}

	// Check if new_pw & confirm_new_pw are equal
	if ( doc.new_pw.value !== doc.confirm_new_pw.value ) {

		doc.new_pw.value = '';
		doc.confirm_new_pw.value = '';
		pma_alert( TEXT.pw_check_failed, doc.new_pw );
		return false;
	}
}

// Uncheck a checkbox with it's name
function uncheck( name ) {
	document.getElementsByName( name )[0].checked = false;
}

// Reset a select field by it's id key
function unselect( id ) {
	document.getElementById( id ).options[0].selected = true;
}

// Parse all options of a select field to find "defaultSelected" and select it.
function select_default( id ) {

	options = document.getElementById( id ).options;

	len = options.length;

	for ( i = 0; i < len; ++i ) {

		// DefaultSelect have been set
		if ( options[ i ].defaultSelected ) {

			options[ i ].selected = true;
			break;
		}
	}
}

function check_all_chkbox( id ) {

	array = document.getElementById( id ).getElementsByTagName( 'input' );

	len = array.length;

	for ( i = 0; i < len; ++i ) {
		array[i].checked = true;
	}
}

function uncheck_all_chkbox( id ) {

	array = document.getElementById( id ).getElementsByTagName( 'input' );

	len = array.length;

	for ( i = 0; i < len; ++i ) {
		array[i].checked = false;
	}
}

function invert_all_chkbox( id ) {

	array = document.getElementById( id ).getElementsByTagName( 'input' );

	len = array.length;

	for ( i = 0; i < len; ++i ) {
		array[i].checked = ! array[i].checked;
	}
}


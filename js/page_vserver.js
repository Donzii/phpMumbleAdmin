
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

window.onload = vserver_onload;

function vserver_onload( e ) {

	if ( document.getElementById( 'current' ) ) {
		document.getElementById( 'current' ).focus();

	} else if ( document.getElementById( 'new_pw' ) ) {
		document.getElementById( 'new_pw' ).focus();
	}

	if ( document.getElementById( 'change_login' ) ) {
		focus_end( document.getElementById( 'change_login' ) );
	}

	if ( document.getElementById( 'ip' ) ) {
		document.getElementById( 'ip' ).focus();
	}

	// Expand menu
	check_expand = document.getElementsByClassName( 'expand' );

	if ( typeof( check_expand[0] ) !== 'undefined' ) {
		expand( e, check_expand[0] );
	}
}

function toggle_infopanel() {

	cookie_name = 'phpMumbleAdmin_conf';

	conf_cookie = read_cookie( cookie_name );

	expdate = new Date();
	// milliseconde require
	expdate.setTime( expdate.getTime() + ( 180*24*60*60*1000 ) );

	doc = document.getElementById( 'info_panel' );

	// If info panel is disactived, return true to follow the http link.
	if ( doc === null ) {
		return true;
	}

	// Toggle cookie 'infoPanel' parameter
	if ( conf_cookie.search( "(\"infoPanel\";b:1;)" ) != -1 ) {
		var reg = new RegExp( "(\"infoPanel\";b:1;)", "g" );
		var replaceBool = "\"infoPanel\";b:0;";
	} else {
		var reg = new RegExp( "(\"infoPanel\";b:0;)", "g" );
		var replaceBool = "\"infoPanel\";b:1;";
	}

	// toggle the panel
	if ( doc.style.display == 'none' ) {
		doc.style.display = 'block';
	} else {
		doc.style.display = 'none';
	}

	// toggle img
	img = document.getElementById( 'js_infopanel' );
	if ( img !== null ) {
		src1 = 'images/tango/delete2_16.png';
		src2 = 'images/tango/add2_16.png';
		if ( doc.style.display == 'none' ) {
			img.src = src2;
		} else {
			img.src = src1;
		}
	}

	// Write the cookie
	new_conf_cookie = conf_cookie.replace( reg, replaceBool );
	document.cookie = cookie_name +'='+ escape( new_conf_cookie ) +'; expires='+ expdate.toGMTString() +'; path=/;';

	// Do not follow the http link.
	return false;
}

function validate_settings( doc ) {

	if ( ! form_is_modified( doc ) ) {
		return false;
	}

	// PORT
	if ( typeof( doc.port ) !== 'undefined' && doc.port.value !== '' ) {
		if ( ! check_port( doc.port.value ) ) {
			pma_alert( TEXT.invalid_port, doc.port );
			return false;
		}
	}

	// TIMEOUT
	if ( typeof( doc.timeout ) !== 'undefined' && doc.timeout.value !== '' ) {
		if ( ! check_digital( doc.timeout.value ) ) {
			pma_alert( TEXT.invalid_number.replace( '%s', 'timeout' ), doc.timeout );
			return false;
		}
		if ( doc.timeout.value < 0 ) {
			pma_alert( TEXT.invalid_number.replace( '%s', 'timeout' ), doc.timeout );
			return false;
		}
	}

	// BANDWIDTH
	if ( typeof( doc.bandwidth ) !== 'undefined' && doc.bandwidth.value !== '' ) {
		if ( ! check_digital( doc.bandwidth.value ) ) {
			pma_alert( TEXT.invalid_number.replace( '%s', 'bandwitch' ), doc.bandwidth );
			return false;
		}
	}

	// USERS
	if ( typeof( doc.users ) !== 'undefined' && doc.users.value !== '' ) {
		if ( ! check_digital( doc.users.value ) ) {
			pma_alert( TEXT.invalid_number.replace( '%s', 'users' ), doc.users );
			return false;
		}
	}

	// DEFAULT CHANNEL
	if ( typeof( doc.defaultchannel ) !== 'undefined' && doc.defaultchannel.value !== '' ) {
		if ( ! check_digital( doc.defaultchannel.value ) ) {
			pma_alert( TEXT.invalid_number.replace( '%s', 'defaultchannel' ), doc.defaultchannel );
			return false;
		}
	}

	// USERS PER CHANNEL
	if ( typeof( doc.usersperchannel ) !== 'undefined' && doc.usersperchannel.value !== '' ) {
		if ( ! check_digital( doc.usersperchannel.value ) ) {
			pma_alert( TEXT.invalid_number.replace( '%s', 'userperchannel' ), doc.usersperchannel );
			return false;
		}
	}

	// TEXT MSG LENGTH
	if ( typeof( doc.textmessagelength ) !== 'undefined' && doc.textmessagelength.value !== '' ) {
		if ( ! check_digital( doc.textmessagelength.value ) ) {
			pma_alert( TEXT.invalid_number.replace( '%s', 'textmessagelength' ), doc.textmessagelength );
			return false;
		}
	}

	// IMAGE MSG LENGTH
	if ( typeof( doc.imagemessagelength ) !== 'undefined' && doc.imagemessagelength.value !== '' ) {
		if ( ! check_digital( doc.imagemessagelength.value ) ) {
			pma_alert( TEXT.invalid_number.replace( '%s', 'imagemessagelength' ), doc.imagemessagelength );
			return false;
		}
	}
}

function validate_ban( doc ) {

	// Check for a valid IP & mask:
	ip_is_valid = false;
	mask_is_valid = false;

	if ( doc.ip.value === '' ) {
		doc.ip.focus();
		return false;
	}

	// ipv4
	if ( check_ipv4( doc.ip.value ) ) {

		ip_is_valid = true;

		// Range 1-32
		if ( doc.mask.value.search( /^[1-9]$|^[1-2][0-9]$|^3[0-2]$/ ) != -1 ) {
			mask_is_valid = true;
		}
	}

	// ipv6
	if ( check_ipv6( doc.ip.value ) ) {

		ip_is_valid = true;

		// Range 1-128
		if ( doc.mask.value.search( /^[1-9]$|^[1-9][0-9]$|^1[0-1][0-9]$|^12[0-8]$/ ) != -1 ) {
			mask_is_valid = true;
		}
	}

	// Empty mask is valid too.
	if ( doc.mask.value === '' ) {
		mask_is_valid = true;
	}

	if ( ip_is_valid === true ) {

		if ( mask_is_valid === false ) {
			pma_alert( TEXT.invalid_mask, doc.mask );
			return false;
		}
	} else {
		pma_alert( TEXT.invalid_ip, doc.ip );
		return false;
	}

	// IP & mask are valid, check if the form have been modified:
	return form_is_modified( doc );
}

// Special function for toggle all select field of the ban duration.
function ban_duration( togglebox ) {

	if ( togglebox.checked ) {

		unselect( 'hour' );
		unselect( 'day' );
		unselect( 'month' );
		unselect( 'year' );

	} else {

		select_default( 'hour' );
		select_default( 'day' );
		select_default( 'month' );
		select_default( 'year' );
	}
}



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

// ActionBox objects:
function add_ice_profile() {

	obj = new Object();
	obj.type = 'send';
	obj.onSubmit = 'unchanged';
	obj.cmd = 'config_ICE';
	obj.h1 = TEXT.add_ice_profile;
	obj.key = 'add_profile';
	obj.value = '';
	obj.txt_submit = TEXT.add;

	return actionBox( obj );
}

function del_ice_profile( name ) {

	obj = new Object();
	obj.type = 'confirm';
	obj.cmd = 'config_ICE';
	obj.hidden_key = 'delete_profile';
	obj.hidden_value = '';
	obj.name = name;
	obj.txt = TEXT.del_ice_profile;

	return actionBox( obj );
}

function del_admin( el, id, name ) {

	obj = new Object();
	obj.el = el;
	obj.type = 'confirm';
	obj.cmd = 'config_admins';
	obj.hidden_key = 'remove_admin';
	obj.hidden_value = id;
	obj.name = name;
	obj.txt = TEXT.del_admin;
	obj.highLight = new Array();
	obj.highLight[0] = 1;
	obj.highLight[1] = 2;

	return actionBox( obj );
}

function add_vserver() {

	obj = new Object();
	obj.type = 'send';
	obj.cmd = 'overview';
	obj.hidden_key = 'add_vserver';
	obj.chkbox_key = 'new_su_pw';
	obj.h1 = TEXT.add_vserver;
	obj.chkbox_txt = TEXT.gen_su_pw;
	obj.txt_submit = TEXT.add;

	return actionBox( obj );
}

function send_msg_all_vservers() {

	obj = new Object();
	obj.type = 'textarea';
	obj.cmd = 'overview';
	obj.onSubmit = 'unchanged';
	obj.h1 = TEXT.send_msg_all_vservers;
	obj.key = 'send_msg_vservers';
	obj.textarea = '';
	obj.txt_submit = TEXT.submit;

	return actionBox( obj );
}

function del_vserver( el, id, name ) {

	obj = new Object();
	obj.type = 'confirm';
	obj.cmd = 'overview';
	obj.hidden_key = 'delete_vserver_id';
	obj.hidden_value = id;
	obj.el = el;
	obj.name = name;
	obj.highLight = new Array();
	obj.highLight[0] = 1;
	obj.highLight[1] = 2;
	obj.txt_replace = true;
	obj.txt = TEXT.del_vserver;

	return actionBox( obj );
}

function reset_vserver( el, id, name ) {

	obj = new Object();
	obj.type = 'confirm';
	obj.cmd = 'overview';
	obj.el = el;
	obj.name = name;
	obj.hidden_key = 'reset_vserver_id';
	obj.hidden_value = id;
	obj.highLight = new Array();
	obj.highLight[0] = 1;
	obj.highLight[1] = 2;
	obj.txt = TEXT.reset_vserver;
	obj.txt_replace = true;
	obj.chkbox_key = 'new_su_pw';
	obj.chkbox_checked = true;
	obj.chkbox_txt = TEXT.gen_su_pw;

	return actionBox( obj );
}

function add_sub_channel() {

	obj = new Object();
	obj.type = 'send';
	obj.cmd = 'murmur_channel';
	obj.onSubmit = 'unchanged';
	obj.key = 'add_sub_channel';
	obj.value = '';
	obj.h1 = TEXT.add_sub_channel;
	obj.txt_submit = TEXT.add;

	return actionBox( obj );
}

function send_channel_msg() {

	obj = new Object();
	obj.type = 'textarea';
	obj.cmd = 'murmur_channel';
	obj.onSubmit = 'unchanged';
	obj.key = 'send_msg';
	obj.h1 = TEXT.send_msg;
	obj.textarea = '';
	obj.chkbox_key = 'to_all_sub';
	obj.chkbox_txt = TEXT.to_all_sub_channels;
	obj.txt_submit = TEXT.submit;

	return actionBox( obj );
}

function del_channel() {

	obj = new Object();
	obj.type = 'confirm';
	obj.cmd = 'murmur_channel';
	obj.hidden_key = 'delete_channel';
	obj.value = '';
	obj.txt = TEXT.del_channel;

	return actionBox( obj );
}

function kick_user() {

	obj = new Object();
	obj.type = 'send';
	obj.cmd = 'murmur_users_sessions';
	obj.h1 = TEXT.kick_user;
	obj.key = 'kick';
	obj.value = '';
	obj.txt_submit = TEXT.kick_user;

	return actionBox( obj );
}

function change_user_session_name() {

	obj = new Object();
	obj.type = 'send';
	obj.cmd = 'murmur_users_sessions';
	obj.key = 'change_user_session_name';
	obj.value = '';
	obj.h1 = TEXT.change_user_session_name;
	obj.txt_submit = TEXT.modify;

	return actionBox( obj );
}

function send_user_msg() {

	obj = new Object();
	obj.type = 'textarea';
	obj.cmd = 'murmur_users_sessions';
	obj.onSubmit = 'unchanged';
	obj.h1 = TEXT.send_msg;
	obj.key = 'send_msg';
	obj.textarea = '';
	obj.txt_submit = TEXT.submit;

	return actionBox( obj );
}

function add_group() {

	obj = new Object();
	obj.type = 'send';
	obj.cmd = 'murmur_groups';
	obj.onSubmit = 'unchanged';
	obj.h1 = TEXT.add_group;
	obj.key = 'add_group';
	obj.value = '';
	obj.txt_submit = TEXT.add;

	return actionBox( obj );
}

function reset_certificate() {

	obj = new Object();
	obj.type = 'confirm';
	obj.cmd = 'murmur_settings';
	obj.hidden_key = 'reset_setting';
	obj.hidden_value = 'certificate';
	obj.txt = TEXT.confirm_del_certificate;

	return actionBox( obj );
}

function add_account() {

	obj = new Object();
	obj.type = 'send';
	obj.onSubmit = 'unchanged';
	obj.cmd = 'murmur_registrations';
	obj.h1 = TEXT.add_acc;
	obj.key = 'add_new_account';
	obj.value = '';
	obj.chkbox_key = 'redirect_to_new_account';
	obj.chkbox_txt = TEXT.redirect_to_new_acc;
	obj.txt_submit = TEXT.add;

	return actionBox( obj );
}

function del_account_id( el, id, name ) {

	obj = new Object();
	obj.type = 'confirm';
	obj.cmd = 'murmur_registrations';
	obj.el = el;
	obj.hidden_key = 'delete_account_id';
	obj.hidden_value = id;
	obj.name = name;
	obj.highLight = new Array();
	obj.highLight[0] = 1;
	obj.highLight[1] = 2;
	obj.txt = TEXT.confirm_del_acc;

	return actionBox( obj );
}

function change_login( login ) {

	obj = new Object();
	obj.type = 'send';
	obj.onSubmit = 'unchanged_unempty';
	obj.cmd = 'murmur_registrations';
	obj.h1 = TEXT.modify_login;
	obj.key = 'change_login';
	obj.value = login;
	obj.txt_submit = TEXT.modify;

	return actionBox( obj );
}

function change_email( email ) {

	obj = new Object();
	obj.type = 'send';
	obj.onSubmit = 'unchanged';
	obj.cmd = 'murmur_registrations';
	obj.h1 = TEXT.modify_email;
	obj.key = 'change_email';
	obj.value = email;
	obj.txt_submit = TEXT.modify;

	return actionBox( obj );
}

function change_comment( textarea ) {

	obj = new Object();
	obj.type = 'textarea';
	obj.onSubmit = 'unchanged';
	obj.cmd = 'murmur_registrations';
	obj.h1 = TEXT.modify_comm;
	obj.key = 'change_desc';
	obj.textarea = textarea;
	obj.txt_submit = TEXT.modify;

	return actionBox( obj );
}

function del_account_sess( name ) {

	obj = new Object();
	obj.type = 'confirm';
	obj.cmd = 'murmur_registrations';
	obj.name = name;
	obj.hidden_key = 'delete_account';
	obj.hidden_value = '';
	obj.txt = TEXT.confirm_del_acc;

	return actionBox( obj );
}

function del_avatar() {

	obj = new Object();
	obj.type = 'confirm';
	obj.cmd = 'murmur_registrations';
	obj.hidden_key = 'remove_avatar';
	obj.hidden_value = '';
	obj.txt = TEXT.confirm_del_avatar;

	return actionBox( obj );
}

function del_ban( el, id ) {

	obj = new Object();
	obj.type = 'confirm';
	obj.onSubmit = '';
	obj.cmd = 'murmur_bans';
	obj.el = el;
	obj.hidden_key = 'delete_ban_id';
	obj.hidden_value = id;
	obj.txt = TEXT.del_ban;
	obj.highLight = new Array();
	obj.highLight[0] = 0;

	return actionBox( obj );
}

// Functions:
function actionBox( obj ) {

	js_background();

	create_form( obj );

	// onSubmit
	if ( typeof( obj.onSubmit ) !== 'undefined' ) {

		if ( obj.onSubmit == 'unchanged' ) {

			form['onsubmit'] = function() {
				return unchanged( input );
			};

		} else if ( obj.onSubmit == 'unchanged_unempty' ) {

			form['onsubmit'] = function() {
				return unchanged( input, 'true' );
			};
		}
	}

	// Text replacement
	if ( typeof( obj.txt_replace ) !== 'undefined' ) {
		obj.text = obj.txt.replace( '%d', obj.hidden_value );
	} else {
		obj.text = obj.txt;
	}

	switch ( obj.type ) {

		case 'send':
		case 'textarea':

			cancel_button( obj );
			create_h1( obj );
			create_pad();

			if ( typeof ( obj.hidden_key ) !== 'undefined' ) {
				create_input_hidden( obj.hidden_key, obj.hidden_value );
			}

			if ( typeof ( obj.key ) !== 'undefined' ) {

				if ( obj.type === 'send' ) {
					create_input_text( obj );

				} else if ( obj.type === 'textarea' ) {
					create_textarea( obj );
				}
			}

			if ( typeof ( obj.chkbox_key ) !== 'undefined' ) {
				create_checkbox( obj );
			}

			create_pad();
			create_submit( obj );
			create_pad();
			break;

		case 'confirm':

			form.className += ' alert';

			create_input_hidden( obj.hidden_key, obj.hidden_value );

			if ( typeof ( obj.highLight ) !== 'undefined' ) {
				highLight_td( obj );
			}

			// drag img
			move = form.appendChild( d.createElement( 'img' ) );
			move.className = 'drag';
			move.src = 'images/pma/space.png';

			// drag
			dragobj = form;
			move['onmousedown'] = grab;

			// NAME div
			if ( typeof( obj.name ) !== 'undefined' && obj.name !== '' ) {

				pName = form.appendChild( d.createElement( 'div' ) );
				pName.className = 'name';
				pName.innerHTML = obj.name;
			}

			// Text div
			title = form.appendChild( d.createElement( 'div' ) );
			title.className = 'pad';
			title.innerHTML = obj.text;
			create_pad();

			if ( typeof ( obj.chkbox_key ) !== 'undefined' ) {

				create_checkbox( obj );
				create_pad();
			}

			create_confirm_submit( obj );
			create_pad();
			break;
	}

	// Return false to disactivate HTML actions.
	return false;
}

// Add a smoked background
function js_background() {

	d = document;

	// Allow only one instance of js_background
	if ( d.getElementById( 'js_background' ) ) {
		return;
	}

	jsBoxBackground = d.getElementsByTagName( 'body' )[0].appendChild( d.createElement( 'div' ) );
	jsBoxBackground.id = 'js_background';

	// Feet jsBoxBackground to the user browser scroll size
	jsBoxBackground.style.height = d.documentElement.scrollHeight + 'px';
	jsBoxBackground.style.width = '100%';

}

function create_form( obj ) {

	form = jsBoxBackground.appendChild( d.createElement( 'form' ) );

	form.className = 'js actionBox';
	form.method = 'post';
	form.action = '';

	// MSIE doesnt treat position:fixed correctly, so this compensates for positioning the alert
	if ( d.all && ! window.opera ) {
		form.style.top = d.documentElement.scrollTop + 'px';
	}

	// center the box
	form.style.left = ( d.documentElement.scrollWidth - form.offsetWidth )/2 + 'px';

	create_input_hidden( 'cmd', obj.cmd );
}

// hightLight current td element
function highLight_td( obj ) {

	len = obj.highLight.length;

	for ( i = 0; i < len; ++i ) {
		obj.el.parentNode.parentNode.getElementsByTagName( 'td' )[ obj.highLight[i] ].style.background = 'red';
	}
}

function remove_highLight_td( obj ) {

	len = obj.highLight.length;

	for ( i = 0; i < len; ++i ) {
		obj.el.parentNode.parentNode.getElementsByTagName( 'td' )[ obj.highLight[i] ].style.background = '';
	}
}

function cancel_button() {

	cancel = form.appendChild( d.createElement( 'img' ) );
	cancel.className = 'button back';
	cancel.src = 'images/gei/cancel_16.png';
	cancel.title = TEXT.cancel;

	cancel.addEventListener( 'click', function() { remove_element( jsBoxBackground ); }, false );
}

// Remove a div with it's element
function remove_element( doc ) {
	doc.parentNode.removeChild( doc );
}

function create_h1( obj ) {

	h1 = form.appendChild( d.createElement( 'h1' ) );

	label = h1.appendChild( d.createElement( 'label' ) );
	label.htmlFor = obj.key;
	label.innerHTML = obj.h1;

	// drag
	dragobj = form;
	label['onmousedown'] = grab;
}

function create_pad() {
	div = form.appendChild( d.createElement( 'div' ) );
	div.className = 'pad';
}

function create_input_hidden( key, value ) {
	input = form.appendChild( d.createElement( 'input' ) );
	input.type = 'hidden';
	input.name = key;
	input.value = value;
}

function create_input_text( obj ) {
	input = form.appendChild( d.createElement( 'input' ) );
	input.type = 'text';
	input.id = obj.key;
	input.name = obj.key;
	input.defaultValue = obj.value;
	focus_end( input, obj.value );
}

function create_textarea( obj ) {

	textarea = form.appendChild( d.createElement( 'textarea' ) );
	textarea.id = obj.key;
	textarea.name = obj.key;
	textarea.defaultValue = obj.textarea;
	textarea.rows = '10';
	focus_end( textarea, obj.textarea );

	// Workaround for unchanged()
	input = textarea;
}

function create_checkbox( obj ) {

	div = form.appendChild( d.createElement( 'div' ) );
	div.className = 'pad';

	label = div.appendChild( d.createElement( 'label' ) );
	label.htmlFor = obj.chkbox_key;
	label.innerHTML = obj.chkbox_txt;

	checkbox = div.appendChild( d.createElement( 'input' ) );
	checkbox.type = 'checkbox';
	checkbox.id = obj.chkbox_key;
	checkbox.name = obj.chkbox_key;

	if ( typeof( obj.chkbox_checked ) !== 'undefined' ) {
		checkbox.checked = 'checked';
	}
}

function create_submit( obj ) {

	submit = form.appendChild( d.createElement( 'input' ) );
	submit.type = 'submit';
	submit.value = obj.txt_submit;
}

// Yes / No submit button
function create_confirm_submit( obj ) {

	submit = form.appendChild( d.createElement( 'input' ) );
	submit.type = 'submit';
	submit.name = 'confirmed';
	submit.style.marginRight = '10px';
	submit.value = TEXT.yes;
	// Cancel
	cancel = form.appendChild( d.createElement( 'input' ) );
	cancel.type = 'button';
	cancel.value = TEXT.no;
	cancel.style.fontWeight = 'bold';
	cancel.focus();
	if ( typeof ( obj.highLight ) !== 'undefined' ) {
		cancel.addEventListener( 'click', function() { remove_highLight_td( obj ); remove_element( jsBoxBackground ); }, false );
	} else {
		cancel.addEventListener( 'click', function() { remove_element( jsBoxBackground ); }, false );
	}
}

// Fancy alert() function
function pma_alert( txt, doc ) {

	js_background();

	box = jsBoxBackground.appendChild( d.createElement( 'div' ) );
	box.className = 'actionBox js alert';

	// MSIE doesnt treat position:fixed correctly, so this compensates for positioning the alert
	if ( d.all && !window.opera ) {
		box.style.top = d.documentElement.scrollTop + 'px';
	}
	// center the alert box
	box.style.left = ( d.documentElement.scrollWidth - box.offsetWidth )/2 + 'px';

	// drag img
	move = box.appendChild( d.createElement( 'img' ) );
	move.className = 'drag';
	move.src = 'images/pma/space.png';
	// drag
	dragobj = box;
	move['onmousedown'] = grab;

	title = box.appendChild( d.createElement( 'div' ) );
	title.className = 'pad';
	title.innerHTML = txt;

	div = box.appendChild( d.createElement( 'div' ) );
	div.className = 'pad';

	ok = div.appendChild( d.createElement( 'input' ) );
	ok.type = 'button';
	ok.value = TEXT.ok;
	ok.style.fontWeight = 'bold';
	ok.addEventListener( 'click', function() { remove_element( jsBoxBackground ); doc.select(); }, false );
	ok.focus();
}
